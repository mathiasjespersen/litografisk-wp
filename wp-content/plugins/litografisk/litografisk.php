<?php
/**
 * Plugin Name: Litografisk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'inc/rest-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/helpers.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/migrate-products.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/api/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/api/artists.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/api/home.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/api/shop.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/api/product-detail.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/api/products-artist.php';