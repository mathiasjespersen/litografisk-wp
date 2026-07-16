<?php
add_action('rest_api_init', function () {

    register_rest_route('litografisk/v1', '/shop/artist/(?P<artist_slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'litografisk_get_products_artist',
        'args' => array(
            'artist_slug' => array(
                'required' => true,
                'validate_callback' => function ($param) {
                    return is_string($param);
                }
            ),
        )
    ));
});

function litografisk_get_products_artist($request) {

    $artist_slug = $request['artist_slug'];
    $page = $request->get_param('paged') ? intval($request->get_param('paged')) : 1;
    $per_page = $request->get_param('limit') ? intval($request->get_param('limit')) : 12;
    $args = [
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'tax_query'      => array(
            array(
                'taxonomy' => 'pa_kunstnere',
                'field' => 'slug',
                'terms' => $artist_slug,
            ),
        ),
    ];

    $getposts = new WP_Query($args);
    $data = [];

    if ($getposts->have_posts()) {
        while ($getposts->have_posts()) {
            $getposts->the_post();
            $item = $getposts->post;
            $product = wc_get_product($item->ID);

            $data[] = LitografiskHelper::prepare_product_data($product);
        }
        wp_reset_postdata();
        wp_send_json([
            'data' => $data,
            'paging' => [
                'currentPage' => $page,
                'limit' => $per_page,
                'totalPages' => $getposts->max_num_pages,
                'totalItems' => $getposts->found_posts,
            ]
        ]);
    } else {
        wp_send_json(['error' => 'artist not found'], 404);
    }
}
