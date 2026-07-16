<?php
add_action('rest_api_init', function () {

    register_rest_route('litografisk/v1', '/shop', array(
        'methods' => 'GET',
        'callback' => 'litografisk_get_products'
    ));
});

function litografisk_get_products($request) {

    $page = $request->get_param('paged') ? intval($request->get_param('paged')) : 1;
    $per_page = $request->get_param('limit') ? intval($request->get_param('limit')) : 10;
    $args = [
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'posts_per_page' => $per_page,
        'paged' => $page
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

        // Get shop introduction from the shop page
        $shop_page_id = wc_get_page_id('shop');
        $shop_introduction = "";
        if ($shop_page_id) {
            $shop_introduction = get_field('introduction', $shop_page_id);
        }

        wp_send_json([
            'data' => $data,
            'seo' => [
                'title' => get_the_title($shop_page_id),
                'description' => $shop_introduction,
            ],
            'content' => [
                'introduction' => $shop_introduction,
                'artists' => litografisk_get_artists()
            ],
            'paging' => [
                'currentPage' => $page,
                'limit' => $per_page,
                'totalPages' => $getposts->max_num_pages,
                'totalItems' => $getposts->found_posts,
            ]
        ]);
    } else {
        wp_send_json(['error' => 'products not found'], 404);
    }
}
