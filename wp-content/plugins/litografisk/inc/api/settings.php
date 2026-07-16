<?php

add_action( 'rest_api_init', function() {

    // Product details
    register_rest_route( 'litografisk/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => 'litografisk_get_settings'
        )
    );

});


function litografisk_get_settings() {
    $settings = [
        'currency' => get_woocommerce_currency(),
        'currency_symbol' => get_woocommerce_currency_symbol(),
        'shop_name' => get_bloginfo('name'),
        'shop_description' => get_bloginfo('description'),
        'shop_url' => get_site_url(),
        'site_notification' => get_field('site_notification', 'options'),
        'site' => [
            'phone' => get_field('site_phone', 'options'),
            'email' => get_field('site_email', 'options'),
            'address' => get_field('site_address', 'options'),
            'open_hours' => get_field('site_open_hours', 'options'),
        ],
        'social' => [
            'facebook' => get_field('social_facebook_link', 'options'),
            'instagram' => get_field('social_instagram_link', 'options'),
            'email' => get_field('social_email', 'options'),
        ]
        
    ];

    $menu_slug = 'menu';
    $menu_object = wp_get_nav_menu_object($menu_slug);
    $menu = wp_get_nav_menu_items($menu_object);
    $menu_links = [];

    if ($menu) {
    
        $filtered_menu = array_map(function ($item) {
            
            // Check category if null then remove it
            if ($item->object == "product_cat") {
                $category_term = get_term($item->object_id, 'product_cat');
                if ($category_term->count == 0) {
                    return false;
                }
            }
            return [
                'id' => $item->ID,
                'parent' => $item->menu_item_parent,
                'url' => str_replace(site_url(), '', $item->url),
                'title' => $item->title,
                'children' => [],
            ];
        }, $menu);
        
        
        // Remove menu link is false
        $count_arr = count($filtered_menu);
        for ($i=0; $i<$count_arr; $i++) {
            if ($filtered_menu[$i] === false) {
                unset($filtered_menu[$i]);
            }
        }
     

        $data = [];
        foreach ($filtered_menu as &$menu_item) {
            if ($menu_item['parent'] == 0) {
                $data[$menu_item['id']] = &$menu_item;
            } else {
                foreach ($filtered_menu as &$potential_parent) {
                    if ($menu_item['parent'] == $potential_parent['id']) {
                        $potential_parent['children'][] = &$menu_item;
                    }
                }
            }
        }
    
        foreach ($filtered_menu as $menu_time) {
            if (isset($data[$menu_time['id']])) {
                $menu_links[] = $data[$menu_time['id']];
            }
        }
    }

    $settings['menu'] = $menu_links;

    wp_send_json([
        'data' => $settings
    ]);

}