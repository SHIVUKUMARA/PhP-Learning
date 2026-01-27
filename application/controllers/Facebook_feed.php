<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Facebook_feed extends CI_Controller
{
    public function index()
    {
        // Required headers for Meta crawler
        header("Content-Type: application/xml; charset=UTF-8");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");

        $this->load->database();
        $this->load->model('Omni_product_model', 'omni');

        // Only Facebook-published products
        $products = $this->omni->get_facebook_products();

        $base = str_replace('http://', 'https://', base_url());

        echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        echo '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . PHP_EOL;
        echo '<channel>' . PHP_EOL;

        echo '<title>My Omni Channel</title>' . PHP_EOL;
        echo '<link>' . htmlspecialchars($base) . '</link>' . PHP_EOL;
        echo '<description>Facebook Product Catalog Feed</description>' . PHP_EOL;

        foreach ($products as $product) {

            if (
                empty($product->sku) ||
                empty($product->product_name) ||
                empty($product->price) ||
                empty($product->main_image_url)
            ) {
                continue;
            }

            $availability = ((int)$product->stock > 0) ? 'in stock' : 'out of stock';
            $currency = !empty($product->currency) ? $product->currency : 'INR';

            $productUrl = rtrim($base, '/') . '/product/' . rawurlencode($product->sku);
            $mainImage = str_replace('http://', 'https://', trim($product->main_image_url));

            $description = trim(strip_tags($product->description ?: $product->short_description));

            echo '<item>' . PHP_EOL;

            echo '<g:id>' . htmlspecialchars($product->sku) . '</g:id>' . PHP_EOL;
            echo '<g:title>' . htmlspecialchars($product->product_name) . '</g:title>' . PHP_EOL;
            echo '<g:description>' . htmlspecialchars($description) . '</g:description>' . PHP_EOL;
            echo '<g:link>' . htmlspecialchars($productUrl) . '</g:link>' . PHP_EOL;
            echo '<g:image_link>' . htmlspecialchars($mainImage) . '</g:image_link>' . PHP_EOL;

            if (!empty($product->extra_image_urls)) {
                $extraImages = json_decode($product->extra_image_urls, true);
                if (is_array($extraImages)) {
                    foreach ($extraImages as $imgString) {
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

            if (!empty($product->video_url)) {
                $video = str_replace('http://', 'https://', trim($product->video_url));
                echo '<g:video_link>' . htmlspecialchars($video) . '</g:video_link>' . PHP_EOL;
            }

            echo '<g:brand>' . htmlspecialchars($product->brand ?: 'Generic') . '</g:brand>' . PHP_EOL;

            if (!empty($product->gtin)) {
                echo '<g:gtin>' . htmlspecialchars($product->gtin) . '</g:gtin>' . PHP_EOL;
            }

            if (!empty($product->mpn)) {
                echo '<g:mpn>' . htmlspecialchars($product->mpn) . '</g:mpn>' . PHP_EOL;
            }

            echo '<g:condition>' . htmlspecialchars($product->condition_type ?: 'new') . '</g:condition>' . PHP_EOL;

            echo '<g:availability>' . $availability . '</g:availability>' . PHP_EOL;
            echo '<g:quantity>' . (int)$product->stock . '</g:quantity>' . PHP_EOL;

            echo '<g:price>' . number_format($product->price, 2, '.', '') . ' ' . $currency . '</g:price>' . PHP_EOL;

            if (!empty($product->sale_price) && $product->sale_price > 0) {
                echo '<g:sale_price>' . number_format($product->sale_price, 2, '.', '') . ' ' . $currency . '</g:sale_price>' . PHP_EOL;
            }

            if (!empty($product->weight) && $product->weight > 0) {
                echo '<g:shipping_weight>' . number_format($product->weight, 2, '.', '') . ' kg</g:shipping_weight>' . PHP_EOL;
            }

            echo '<g:google_product_category>' .
                htmlspecialchars($product->category_name ?: 'Other') .
                '</g:google_product_category>' . PHP_EOL;

            echo '</item>' . PHP_EOL;
        }

        echo '</channel>' . PHP_EOL;
        echo '</rss>' . PHP_EOL;
    }
}
