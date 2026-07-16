<?php

//Remove CORS headers for REST API that allow arbitrary origins
add_action( 'rest_api_init', function($var){
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
}, 15, 1 );

add_action( 'init', 'handle_cors_for_wc_api' );
function handle_cors_for_wc_api() {
    // Allow from any origin (Change * to your specific Shopee/Frontend domain for better security)
    header( "Access-Control-Allow-Origin: *" );
    header( "Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE" );
    header( "Access-Control-Allow-Credentials: true" );
    header( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization' );
}

add_action('send_headers', 'handle_cors_for_wc_api');
add_action( 'rest_api_init', function() {
    header( "Access-Control-Allow-Origin: *" );
});

add_filter( 'allowed_http_origin', function( $origin ) {
    return true;
} );




/**
 * Enable featured image (Product Thumbnail) support in WooCommerce theme.
 */
add_action('after_setup_theme', function () {

    // Enable WordPress featured images.
    add_theme_support('post-thumbnails');

    // Enable WooCommerce support.
    add_theme_support('woocommerce');
});

add_action('acf/init', function () {

    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => 'Theme Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug'  => 'theme-settings',
            'capability' => 'edit_posts',
            'redirect'   => false,
            'position'   => 60,
            'icon_url'   => 'dashicons-admin-generic',
        ]);

    }

});

// Filter to add a new JSON load path
add_filter('acf/settings/load_json', 'lito_json_load_point');

function lito_json_load_point($paths) {
    $paths[] = plugin_dir_path(__FILE__) . 'acf-json';
    
    return $paths;
}

// Only alter the save path if it matches your specific plugin field group key
add_action('acf/update_field_group', 'lito_save_specific_group', 1, 1);
function lito_save_specific_group($group) {
    $my_plugin_groups = array('group_6a4f44e92a4eb'); 

    if (in_array($group['key'], $my_plugin_groups)) {
        add_filter('acf/settings/save_json', function() {
            return plugin_dir_path( __FILE__ ) . 'acf-json';
        });
    }
}