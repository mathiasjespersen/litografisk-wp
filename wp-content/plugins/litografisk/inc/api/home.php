<?php
add_action('rest_api_init', function () {

    register_rest_route('litografisk/v1', '/home', array(
        'methods' => 'GET',
        'callback' => 'litografisk_get_home'
    ));
    register_rest_route('litografisk/v1', '/gallery', array(
        'methods' => 'GET',
        'callback' => 'litografisk_get_gallery'
    ));
    register_rest_route('litografisk/v1', '/page/(?P<slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'litografisk_get_page'
    ));
});


function litografisk_get_blocks($page_id) {
    $blocks = get_field('content_pagemodules', $page_id);

    $results_blocks = [];
    foreach ($blocks as $key => $block) {
        if ($block['acf_fc_layout'] === 'content_pagemodules_textnote') {
            $data = [
                'id' => $key,
                'type' => 'TextAndNote',
                'left_content' => $block['content_pagemodules_textnote_note'],
                'right_content' => $block['content_pagemodules_textnote_wysiwyg']
            ];

            $results_blocks[] = $data;
        }

        if ($block['acf_fc_layout'] === 'content_pagemodules_image') {
            $data = [
                'id' => $key,
                'type' => 'Image',
                'image' => $block['content_pagemodules_image_image'],
                'width' => $block['content_pagemodules_image_width'],
            ];

            $results_blocks[] = $data;
        }
        if ($block['acf_fc_layout'] === 'content_pagemodules_text') {
            $data = [
                'id' => $key,
                'type' => 'Text',
                'content' => $block['content_pagemodules_text_wysiwyg'],
                'position' => $block['content_pagemodules_text_position'],
            ];

            $results_blocks[] = $data;
        }
    }

    return $results_blocks;
}

function litografisk_get_gallery($request) {
    $gallery_page = get_page_by_path('Galleri');
    $gallery_page_id = $gallery_page ? $gallery_page->ID : 0;

    $galleries = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    $galleries_data = [];
    foreach ($galleries as $gallery) {

        $image = wp_get_attachment_image_src(get_post_thumbnail_id($gallery->ID), 'single-post-thumbnail');

        $galleries_data[] = [
            'id' => $gallery->ID,
            'title' => $gallery->post_title,
            'slug' => $gallery->post_name,
            'image' => [
                'url' => $image ? $image[0] : '',
                'alt' => get_post_meta(get_post_thumbnail_id($gallery->ID), '_wp_attachment_image_alt', true),
                'width' => $image ? $image[1] : '',
                'height' => $image ? $image[2] : '',
            ],
            'link' => get_permalink($gallery->ID),
            'post_date' => get_field('post_date', $gallery->ID),
            'post_time' => get_field('post_time', $gallery->ID),
            'post_excerpt' => get_field('post_excerpt', $gallery->ID),
        ];
    }

    wp_send_json([
        'data' => [
            'title' => $gallery_page->post_title,
            'seo' => [
                'title' => $gallery_page->post_title,
                'description' => get_field('introduction', $gallery_page_id),
            ],
            'introduction' => get_field('introduction', $gallery_page_id),
            'galleries' => $galleries_data,
        ]
    ]);
}

function litografisk_get_page($request) {
    $slug = $request->get_param('slug');
    $page = get_page_by_path($slug);

    if (!$page) {
        return new WP_Error(
            'page_not_found',
            'Page not found.',
            array('status' => 404)
        );
    }

    $results_blocks = litografisk_get_blocks($page->ID);


    wp_send_json([
        'data' =>[
            'seo' => [
                'title' => LitografiskHelper::normalize_text($page->post_title),
                'description' => '',
            ],
            'title' => LitografiskHelper::normalize_text($page->post_title),
            'blocks' => $results_blocks
        ]
    ]);
}


function litografisk_get_home($request) {
    $home_page_id = get_option('page_on_front');
    $home_page = get_post($home_page_id);
    $results_blocks = litografisk_get_blocks($home_page_id);


    wp_send_json([
        'data' =>[
            'title' => $home_page->post_title,
            'seo' => [
                'title' => $home_page->post_title,
                'description' => '',
            ],
            'blocks' => $results_blocks
        ]
    ]);
}
