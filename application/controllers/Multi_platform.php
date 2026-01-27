<?php defined('BASEPATH') or exit('No direct script access allowed');

class Multi_platform extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'facebook_catalog']);
        $this->load->database();

        // Auth check
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('Multi_platform_model', 'multi');
        $this->load->model('User_model');

        // Share logged-in user
        $logged_user = $this->User_model->get_user_by_id(
            $this->session->userdata('user_id')
        );
        $this->load->vars(['logged_user' => $logged_user]);
    }

    public function index()
    {
        $data['title'] = 'Publish Products | AdminLTE';
        // $data['products'] = $this->multi->get_all();

        $this->load->view('multi_platform/multi_publish', $data);
    }

    // public function publish()
    // {
    //     header('Content-Type: application/json');
    //     $products  = (array)$this->input->post('products');
    //     $platforms = (array)$this->input->post('platforms');

    //     if (empty($products) || empty($platforms)) {
    //         echo json_encode(['status' => 'error', 'message' => 'Nothing selected', 'new_csrf_hash' => $this->security->get_csrf_hash()]);
    //         return;
    //     }

    //     // foreach ($products as $pid) {
    //     //     foreach ($platforms as $platform) {
    //     //         $rowId = $this->multi->insert_pending($pid, $platform);

    //     //         if (strtolower($platform) === 'facebook') {
    //     //             $product = $this->db->get_where('omni_products', ['id' => $pid])->row_array();

    //     //             if (!$product) {
    //     //                 $this->multi->update_result($rowId, 'failed', null, 'Product not found in database');
    //     //                 continue;
    //     //             }

    //     //             // 2. TRY THE API
    //     //             try {
    //     //                 $res = $this->facebook_catalog->publish_product($product);

    //     //                 if ($res['success']) {
    //     //                     $this->multi->update_result($rowId, 'processing', $res['platform_product_id']);
    //     //                 } else {
    //     //                     $this->multi->update_result($rowId, 'failed', null, $res['error']);
    //     //                 }
    //     //             } catch (Exception $e) {
    //     //                 $this->multi->update_result($rowId, 'failed', null, $e->getMessage());
    //     //             }
    //     //         }
    //     //     }
    //     // }
    //     foreach ($products as $pid) {
    //         $product = $this->db->get_where('omni_products', ['id' => $pid])->row_array();

    //         if (!$product) {
    //             $this->multi->update_result($rowId ?? null, 'failed', null, 'Product not found');
    //             continue;
    //         }

    //         $rowId = $this->multi->insert_pending($pid, implode(',', $platforms));

    //         $result = $this->send_to_platforms($product, $platforms);

    //         foreach ($result['responses'] as $platform => $res) {
    //             if ($res['success']) {
    //                 $this->multi->update_result($rowId, 'processing', $res['external_id'] ?? null);
    //             } else {
    //                 $this->multi->update_result($rowId, 'failed', null, $res['error'] ?? 'Unknown error');
    //             }
    //         }

    //         // Update product published_ids field
    //         if (!empty($result['published_ids'])) {
    //             $this->db->where('id', $pid)
    //                 ->update('omni_products', ['published_ids' => json_encode($result['published_ids'])]);
    //         }
    //     }
    //     echo json_encode([
    //         'status' => 'success',
    //         'platforms' => $result['responses'], // REQUIRED
    //         'new_csrf_hash' => $this->security->get_csrf_hash()
    //     ]);

    //     // echo json_encode([
    //     //     'status' => 'success',
    //     //     'new_csrf_hash' => $this->security->get_csrf_hash()
    //     // ]);
    // }
    public function publish()
    {
        header('Content-Type: application/json');

        $products  = (array)$this->input->post('products');
        $platforms = (array)$this->input->post('platforms');

        if (empty($products) || empty($platforms)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Nothing selected',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        foreach ($products as $pid) {

            $product = $this->db
                ->get_where('omni_products', ['id' => $pid])
                ->row_array();

            if (!$product) {
                continue;
            }

            // Publish to platforms
            $result = $this->send_to_platforms($product, $platforms);

            // One row per platform
            foreach ($result['responses'] as $platform => $res) {

                $rowId = $this->multi->insert_pending($pid, $platform);

                if ($res['success']) {
                    $this->multi->update_result(
                        $rowId,
                        'published',
                        $res['external_id'] ?? null
                    );
                } else {
                    $this->multi->update_result(
                        $rowId,
                        'failed',
                        null,
                        $res['error'] ?? 'Unknown error'
                    );
                }
            }

            // Update published_ids in omni_products
            if (!empty($result['published_ids'])) {
                $this->db->where('id', $pid)
                    ->update('omni_products', [
                        'published_ids' => json_encode($result['published_ids'])
                    ]);
            }
        }

        echo json_encode([
            'status' => 'success',
            'platforms' => $result['responses'],
            'new_csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    public function get_meta_details()
    {
        $handle = $this->input->get('handle');

        if (!$handle) {
            echo json_encode(['status' => 'error', 'message' => 'No handle']);
            return;
        }

        $result = $this->facebook_catalog->get_product_details_by_handle($handle);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    // Check that this exists in Multi_platform.php
    public function get_products()
    {
        $searchTerm = $this->input->get('q');
        $products = $this->multi->search_products($searchTerm);

        $results = [];
        foreach ($products as $p) {
            $results[] = [
                'id' => $p['id'],
                'text' => $p['product_name']
            ];
        }

        echo json_encode(['results' => $results]);
    }

    public function get_platform_product_details()
    {
        header('Content-Type: application/json');

        $platform = $this->input->get('platform');
        $platformId = $this->input->get('platform_id');

        if (!$platform || !$platformId) {
            echo json_encode([
                'success' => false,
                'error' => 'Missing platform or product ID'
            ]);
            return;
        }

        try {

            switch ($platform) {

                case 'Facebook':
                    $res = $this->facebook_catalog
                        ->get_product_details_by_handle($platformId);

                    echo json_encode([
                        'success' => true,
                        'data' => $res['product_info'] ?? $res
                    ]);
                    return;

                case 'Supabase':
                    $url = "https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products?id=eq.$platformId";

                    $headers = [
                        'apikey: sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2',
                        'Authorization: Bearer sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2'
                    ];

                    break;

                case 'Mock API':
                    $url = "https://6968d5a969178471522bae41.mockapi.io/api/products/$platformId";
                    $headers = ['Content-Type: application/json'];
                    break;

                default:
                    throw new Exception('Unsupported platform');
            }

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 20
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            echo json_encode([
                'success' => true,
                'data' => json_decode($response, true)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update_meta_item()
    {
        header('Content-Type: application/json');

        $id = $this->input->post('id');

        $product = $this->db->get_where('omni_products', ['id' => $id])->row_array();

        if (!$product) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $extra_images = json_decode($product['extra_image_urls'], true);
        $product['extra_images'] = is_array($extra_images) ? $extra_images : [];

        try {
            $result = $this->facebook_catalog->update_product($product);

            if (isset($result['handles'][0])) {
                $new_handle = $result['handles'][0];

                $this->db->where('product_id', $id)
                    ->where('platform', 'Facebook')
                    ->update('omni_product_publish', [
                        'platform_product_id' => $new_handle,
                        'status' => 'processing'
                    ]);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Update sent to Meta',
                    'new_csrf_hash' => $this->security->get_csrf_hash()
                ]);
            } else {
                $error = isset($result['error'])
                    ? json_encode($result['error'])
                    : 'Meta Response: ' . json_encode($result);

                echo json_encode([
                    'status' => 'error',
                    'message' => $error,
                    'new_csrf_hash' => $this->security->get_csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    public function update_platform_item()
    {
        header('Content-Type: application/json');

        $id = $this->input->post('id');

        $product = $this->db->get_where('omni_products', ['id' => $id])->row_array();
        if (!$product) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Get platforms where this product is already published
        $published = json_decode($product['published_ids'] ?? '[]', true) ?: [];

        if (empty($published)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product has not been published on any platform yet.',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $platformsToSync = array_keys($published); // only platforms where it exists

        // Reuse your existing helper
        $result = $this->send_to_platforms($product, $platformsToSync);

        // Record each response in omni_product_publish table
        foreach ($result['responses'] as $platform => $res) {
            $rowId = $this->multi->insert_pending($id, $platform);

            if ($res['success']) {
                $this->multi->update_result($rowId, 'published', $res['external_id'] ?? null);
            } else {
                $this->multi->update_result($rowId, 'failed', null, $res['error'] ?? 'Unknown error');
            }
        }

        echo json_encode([
            'status' => 'success',
            'platforms' => $result['responses'],
            'new_csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    public function update_single_platform_item()
    {
        header('Content-Type: application/json');

        $productId = (int)$this->input->post('product_id', true);
        $platform  = $this->input->post('platform', true);

        if (!$productId || !$platform) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing update parameters',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // ✅ Load product
        $product = $this->db
            ->get_where('omni_products', ['id' => $productId])
            ->row_array();

        if (!$product) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // ✅ Load existing platform row (SOURCE OF TRUTH)
        $publishRow = $this->db
            ->get_where('omni_product_publish', [
                'product_id' => $productId,
                'platform'   => $platform
            ])
            ->row_array();

        if (!$publishRow || empty($publishRow['platform_product_id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not published on this platform',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $externalId = $publishRow['platform_product_id'];

        try {

            switch ($platform) {

                /* ================= FACEBOOK ================= */
                case 'Facebook':

                    $extra = json_decode($product['extra_image_urls'], true);
                    $product['extra_images'] = is_array($extra) ? $extra : [];

                    $res = $this->facebook_catalog->update_product($product);

                    if (!isset($res['handles'][0])) {
                        throw new Exception('Facebook update failed');
                    }

                    $newId = $res['handles'][0];
                    break;

                /* ================= SUPABASE ================= */
                // case 'Supabase':

                //     // Force PATCH using existing external ID
                //     $result = $this->send_to_platforms(
                //         array_merge($product, ['_force_external_id' => $externalId]),
                //         ['Supabase']
                //     );

                //     if (empty($result['responses']['Supabase']['success'])) {
                //         throw new Exception(
                //             $result['responses']['Supabase']['error'] ?? 'Supabase update failed'
                //         );
                //     }

                //     $newId = $externalId; // Supabase ID never changes
                //     break;

                /* ================= MOCK API ================= */
                // case 'Mock API':

                //     $result = $this->send_to_platforms($product, ['Mock API']);

                //     if (empty($result['responses']['Mock API']['success'])) {
                //         throw new Exception(
                //             $result['responses']['Mock API']['error'] ?? 'Mock API update failed'
                //         );
                //     }

                //     $newId = $result['responses']['Mock API']['external_id'] ?? $externalId;
                //     break;
                case 'Mock API':
                case 'Supabase':
                    $result = $this->update_platform_product($product, $platform, $externalId);

                    if (empty($result['success'])) {
                        throw new Exception($result['error'] ?? "$platform update failed");
                    }

                    $newId = $externalId; // ID doesn’t change
                    break;

                default:
                    throw new Exception('Unsupported platform');
            }

            // ✅ Update ONLY this platform row
            $this->db->where([
                'product_id' => $productId,
                'platform'   => $platform
            ])->update('omni_product_publish', [
                'platform_product_id' => $newId,
                'status'              => 'synced',
                'updated_at'          => date('Y-m-d H:i:s')
            ]);

            echo json_encode([
                'status' => 'success',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
        } catch (Exception $e) {

            $this->db->where([
                'product_id' => $productId,
                'platform'   => $platform
            ])->update('omni_product_publish', [
                'status'     => 'failed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    public function get_publish_status($product_ids)
    {
        $this->db->select('opp.*, op.product_name')
            ->from('omni_product_publish opp')
            ->join('omni_products op', 'op.id = opp.product_id');

        if (!empty($product_ids)) {
            $this->db->where_in('opp.product_id', $product_ids);
        } else {
            // If no products selected, show last 10 entries by default
            $this->db->limit(10);
        }

        return $this->db->order_by('opp.created_at', 'DESC')->get()->result_array();
    }

    public function publish_status()
    {
        // Get products from GET request
        $product_ids = $this->input->get('products');

        // Call the model
        $data = $this->multi->get_publish_status($product_ids);

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
    }

    public function delete_meta_item()
    {
        header('Content-Type: application/json');

        $id = $this->input->post('id');

        $product = $this->db
            ->get_where('omni_products', ['id' => $id])
            ->row_array();

        if (!$product) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $retailer_id = 'VISIONVYAS_' . $product['id'];

        try {
            $result = $this->facebook_catalog->delete_product($retailer_id);

            if (isset($result['handles'][0])) {

                $this->db->where([
                    'product_id' => $id,
                    'platform'   => 'Facebook'
                ])->update('omni_product_publish', [
                    'status' => 'deleted',
                    'platform_product_id' => null
                ]);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Delete request sent to Meta',
                    'new_csrf_hash' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Meta response: ' . json_encode($result),
                    'new_csrf_hash' => $this->security->get_csrf_hash()
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    public function delete_platform_item()
    {
        header('Content-Type: application/json');

        $productId = $this->input->post('product_id', true);
        $platform  = $this->input->post('platform', true);
        $externalId = $this->input->post('platform_product_id', true);

        if (!$productId || !$platform || !$externalId) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing delete parameters',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        try {

            switch ($platform) {

                case 'Supabase':
                    $url = "https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products?id=eq.$externalId";
                    $headers = [
                        'apikey: sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2',
                        'Authorization: Bearer sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2'
                    ];

                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_CUSTOMREQUEST => 'DELETE',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => $headers
                    ]);
                    curl_exec($ch);
                    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if (!in_array($code, [200, 204])) {
                        throw new Exception('Supabase delete failed');
                    }
                    break;

                case 'Mock API':
                    if (empty($externalId)) {
                        throw new Exception('Mock API external ID missing');
                    }

                    $url = "https://6968d5a969178471522bae41.mockapi.io/api/products/$externalId";

                    $ch = curl_init($url);
                    curl_setopt_array($ch, [
                        CURLOPT_CUSTOMREQUEST => 'DELETE',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                        CURLOPT_TIMEOUT => 20
                    ]);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlErr = curl_error($ch);
                    curl_close($ch);

                    if ($curlErr) {
                        throw new Exception('Mock API cURL error: ' . $curlErr);
                    }

                    if (!in_array($httpCode, [200, 204], true)) {
                        $decoded = json_decode($response, true);
                        $msg = $decoded['message'] ?? $response ?? "HTTP $httpCode";
                        throw new Exception('Mock API delete failed: ' . $msg);
                    }
                    break;


                case 'Facebook':
                    $retailerId = 'VISIONVYAS_' . (int)$productId;
                    $res = $this->facebook_catalog->delete_product($retailerId);

                    if (!isset($res['handles'][0])) {
                        throw new Exception('Facebook delete failed');
                    }
                    break;

                default:
                    throw new Exception('Unsupported platform');
            }

            // Update ONLY this platform row
            $this->db->where([
                'product_id' => $productId,
                'platform' => $platform
            ])->update('omni_product_publish', [
                'status' => 'deleted',
                'platform_product_id' => null
            ]);

            echo json_encode([
                'status' => 'success',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    public function delete_history()
    {
        header('Content-Type: application/json');

        $id = $this->input->post('id');

        if (!$id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Missing ID',
                'new_csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $deleted = $this->db
            ->where('id', $id)
            ->delete('omni_product_publish');

        echo json_encode([
            'status' => $deleted ? 'success' : 'error',
            'new_csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    private function send_to_platforms(array $product, array $platforms): array
    {
        $urls = [
            'Supabase' => 'https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products',
            'Mock API' => 'https://6968d5a969178471522bae41.mockapi.io/api/products',
        ];

        $supabaseKey = 'sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2';
        $responses = [];
        $publishedIds = json_decode($product['published_ids'] ?? '[]', true) ?: [];

        foreach ($platforms as $platform) {
            $payload = $product;
            $platformResponse = [];

            if (strtolower($platform) === 'facebook') {
                try {
                    $res = $this->facebook_catalog->publish_product($product);

                    if ($res['success']) {
                        $responses[$platform] = [
                            'success' => true,
                            'external_id' => $res['platform_product_id'],
                            'payload_sent' => $product,
                            'response_received' => $res
                        ];
                        $publishedIds[$platform] = $res['platform_product_id'];
                    } else {
                        $responses[$platform] = [
                            'success' => false,
                            'error' => $res['error'],
                            'payload_sent' => $product,
                            'response_received' => $res
                        ];
                    }
                } catch (Exception $e) {
                    $responses[$platform] = [
                        'success' => false,
                        'error' => $e->getMessage(),
                        'payload_sent' => $product,
                        'response_received' => null
                    ];
                }
                continue;
            }

            if (!isset($urls[$platform])) continue;

            $headers = ['Content-Type: application/json'];

            if ($platform === 'Supabase') {
                $headers = [
                    'Content-Type: application/json',
                    'apikey: ' . $supabaseKey,
                    'Authorization: Bearer ' . $supabaseKey,
                    'Prefer: return=representation'
                ];

                $payload = [
                    'product_name'       => $product['product_name'],
                    'sku'                => $product['sku'],
                    'brand'              => $product['brand'],
                    'category_name'      => $product['category_name'],
                    'category_code'      => $product['category_code'],
                    'price'              => (float)$product['price'],
                    'sale_price'         => (float)$product['sale_price'],
                    'currency'           => $product['currency'] ?: 'INR',
                    'stock'              => (int)$product['stock'],
                    'weight'             => $product['weight'],
                    'length'             => $product['length'],
                    'width'              => $product['width'],
                    'height'             => $product['height'],
                    'short_description'  => $product['short_description'],
                    'description'        => $product['description'],
                    'main_image_url'     => $product['main_image_url'],
                    'extra_image_urls'   => json_decode($product['extra_image_urls'], true),
                    'condition_type'     => $product['condition_type'],
                    'variant_attributes' => json_decode($product['variant_attributes'], true),
                    'gtin'               => $product['gtin'],
                    'mpn'                => $product['mpn'],
                    'video_url'          => $product['video_url'],
                    'status'             => $product['status']
                ];
            } else {
                $payload['published_at'] = date('c');
                $payload['published_from'] = 'multi_platform';
            }

            $ch = curl_init($urls[$platform]);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 30
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error || !in_array($httpCode, [200, 201], true)) {
                $responses[$platform] = [
                    'success' => false,
                    'error' => $error ?: "HTTP {$httpCode}",
                    'payload_sent' => $payload,
                    'response_received' => $response
                ];
                continue;
            }

            $decoded = json_decode($response, true);
            $externalId = $decoded['_id'] ?? $decoded['id'] ?? ($decoded[0]['id'] ?? null);

            if ($externalId) {
                $publishedIds[$platform] = $externalId;
            }

            $responses[$platform] = [
                'success' => true,
                'external_id' => $externalId,
                'payload_sent' => $payload,
                'response_received' => $response
            ];
        }

        return [
            'responses' => $responses,
            'published_ids' => $publishedIds
        ];
    }

    // private function update_platform_product(array $product, string $platform, string $externalId): array
    // {
    //     $urls = [
    //         'Supabase' => 'https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products',
    //         'Mock API' => 'https://6968d5a969178471522bae41.mockapi.io/api/products',
    //     ];

    //     $supabaseKey = 'sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2';
    //     $responseData = [];

    //     switch ($platform) {
    //         case 'Supabase':
    //             $url = $urls['Supabase'] . '?id=eq.' . $externalId;
    //             $headers = [
    //                 'Content-Type: application/json',
    //                 'apikey: ' . $supabaseKey,
    //                 'Authorization: Bearer ' . $supabaseKey,
    //                 'Prefer: return=representation'
    //             ];
    //             $payload = $product; // map fields same as in create
    //             $method = 'PATCH';
    //             break;

    //         case 'Mock API':
    //             $url = $urls['Mock API'] . '/' . $externalId;
    //             $headers = ['Content-Type: application/json'];
    //             $payload = $product; // add any fields you want to update
    //             $method = 'PUT';
    //             break;

    //         default:
    //             return ['success' => false, 'error' => 'Unsupported platform'];
    //     }

    //     $ch = curl_init($url);
    //     curl_setopt_array($ch, [
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_CUSTOMREQUEST => $method,
    //         CURLOPT_HTTPHEADER => $headers,
    //         CURLOPT_POSTFIELDS => json_encode($payload),
    //         CURLOPT_TIMEOUT => 20
    //     ]);

    //     $response = curl_exec($ch);
    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     $curlErr = curl_error($ch);
    //     curl_close($ch);

    //     if ($curlErr) {
    //         return ['success' => false, 'error' => 'cURL error: ' . $curlErr];
    //     }

    //     if (!in_array($httpCode, [200, 201, 204], true)) {
    //         $decoded = json_decode($response, true);
    //         $msg = $decoded['message'] ?? $response ?? "HTTP $httpCode";
    //         return ['success' => false, 'error' => $msg];
    //     }

    //     $decoded = json_decode($response, true);
    //     $externalId = $decoded['_id'] ?? $decoded['id'] ?? $externalId;

    //     return ['success' => true, 'external_id' => $externalId, 'response' => $decoded];
    // }
    private function update_platform_product(array $product, string $platform, string $externalId): array
    {
        $urls = [
            'Supabase' => 'https://ryeevubdyhdhbxbhpadd.supabase.co/rest/v1/products',
            'Mock API' => 'https://6968d5a969178471522bae41.mockapi.io/api/products',
        ];

        $supabaseKey = 'sb_publishable_sTNam675QMlDgMmmhTnOyQ_Q_-CfbA2';

        // Only send allowed columns
        $allowedColumns = [
            'product_name',
            'sku',
            'brand',
            'category_name',
            'category_code',
            'price',
            'sale_price',
            'currency',
            'stock',
            'weight',
            'length',
            'width',
            'height',
            'short_description',
            'description',
            'main_image_url',
            'extra_image_urls',
            'condition_type',
            'variant_attributes',
            'gtin',
            'mpn',
            'video_url',
            'status'
        ];
        $payload = array_intersect_key($product, array_flip($allowedColumns));

        switch ($platform) {
            case 'Supabase':
                $url = $urls['Supabase'] . '?id=eq.' . $externalId;
                $headers = [
                    'Content-Type: application/json',
                    'apikey: ' . $supabaseKey,
                    'Authorization: Bearer ' . $supabaseKey,
                    'Prefer: return=representation'
                ];
                $method = 'PATCH'; // PATCH updates existing row
                break;

            case 'Mock API':
                $url = $urls['Mock API'] . '/' . $externalId;
                $headers = ['Content-Type: application/json'];
                $method = 'PUT'; // Mock API uses PUT for update
                break;

            default:
                return ['success' => false, 'error' => 'Unsupported platform'];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 20
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['success' => false, 'error' => 'cURL error: ' . $curlErr];
        }

        if (!in_array($httpCode, [200, 201, 204], true)) {
            $decoded = json_decode($response, true);
            $msg = $decoded['message'] ?? $response ?? "HTTP $httpCode";
            return ['success' => false, 'error' => $msg];
        }

        $decoded = json_decode($response, true);
        $externalId = $decoded['_id'] ?? $decoded['id'] ?? $externalId;

        return ['success' => true, 'external_id' => $externalId, 'response' => $decoded];
    }
}
