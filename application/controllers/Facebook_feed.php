<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* class Facebook_feed extends CI_Controller
{
    public function index()
    {
        // Meta requires public access — DO NOT use session auth here
        header("Content-Type: application/xml; charset=utf-8");

        $this->load->database();
        $this->load->model('Omni_product_model', 'omni');

        $products = $this->omni->get_facebook_products();

        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        echo '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . PHP_EOL;
        echo '<channel>' . PHP_EOL;

        echo '<title>Your Store Name</title>' . PHP_EOL;
        echo '<link>' . base_url() . '</link>' . PHP_EOL;
        echo '<description>Facebook Product Catalog Feed</description>' . PHP_EOL;

        foreach ($products as $product) {

            // Skip inactive / unpublished products
            // if ($product->status !== 'active') {
            //     continue;
            // }

            // Stock mapping
            $availability = ((int)$product->stock > 0) ? 'in stock' : 'out of stock';

            // Price logic
            $price = (!empty($product->sale_price) && $product->sale_price > 0)
                ? $product->sale_price
                : $product->price;

            $currency = $product->currency ?: 'INR';

            // Build product URL (important for ngrok)
            $productUrl = base_url('product/' . $product->sku);

            echo '<item>' . PHP_EOL;

            echo '<g:id>' . htmlspecialchars($product->sku) . '</g:id>' . PHP_EOL;
            echo '<g:title>' . htmlspecialchars($product->product_name) . '</g:title>' . PHP_EOL;

            echo '<g:description>' .
                htmlspecialchars(strip_tags($product->description ?: $product->short_description)) .
                '</g:description>' . PHP_EOL;

            echo '<g:link>' . $productUrl . '</g:link>' . PHP_EOL;

            echo '<g:image_link>' .
                htmlspecialchars($product->main_image_url) .
                '</g:image_link>' . PHP_EOL;

            // Extra images (optional but powerful)
            if (!empty($product->extra_image_urls)) {
                $extraImages = json_decode($product->extra_image_urls, true);
                if (is_array($extraImages)) {
                    foreach ($extraImages as $img) {
                        echo '<g:additional_image_link>' .
                            htmlspecialchars($img) .
                            '</g:additional_image_link>' . PHP_EOL;
                    }
                }
            }

            echo '<g:availability>' . $availability . '</g:availability>' . PHP_EOL;
            echo '<g:price>' . number_format($price, 2, '.', '') . ' ' . $currency . '</g:price>' . PHP_EOL;

            echo '<g:brand>' . htmlspecialchars($product->brand ?: 'Generic') . '</g:brand>' . PHP_EOL;
            echo '<g:condition>' . htmlspecialchars($product->condition_type ?: 'new') . '</g:condition>' . PHP_EOL;

            // Strongly recommended identifiers
            if (!empty($product->gtin)) {
                echo '<g:gtin>' . htmlspecialchars($product->gtin) . '</g:gtin>' . PHP_EOL;
            }

            if (!empty($product->mpn)) {
                echo '<g:mpn>' . htmlspecialchars($product->mpn) . '</g:mpn>' . PHP_EOL;
            }

            echo '<g:google_product_category>' .
                htmlspecialchars($product->category_name) .
                '</g:google_product_category>' . PHP_EOL;

            echo '</item>' . PHP_EOL;
        }

        echo '</channel>' . PHP_EOL;
        echo '</rss>' . PHP_EOL;
    }
} */

class Facebook_feed extends CI_Controller
{
    public function index()
    {
        // Required headers for Facebook crawler
        header("Content-Type: application/xml; charset=UTF-8");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");

        $this->load->database();
        $this->load->model('Omni_product_model', 'omni');

        // Only products explicitly published to Facebook
        $products = $this->omni->get_facebook_products();

        // Base URL (force HTTPS)
        $base = base_url();
        $base = str_replace('http://', 'https://', $base);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        echo '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . PHP_EOL;
        echo '<channel>' . PHP_EOL;

        echo '<title>My Omni Channel</title>' . PHP_EOL;
        echo '<link>' . htmlspecialchars($base) . '</link>' . PHP_EOL;
        echo '<description>Facebook Product Catalog Feed</description>' . PHP_EOL;

        foreach ($products as $product) {

            // Skip invalid products
            if (empty($product->sku) || empty($product->product_name) || empty($product->price)) {
                continue;
            }

            // Map stock to availability
            $availability = ((int)$product->stock > 0) ? 'in stock' : 'out of stock';

            // Determine price
            $price = (!empty($product->sale_price) && $product->sale_price > 0)
                ? $product->sale_price
                : $product->price;

            $currency = !empty($product->currency) ? $product->currency : 'INR';

            // Product URL (force HTTPS)
            $productUrl = rtrim($base, '/') . '/product/' . rawurlencode($product->sku);

            // Main image (required)
            $mainImage = !empty($product->main_image_url) ? str_replace('http://', 'https://', trim($product->main_image_url)) : '';

            echo '<item>' . PHP_EOL;
            echo '<g:id>' . htmlspecialchars($product->sku) . '</g:id>' . PHP_EOL;
            echo '<g:title>' . htmlspecialchars($product->product_name) . '</g:title>' . PHP_EOL;

            $description = $product->description ?: $product->short_description;
            $description = trim(strip_tags($description));
            echo '<g:description>' . htmlspecialchars($description) . '</g:description>' . PHP_EOL;

            echo '<g:link>' . htmlspecialchars($productUrl) . '</g:link>' . PHP_EOL;

            if ($mainImage) {
                echo '<g:image_link>' . htmlspecialchars($mainImage) . '</g:image_link>' . PHP_EOL;
            }

            if (!empty($product->extra_image_urls)) {
                $extraImages = json_decode($product->extra_image_urls, true);
                if (is_array($extraImages)) {
                    foreach ($extraImages as $imgString) {
                        // Split comma-separated URLs inside the string
                        $urls = array_map('trim', explode(',', $imgString));
                        foreach ($urls as $url) {
                            if (!empty($url)) {
                                $url = str_replace('http://', 'https://', $url);
                                echo '<g:additional_image_link>' . htmlspecialchars($url) . '</g:additional_image_link>' . PHP_EOL;
                            }
                        }
                    }
                }
            }

            echo '<g:availability>' . $availability . '</g:availability>' . PHP_EOL;
            echo '<g:price>' . number_format($price, 2, '.', '') . ' ' . $currency . '</g:price>' . PHP_EOL;
            echo '<g:brand>' . htmlspecialchars($product->brand ?: 'Generic') . '</g:brand>' . PHP_EOL;
            echo '<g:condition>new</g:condition>' . PHP_EOL;

            // GTIN / MPN if available
            if (!empty($product->gtin)) {
                echo '<g:gtin>' . htmlspecialchars($product->gtin) . '</g:gtin>' . PHP_EOL;
            }
            if (!empty($product->mpn)) {
                echo '<g:mpn>' . htmlspecialchars($product->mpn) . '</g:mpn>' . PHP_EOL;
            }

            echo '<g:identifier_exists>true</g:identifier_exists>' . PHP_EOL;

            // Google product category — fallback to 'Other'
            echo '<g:google_product_category>' . htmlspecialchars($product->category_name ?: 'Other') . '</g:google_product_category>' . PHP_EOL;

            echo '</item>' . PHP_EOL;
        }

        echo '</channel>' . PHP_EOL;
        echo '</rss>' . PHP_EOL;
    }
}
