<?php defined('BASEPATH') or exit('No direct script access allowed');

class Omni_publish extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper(['url', 'form']);
        $this->load->library(['session']);
        $this->load->database();

        // Auth check
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('Omni_product_model', 'omni');
        $this->load->model('User_model');

        // Share logged-in user
        $logged_user = $this->User_model->get_user_by_id(
            $this->session->userdata('user_id')
        );
        $this->load->vars(['logged_user' => $logged_user]);
    }


    public function index()
    {
        $data['title'] = 'Publish Omni Products | AdminLTE';
        $data['products'] = $this->omni->get_all();

        $this->load->view('omni_products/omni_publish', $data);
    }

    public function publish()
    {
        $productId = $this->input->post('product_id', true);
        $social    = $this->input->post('social_platforms') ?? [];

        if (!$productId) {
            return $this->_json(false, 'Product is required');
        }

        $product = $this->omni->get_by_id($productId);
        if (!$product) {
            return $this->_json(false, 'Invalid product');
        }

        if (in_array('Facebook', $social, true) && (int)$product->facebook_publish === 1) {
            return $this->_json(false, 'Product is already published to Facebook');
        }

        if (empty($social)) {
            return $this->_json(false, 'Select at least one platform');
        }

        // Platform URLs
        $urls = [
            'CrudCrud' => 'https://crudcrud.com/api/2350da2f06394fc792347ab832c93d08/products',
            'Mock API' => 'https://6968d5a969178471522bae41.mockapi.io/api/products',
            'Beeceptor' => 'https://ca88fe7b23e3b6cf0603.free.beeceptor.com/api/products',
            'Supabase' => 'https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products'
        ];

        $supabaseKey = 'sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2';

        // Payload (shared)
        $payload = [
            'product_name'       => $product->product_name,
            'sku'                => $product->sku,
            'brand'              => $product->brand,
            'category_name'      => $product->category_name,
            'category_code'      => $product->category_code,
            'price'              => (float) $product->price,
            'sale_price'         => (float) $product->sale_price,
            'currency'           => $product->currency ?: 'INR',
            'stock'              => (int) $product->stock,
            'weight'             => $product->weight,
            'length'             => $product->length,
            'width'              => $product->width,
            'height'             => $product->height,
            'short_description'  => $product->short_description,
            'description'        => $product->description,
            'main_image_url'     => $product->main_image_url,
            'extra_image_urls'   => json_decode($product->extra_image_urls, true),
            'condition_type'     => $product->condition_type,
            'variant_attributes' => json_decode($product->variant_attributes, true),
            'gtin'               => $product->gtin,
            'mpn'                => $product->mpn,
            'video_url'          => $product->video_url,
            'status'             => $product->status
        ];

        $responses = [];
        $publishedIds = json_decode($product->published_ids, true) ?: [];

        foreach ($social as $platform) {

            // Facebook is feed-based, not API-based
            if ($platform === 'Facebook') {
                $responses['Facebook'] = [
                    'success' => true,
                    'mode'    => 'feed',
                    'message' => 'Product will be included in Facebook catalog feed'
                ];
                continue;
            }

            if (!isset($urls[$platform])) {
                continue;
            }

            $headers = ['Content-Type: application/json'];
            $postPayload = $payload;

            if ($platform === 'Supabase') {
                $headers = [
                    'Content-Type: application/json',
                    'apikey: ' . $supabaseKey,
                    'Authorization: Bearer ' . $supabaseKey,
                    'Prefer: return=representation'
                ];
            } else {
                $postPayload['published_at'] = date('c');
                $postPayload['published_from'] = 'omni_panel';
            }

            $ch = curl_init($urls[$platform]);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_POSTFIELDS     => json_encode($postPayload),
                CURLOPT_TIMEOUT        => 30
            ]);

            $response = curl_exec($ch);
            $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error || !in_array($code, [200, 201], true)) {
                $responses[$platform] = [
                    'success' => false,
                    'error'   => $error ?: "HTTP {$code}"
                ];
                continue;
            }

            $decoded = json_decode($response, true);

            // Extract external ID
            $externalId =
                $decoded['_id'] ??
                $decoded['id'] ??
                ($decoded[0]['id'] ?? null);

            if ($externalId) {
                $publishedIds[$platform] = $externalId;
            }

            $responses[$platform] = [
                'success' => true,
                'external_id' => $externalId
            ];
        }

        // Save platform IDs (CRITICAL)
        // if (!empty($publishedIds)) {
        //     $this->omni->update($productId, [
        //         'published_ids' => json_encode($publishedIds)
        //     ]);
        // }
        // Save platform IDs (CRITICAL)
        $updateData = [];

        if (!empty($publishedIds)) {
            $updateData['published_ids'] = json_encode($publishedIds);
        }

        // If Facebook selected → enable feed publishing
        if (in_array('Facebook', $social, true)) {
            $updateData['facebook_publish'] = 1;
        }

        if (!empty($updateData)) {
            $this->omni->update($productId, $updateData);
        }

        return $this->_json(true, 'Product published successfully', $responses);
    }

    public function unpublish_facebook($productId)
    {
        if (!$productId) {
            return $this->_json(false, 'Missing product ID');
        }

        $product = $this->omni->get_by_id($productId);
        if (!$product) {
            return $this->_json(false, 'Product not found');
        }

        $this->omni->update($productId, [
            'facebook_publish' => 0
        ]);

        return $this->_json(true, 'Product removed from Facebook feed');
    }

    // Get All published products for a platform
    public function get_published_products()
    {
        $platform = $this->input->get('platform');

        // FACEBOOK = LOCAL DATABASE
        if ($platform === 'Facebook') {

            $products = $this->omni->get_facebook_published_products();

            if ($products === false) {
                return $this->_json(false, 'Database error fetching Facebook products');
            }

            return $this->_json(true, 'Facebook published products fetched', $products);
        }

        $platformUrls = [
            'CrudCrud' => 'https://crudcrud.com/api/2350da2f06394fc792347ab832c93d08/products',
            'Beeceptor' => 'https://ca88fe7b23e3b6cf0603.free.beeceptor.com/api/products',
            'Mock API'  => 'https://6968d5a969178471522bae41.mockapi.io/api/products'
        ];

        // SUPABASE
        if ($platform === 'Supabase') {

            $supabaseUrl = 'https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products';
            $supabaseKey = 'sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2';

            $ch = curl_init($supabaseUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'apikey: ' . $supabaseKey,
                    'Authorization: Bearer ' . $supabaseKey,
                    'Accept: application/json'
                ],
                CURLOPT_TIMEOUT => 30
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return $this->_json(false, 'Supabase Curl error: ' . $error);
            }

            $decoded = json_decode($response, true);

            if (!is_array($decoded)) {
                return $this->_json(false, 'Invalid JSON from Supabase');
            }

            return $this->_json(true, 'Supabase products fetched', $decoded);
        }

        // OTHER APIs
        if (!isset($platformUrls[$platform])) {
            return $this->_json(true, 'No persisted products for this platform', []);
        }

        $url = $platformUrls[$platform];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return $this->_json(false, 'Curl error: ' . $error);
        }

        if ($httpCode !== 200) {
            return $this->_json(false, "HTTP error {$httpCode}");
        }

        $decoded = json_decode($response, true);

        if (!is_array($decoded)) {
            return $this->_json(false, 'Invalid JSON received');
        }

        // Some APIs wrap in data[]
        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $decoded = $decoded['data'];
        }

        return $this->_json(true, 'Products fetched', $decoded);
    }

    public function published_view()
    {
        $data['title'] = 'Published Products | AdminLTE';
        $this->load->view('omni_products/published', $data);
    }

    public function update_published($id)
    {
        if (!$id) {
            return $this->_json(false, 'Missing product ID');
        }

        $platform = $this->input->get('platform');
        $payload  = json_decode($this->input->raw_input_stream, true);

        if (!$platform || !is_array($payload)) {
            return $this->_json(false, 'Invalid request');
        }

        switch ($platform) {
            case 'CrudCrud':
                $url = "https://crudcrud.com/api/2350da2f06394fc792347ab832c93d08/products/{$id}";
                $method = 'PUT';
                $headers = ['Content-Type: application/json'];
                break;

            case 'Mock API':
                $url = "https://6968d5a969178471522bae41.mockapi.io/api/products/{$id}";
                $method = 'PUT';
                $headers = ['Content-Type: application/json'];
                break;

            case 'Beeceptor':
                $url = "https://ca0743cc834864a4c49e.free.beeceptor.com/api/products/{$id}";
                $method = 'PUT';
                $headers = ['Content-Type: application/json'];
                break;

            case 'Supabase':
                $supabaseUrl = 'https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products';
                $supabaseKey = 'sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2';

                $url = $supabaseUrl . "?id=eq.{$id}";
                $method = 'PATCH';
                $headers = [
                    'Content-Type: application/json',
                    'apikey: ' . $supabaseKey,
                    'Authorization: Bearer ' . $supabaseKey,
                    'Prefer: return=representation'
                ];
                break;

            default:
                return $this->_json(false, 'Platform not supported');
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30
        ]);

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return $this->_json(false, 'Curl error: ' . $error);
        }

        // Supabase returns 200/204 on success
        if ($platform === 'Supabase' && !in_array($code, [200, 204], true)) {
            return $this->_json(false, "Supabase update failed (HTTP {$code})");
        }

        // Other platforms
        if ($platform !== 'Supabase' && !in_array($code, [200, 201], true)) {
            return $this->_json(false, "Update failed (HTTP {$code})");
        }

        return $this->_json(true, 'Product updated', json_decode($response, true));
    }

    public function delete_published($id)
    {
        if (!$id) {
            return $this->_json(false, 'Missing product ID');
        }

        $platform = $this->input->get('platform');

        switch ($platform) {
            case 'CrudCrud':
                $url = 'https://crudcrud.com/api/2350da2f06394fc792347ab832c93d08/products/' . $id;
                break;

            case 'Beeceptor':
                $url = 'https://caf07b462ad53cca679f.free.beeceptor.com/api/products/' . $id;
                break;

            case 'Mock API':
                $url = 'https://6968d5a969178471522bae41.mockapi.io/api/products/' . $id;
                break;

            case 'Supabase':
                $supabaseUrl = 'https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products';
                $supabaseKey = 'sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2';

                $url = $supabaseUrl . "?id=eq.{$id}";
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_CUSTOMREQUEST  => 'DELETE',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER     => [
                        'apikey: ' . $supabaseKey,
                        'Authorization: Bearer ' . $supabaseKey,
                        'Prefer: return=representation'
                    ],
                    CURLOPT_TIMEOUT        => 30,
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error    = curl_error($ch);

                curl_close($ch);

                if ($error) {
                    return $this->_json(false, 'Supabase curl error: ' . $error);
                }

                if (!in_array($httpCode, [200, 204], true)) {
                    return $this->_json(false, 'Supabase delete failed (HTTP ' . $httpCode . ')');
                }

                return $this->_json(true, 'Product deleted from Supabase', [
                    'platform' => $platform,
                    'httpCode' => $httpCode,
                    'response' => json_decode($response, true)
                ]);

            default:
                return $this->_json(false, 'Delete not supported for this platform');
        }

        // For other platforms (CrudCrud, Beeceptor, Mock API)
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return $this->_json(false, 'Curl error: ' . $error);
        }

        if (!in_array($httpCode, [200, 202, 204], true)) {
            return $this->_json(false, 'Delete failed (HTTP ' . $httpCode . ')');
        }

        return $this->_json(true, 'Product deleted', [
            'platform' => $platform,
            'httpCode' => $httpCode
        ]);
    }

    private function _json($ok, $message, $data = [])
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'ok'      => $ok,
                'message' => $message,
                'data'    => $data
            ]));
    }
}
