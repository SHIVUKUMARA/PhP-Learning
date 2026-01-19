<?php defined('BASEPATH') or exit('No direct script access allowed');

class Omni_products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'upload']);
        $this->load->database();

        // Redirect if not logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        // Load models
        $this->load->model('Omni_product_model', 'omni');
        $this->load->model('User_model');

        // Make logged-in user info available in all views
        $logged_user = $this->User_model->get_user_by_id($this->session->userdata('user_id'));
        $this->load->vars(['logged_user' => $logged_user]);
    }

    private function is_admin()
    {
        return $this->session->userdata('role') === 'admin';
    }

    /* ======================
       PAGE LOAD
    ====================== */
    public function index()
    {
        $data['products'] = $this->omni->get_all();
        $this->load->view('omni_products/index', $data);
    }

    public function create()
    {
        $data['title'] = "Add Omni Product | AdminLTE";
        $this->load->view('omni_products/omni_add', $data);
    }
    public function store()
    {
        $this->_rules();

        if ($this->form_validation->run() === FALSE) {
            return $this->_json(false, validation_errors());
        }

        $data = $this->_payload();
        $id = $this->omni->insert($data);

        return $this->_json(true, 'Product created', ['id' => $id]);
    }

    // Get Product by ID
    public function get_product($id)
    {
        $product = $this->omni->get_by_id($id);
        if (!$product) {
            return $this->_json(false, 'Product not found');
        }

        return $this->_json(true, 'OK', $product);
    }

    public function edit($id)
    {
        $product = $this->omni->get_by_id($id);
        if (!$product) {
            show_404();
        }

        $data['title'] = "Edit Omni Product | AdminLTE";
        $data['product'] = $product;

        $this->load->view('omni_products/omni_add', $data);
    }
    // Update Product
    // public function update($id)
    // {
    //     if (!$this->omni->get_by_id($id)) {
    //         return $this->_json(false, 'Product not found', [
    //             'csrf' => [
    //                 'name' => $this->security->get_csrf_token_name(),
    //                 'hash' => $this->security->get_csrf_hash()
    //             ]
    //         ]);
    //     }

    //     $this->_rules(false);

    //     if ($this->form_validation->run() === FALSE) {
    //         return $this->_json(false, validation_errors(), [
    //             'csrf' => [
    //                 'name' => $this->security->get_csrf_token_name(),
    //                 'hash' => $this->security->get_csrf_hash()
    //             ]
    //         ]);
    //     }

    //     $this->omni->update($id, $this->_payload());

    //     return $this->_json(true, 'Product updated', [
    //         'csrf' => [
    //             'name' => $this->security->get_csrf_token_name(),
    //             'hash' => $this->security->get_csrf_hash()
    //         ]
    //     ]);
    // }
    public function update($id)
    {
        $product = $this->omni->get_by_id($id);
        if (!$product) {
            return $this->_json(false, 'Product not found');
        }

        $this->_rules(false);
        if ($this->form_validation->run() === FALSE) {
            return $this->_json(false, validation_errors());
        }

        // Prepare payload
        $data = $this->_payload();
        $this->omni->update($id, $data);

        // Sync to all published platforms
        $syncResults = $this->_sync_to_platforms($id, $data);

        return $this->_json(true, 'Product updated and synced', [
            'sync' => $syncResults
        ]);
    }

    private function _sync_to_platforms($productId, $payload)
    {
        $product = $this->omni->get_by_id($productId);
        if (!$product) return [];

        $platformIds = json_decode($product->published_ids, true) ?: [];
        if (empty($platformIds)) return [];

        $urls = [
            'CrudCrud' => 'https://crudcrud.com/api/2350da2f06394fc792347ab832c93d08/products',
            'Mock API' => 'https://6968d5a969178471522bae41.mockapi.io/api/products',
            'Beeceptor' => 'https://ca88fe7b23e3b6cf0603.free.beeceptor.com/api/products',
            'Supabase' => 'https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products'
        ];

        $supabaseKey = 'sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2';
        $results = [];

        foreach ($platformIds as $platform => $externalId) {

            if (!isset($urls[$platform])) continue;

            $headers = ['Content-Type: application/json'];

            if ($platform === 'Supabase') {
                $url = $urls[$platform] . "?id=eq.{$externalId}";
                $headers = [
                    'Content-Type: application/json',
                    'apikey: ' . $supabaseKey,
                    'Authorization: Bearer ' . $supabaseKey
                ];
            } else {
                $url = $urls[$platform] . "/{$externalId}";
            }

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_CUSTOMREQUEST  => 'PATCH',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_TIMEOUT        => 30
            ]);

            $response = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            $results[$platform] = [
                'success' => !$error && in_array($code, [200, 204]),
                'httpCode' => $code,
                'error' => $error
            ];
        }

        return $results;
    }


    // Delete Product
    // public function delete($id)
    // {
    //     if (!$this->omni->get_by_id($id)) {
    //         return $this->_json(false, 'Product not found');
    //     }

    //     $this->omni->delete($id);
    //     return $this->_json(true, 'Product deleted');
    // }
    public function delete($id)
    {
        $product = $this->omni->get_by_id($id);
        if (!$product) {
            return $this->_json(false, 'Product not found');
        }

        // Delete from all published platforms first
        $syncResults = $this->_sync_to_platforms($id, [], 'DELETE');

        // Then delete locally
        $this->omni->delete($id);

        return $this->_json(true, 'Product deleted from all platforms', [
            'sync' => $syncResults
        ]);
    }

    // Search Products
    public function search()
    {
        $q = trim($this->input->get('q'));
        if ($q === '') return $this->_json(false, 'Empty search');

        return $this->_json(true, 'OK', $this->omni->search($q));
    }

    // Validation rules
    private function _rules($create = true)
    {
        $this->form_validation->set_rules('product_name', 'Product Name', 'required');
        $this->form_validation->set_rules(
            'sku',
            'SKU',
            $create ? 'required|is_unique[omni_products.sku]' : 'required'
        );
        $this->form_validation->set_rules('category_name', 'Category', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');
        $this->form_validation->set_rules('stock', 'Stock', 'integer');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('main_image_url', 'Main Image', 'required|valid_url');
    }


    private function _payload()
    {
        $extraImages = $this->input->post('extra_image_urls');

        // normalize extra images → ALWAYS JSON ARRAY
        if (is_array($extraImages)) {
            $extraImages = array_values(array_filter(array_map('trim', $extraImages)));
        } else {
            $extraImages = array_filter(array_map('trim', explode(',', (string) $extraImages)));
        }

        return [
            'product_name' => $this->input->post('product_name', true),
            'sku' => $this->input->post('sku', true),
            'brand' => $this->input->post('brand', true),
            'category_name' => $this->input->post('category_name', true),
            'category_code' => $this->input->post('category_code', true),
            'price' => $this->input->post('price', true),
            'sale_price' => $this->input->post('sale_price', true),
            'currency' => $this->input->post('currency', true) ?: 'INR',
            'stock' => $this->input->post('stock', true),
            'weight' => $this->input->post('weight', true),
            'length' => $this->input->post('length', true),
            'width' => $this->input->post('width', true),
            'height' => $this->input->post('height', true),
            'short_description' => $this->input->post('short_description', true),
            'description' => $this->input->post('description', true),
            'main_image_url' => $this->input->post('main_image_url', true),
            'extra_image_urls' => json_encode($extraImages, JSON_UNESCAPED_SLASHES),
            'condition_type' => $this->input->post('condition_type', true),
            'variant_attributes' => $this->input->post('variant_attributes'),
            'gtin' => $this->input->post('gtin', true),
            'mpn' => $this->input->post('mpn', true),
            'video_url' => $this->input->post('video_url', true),
            'status' => 'DRAFT'
        ];
    }

    private function _json($ok, $message, $data = [])
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'ok' => $ok,
                'message' => $message,
                'data' => $data
            ]));
    }
}
