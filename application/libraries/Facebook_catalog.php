<?php defined('BASEPATH') or exit('No direct script access allowed');

class Facebook_catalog
{
    protected $access_token = '';
    protected $catalog_id   = '';

    // public function publish_product(array $product)
    // {
    //     // Match the retailer_id format from your successful test
    //     $retailer_id = 'VISIONVYAS_' . $product['id'];

    //     $payload = [
    //         'item_type' => 'PRODUCT_ITEM',
    //         'allow_upsert' => true,
    //         'requests' => [
    //             [
    //                 'method' => 'CREATE',
    //                 'retailer_id' => $retailer_id,
    //                 'data' => [
    //                     'name'         => $product['product_name'],
    //                     'description'  => $product['description'] ?: $product['product_name'],
    //                     'availability' => 'in stock',
    //                     'condition'    => 'new',
    //                     'price'        => (int)$product['price'],
    //                     'currency'     => $product['currency'] ?: 'INR',
    //                     'image_url'    => $product['main_image_url'],
    //                     'url'          => $product['url'] ?: 'https://visionvyas.com',
    //                     'brand'        => $product['brand'] ?: 'VisionVyas'
    //                 ]
    //             ]
    //         ]
    //     ];

    //     // Using the EXACT endpoint from your successful test
    //     $url = "https://graph.facebook.com/v24.0/{$this->catalog_id}/batch";

    //     $ch = curl_init($url);
    //     curl_setopt_array($ch, [
    //         CURLOPT_POST           => true,
    //         CURLOPT_POSTFIELDS     => json_encode($payload),
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_SSL_VERIFYPEER => false,
    //         CURLOPT_HTTPHEADER     => [
    //             'Content-Type: application/json',
    //             'Authorization: Bearer ' . $this->access_token
    //         ],
    //         CURLOPT_TIMEOUT => 30
    //     ]);

    //     $response = curl_exec($ch);
    //     $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     $json = json_decode($response, true);
    //     curl_close($ch);

    //     // For the /batch endpoint, Meta returns "handles" if successful
    //     if ($http_code === 200 && isset($json['handles'][0])) {
    //         return [
    //             'success' => true,
    //             'platform_product_id' => $json['handles'][0]
    //         ];
    //     }

    //     // If there is a validation error, extract it
    //     if (isset($json['validation_status'][0]['errors'][0]['message'])) {
    //         $msg = $json['validation_status'][0]['errors'][0]['message'];
    //     } else {
    //         $msg = $json['error']['message'] ?? 'Meta Error: ' . $response;
    //     }

    //     return [
    //         'success' => false,
    //         'error' => substr($msg, 0, 255)
    //     ];
    // }
    public function publish_product(array $product)
    {
        $retailer_id = 'VISIONVYAS_' . $product['id'];

        $currency = $product['currency'] ?: 'INR';

        // Meta expects minor units (paise / cents)
        $price = (int) round($product['price'] * 100);

        $sale_price = !empty($product['sale_price'])
            ? (int) round($product['sale_price'] * 100)
            : null;

        $data = [
            'name'         => $product['product_name'],
            'description'  => $product['description'] ?: $product['product_name'],
            'availability' => 'in stock',
            'condition'    => 'new',
            'price'        => $price,
            'currency'     => $currency,
            'image_url'    => $product['main_image_url'],
            'url'          => $product['url'] ?: 'https://visionvyas.com',
            'brand'        => $product['brand'] ?: 'VisionVyas'
        ];

        // ✅ Optional sale price (ONLY if valid)
        if ($sale_price !== null && $sale_price > 0) {
            $data['sale_price'] = $sale_price;
        }

        $payload = [
            'item_type' => 'PRODUCT_ITEM',
            'allow_upsert' => true,
            'requests' => [
                [
                    'method' => 'CREATE',
                    'retailer_id' => $retailer_id,
                    'data' => $data
                ]
            ]
        ];

        $url = "https://graph.facebook.com/v24.0/{$this->catalog_id}/batch";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->access_token
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $json      = json_decode($response, true);
        curl_close($ch);

        if ($http_code === 200 && isset($json['handles'][0])) {
            return [
                'success' => true,
                'platform_product_id' => $json['handles'][0]
            ];
        }

        if (isset($json['validation_status'][0]['errors'][0]['message'])) {
            $msg = $json['validation_status'][0]['errors'][0]['message'];
        } else {
            $msg = $json['error']['message'] ?? 'Meta Error: ' . $response;
        }

        return [
            'success' => false,
            'error' => substr($msg, 0, 255)
        ];
    }

    public function get_product_details_by_handle($handle)
    {
        $status_url = "https://graph.facebook.com/v24.0/{$this->catalog_id}/check_batch_request_status?handle={$handle}&access_token={$this->access_token}";

        $ch = curl_init($status_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $status_res = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (!isset($status_res['data'][0])) {
            return ['success' => false, 'error' => 'Handle expired or invalid'];
        }

        $report = $status_res['data'][0];

        $fields = "id,name,description,image_url,additional_image_urls,price,currency,url,visibility";

        $product_url = "https://graph.facebook.com/v24.0/{$this->catalog_id}/products?fields={$fields}&access_token={$this->access_token}";

        $ch2 = curl_init($product_url);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        $products_data = json_decode(curl_exec($ch2), true);
        curl_close($ch2);

        return [
            'success' => true,
            'batch_status' => $report['status'],
            'product_info' => $products_data['data'][0] ?? null // Returns the first product in this catalog
        ];
    }

    public function update_product(array $product)
    {
        $retailer_id = 'VISIONVYAS_' . $product['id'];
        $currency = $product['currency'] ?: 'INR';
        $product_link = !empty($product['product_url']) ? $product['product_url'] : 'https://visionvyas.com/product/' . $product['id'];
        $price = (int) round($product['price'] * 100);
        $sale_price = !empty($product['sale_price'])
            ? (int) round($product['sale_price'] * 100)
            : null;

        $data = [
            'name'           => $product['product_name'],
            'description'    => $product['description'] ?: $product['product_name'],
            'price'          => $price,
            'currency'       => $currency,
            'image_url'      => $product['main_image_url'],
            'additional_image_urls' => $product['extra_images'],
            'url'            => $product_link,
            'brand'          => $product['brand'] ?: 'VisionVyas',
            'condition'      => 'new',
            'availability'   => 'in stock',
        ];

        if ($sale_price !== null && $sale_price > 0) {
            $data['sale_price'] = $sale_price;
        }

        $payload = [
            'item_type' => 'PRODUCT_ITEM',
            'allow_upsert' => true,
            'requests' => [
                [
                    'method' => 'UPDATE',
                    'retailer_id' => $retailer_id,
                    'data' => $data
                ]
            ]
        ];

        log_message('debug', 'Meta Payload: ' . json_encode($payload));

        $url = "https://graph.facebook.com/v24.0/{$this->catalog_id}/batch";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->access_token
            ]
        ]);

        $response = curl_exec($ch);
        $json = json_decode($response, true);
        curl_close($ch);

        return $json;
    }

    public function delete_product(string $retailer_id)
    {
        $payload = [
            'item_type' => 'PRODUCT_ITEM',
            'requests' => [
                [
                    'method' => 'DELETE',
                    'retailer_id' => $retailer_id
                ]
            ]
        ];

        log_message('debug', 'Meta Delete Payload: ' . json_encode($payload));

        $url = "https://graph.facebook.com/v24.0/{$this->catalog_id}/batch";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->access_token
            ]
        ]);

        $response = curl_exec($ch);
        $json = json_decode($response, true);
        curl_close($ch);

        return $json;
    }
}
