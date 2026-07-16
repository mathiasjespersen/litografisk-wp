<?php
add_action('rest_api_init', function () {

    register_rest_route('litografisk/v1', '/artists', array(
        'methods' => 'GET',
        'callback' => 'litografisk_get_artists'
    ));
});

function litografisk_get_artists() {

    $artists = get_terms(array(
        'taxonomy' => 'pa_kunstnere',
        'hide_empty' => true,
    ));

    $results = array_map(function ($artist) {

        $first_product = get_posts(array(
            'post_type' => 'product',
            'tax_query' => array(
                array(
                    'taxonomy' => 'pa_kunstnere',
                    'field' => 'slug',
                    'terms' => $artist->slug,
                ),
            ),
            'posts_per_page' => 1,
        ));

        $image = wp_get_attachment_image_src(get_post_thumbnail_id($first_product ? $first_product[0]->ID : 0), 'single-post-thumbnail');
        $product_image = $image ? $image[0] : '';
        $product_image_width = $image ? $image[1] : '';
        $product_image_height = $image ? $image[2] : '';

        return [
            'id' => $artist->term_id,
            'name' => $artist->name,
            'slug' => $artist->slug,
            'first_product' => $first_product ? $first_product[0] : null,
            'first_product_image_url' => $product_image,
            'first_product_image_width' => $product_image_width,
            'first_product_image_height' => $product_image_height
        ];
    }, $artists);

    return $results;
}
