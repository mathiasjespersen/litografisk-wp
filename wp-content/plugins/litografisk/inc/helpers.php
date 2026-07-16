<?php 

class LitografiskHelper {

    public static function get_product_id_by_slug( $slug, $post_type = "product" ) {
        $query = new WP_Query(
            array(
                'name'   => $slug,
                'post_type'   => $post_type,
                'posts_per_page' => 1,
                'fields'      => 'ids',
            ) );
        $products = $query->get_posts();

        return !empty($products) ? array_shift($products) : null;
    }

    /**
     * Convert absolute URL to relative URL for product links
     */
    public static function product_link($link) {
        $link = str_replace(site_url(), '', $link);
    
        return $link;
    }

    public static function normalize_text($text) {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return str_replace("\u{00AD}", '', $text);
    }

    public static function clean_media_data($data) {
        if (is_array($data) && isset($data['type']) && ($data['type'] === 'image' || $data['type'] === 'video')) {

            $keys_to_unset = [
                'width',
                'height',
                'icon',
                'subtype',
                'mime_type',
                'menu_order',
                'modified',
                'date', 
                'uploaded_to',
                'status',
                'name',
                'caption', 
                'description',
                'author',
                'alt',
                'link',
                'filesize',
                'ID'
            ];

            foreach ($keys_to_unset as $key) {
                if (isset($data[$key])) {
                    unset($data[$key]);
                }
            }        
            
        }
        
        
        if (is_string($data)) {
        //  $data = replace_link_images($data);
        }
        return $data;
    }

    public static function prepare_product_data($product) {
    if (!$product) {
        return [];
    }

        $currency_symbol = get_woocommerce_currency_symbol();

        $product_name = $product->get_name();
        $product_image_hover = get_field('product_image_hover', $product->get_id());
        $product_link = LitografiskHelper::product_link(get_permalink($product->get_id()));
        $product_regular_price = $product->get_regular_price();
        $product_sale_price = $product->get_sale_price();
        $product_price = $product->get_price();
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail');
        $product_image = $image ? $image[0] : '';
        $product_image_width = $image ? $image[1] : '';
        $product_image_height = $image ? $image[2] : '';


        if (is_array($product_image_hover)) {
            $product_image_hover = LitografiskHelper::product_link($product_image_hover['url']);
        } else {
            $product_image_hover = LitografiskHelper::clean_media_data($product_image ?? []);
        }

        $artist_terms = get_the_terms($product->get_id(), 'pa_kunstnere');

        return [
            'id' => $product->get_id(),
            'publication_time' => get_the_date('U', $product->get_id()),
            'name' => $product_name,
            'slug' => $product->get_slug(),
            'image' => LitografiskHelper::clean_media_data($product_image ?? []),
            'image_width' => $product_image_width,
            'image_height' => $product_image_height,
            'image_hover' => $product_image_hover,
            'link' => $product_link,
            'regularPrice' => $product_regular_price,
            'salePrice' => $product_sale_price,
            'price' => $product_price,
            'description' => nl2br($product->get_description()),
            'artists' => $artist_terms ? array_map(function($term) {
                return [
                    'unique_id' => uniqid('artist_'),
                    'name' => $term->name,
                    'slug' => $term->slug,
                ];
            }, $artist_terms) : [],
        ];
    }
}