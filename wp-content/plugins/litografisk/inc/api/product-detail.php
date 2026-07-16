<?php

add_action( 'rest_api_init', function() {

    // Product details
    register_rest_route( 'litografisk/v1', '/shop/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'litografisk_get_product_detail'
        )
    );

});


function litografisk_get_related_products($product, $args) {
    $defaults = array(
        'posts_per_page' => 3,
        'columns' => 2,
        'orderby' => 'rand', // @codingStandardsIgnoreLine.
        'order' => 'desc',
    );

    $args = wp_parse_args($args, $defaults);
    $current_lang = apply_filters( 'wpml_current_language', NULL );

    // Get visible related products then sort them at random.
    $args['related_products'] = array_filter(array_map('wc_get_product', wc_get_related_products($product->get_id(), $args['posts_per_page'], $product->get_upsell_ids())), 'wc_products_array_filter_visible');

    // Handle orderby.
    $args['related_products'] = wc_products_array_orderby($args['related_products'], $args['orderby'], $args['order']);
    $related_products = [];
    foreach ($args['related_products'] as $value) {
        $product_language_details = apply_filters( 'wpml_post_language_details', NULL, $value->get_id() ) ;
        if ($product_language_details && $product_language_details['language_code'] == $current_lang) {
            $related_products[] = LitografiskHelper::prepare_product_data($value);
        }
    }

    return $related_products;
}


function litografisk_get_product_detail($data) {
    global $product;
    $product_id = LitografiskHelper::get_product_id_by_slug($data['slug']);
    $product = wc_get_product($product_id);
    
    if (!$product) {
        return new WP_Error(
            'product_not_found',
            'Product not found.',
            array( 'status' => 404 )
        );
    }

    $currency_symbol = get_woocommerce_currency_symbol();
    $data = [];


    if ($product) {
        $product_name = $product->get_name();
        $product_id = $product->get_id();
        $product_link = LitografiskHelper::product_link(get_permalink($product->get_id()));
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail');
        $product_image = $image ? $image[0] : '';
        $product_description = $product->get_description();
        $product_short_description = $product->get_short_description();
        
        $related_products = litografisk_get_related_products($product, array(
            'posts_per_page' => 6,
            'columns'        => 3,
        ) );
              
        $data['id'] = $product_id;
        $data['name'] = $product_name;
        $data['image'] = LitografiskHelper::clean_media_data($product_image ?? []);
        $data['link'] = $product_link;
        $data['description'] = apply_filters('the_content', $product_description);
        $data['shortDescription'] = $product_short_description;
        
        //$data['galleryImages'] = $gallery_images;
        $data['relatedProducts'] = $related_products;

        if ($product->is_type('simple')) {

            $product_stock_status = $product->get_stock_status();
            $product_in_stock = $product->is_in_stock();
            $product_regular_price = $product->get_regular_price();
            $product_sale_price = $product->get_sale_price();
            $product_price = $product->get_price();

            $data['regularPrice'] = $product_regular_price;
            $data['salePrice'] = $product_sale_price;
            $data['price'] = $product_price;
            $data['inStock'] = $product_in_stock;
            $data['productType'] = 'simple';

        }
        
        wp_send_json(['data' => $data]);
    } else {
        wp_send_json(['error' => 'Product not found'], 404);
    }
    
}



add_filter('post_type_link', 'litografisk_product_post_link', 20, 4);
function litografisk_product_post_link($post_link, $post, $leavename, $sample) {
    if ($post && $post->post_type == "product") {
        $post_link = LitografiskHelper::product_link($post_link);
    }
    
    return $post_link;
}