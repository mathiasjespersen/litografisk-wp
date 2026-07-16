<?php
/**
 * Plugin Name: Hostrup-Pedersen & Johansen — Setup
 * Plugin URI: litografisk.dk
 * Description: Setup of content types, customizing of admin and other good stuff
 * Version: 1.0
 * Author: Mathias Jespersen
 * Author URI: m-1.cc
 * License: GPL2
 */


/**********************
 GENERAL ADMIN AND CORE
 **********************/

// Add Login style
function login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/login-styles.css' );
}
add_action( 'login_enqueue_scripts', 'login_stylesheet' );

// Remove admin bar space in frontend
function my_filter_head() {
  remove_action('wp_head', '_admin_bar_bump_cb');
}
//add_action('get_header', 'my_filter_head');

// Disable default dashboard widgets
function disable_default_dashboard_widgets() {
	// remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' );    // Right Now Widget
	remove_meta_box( 'dashboard_activity', 'dashboard', 'core' );    // Activity Widget
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'core' ); // Comments Widget
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );  // Incoming Links Widget
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );         // Plugins Widget
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );   // Quick Press Widget
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );   // Recent Drafts Widget
	remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );         //
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );       //
    remove_meta_box( 'wpe_dify_news_feed', 'dashboard', 'core' ); // WP Engine Blog feed

	// Hide page template box
	//remove_meta_box( 'pageparentdiv', 'page', 'normal' );       //
}
add_action( 'admin_menu', 'disable_default_dashboard_widgets' );

// Remove default "Welcome" message
remove_action('welcome_panel', 'wp_welcome_panel');

// Adjust admin interface
function remove_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
    //$wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
    //$wp_admin_bar->remove_menu('view-site');        // Remove the view site link
    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
    $wp_admin_bar->remove_menu('comments');         // Remove the comments link
    //$wp_admin_bar->remove_menu('new-content');      // Remove the content link
    //$wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
    //$wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

// Remove unnecessary header info
function remove_header_info() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'start_post_rel_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head'); // for WordPress >= 3.0
}
add_action('init', 'remove_header_info');

// Remove unwanted shortcuts to new items in admin bar
function remove_wp_nodes() {
    global $wp_admin_bar;
    //$wp_admin_bar->remove_node( 'new-page' );
    //$wp_admin_bar->remove_node( 'new-post' );
    $wp_admin_bar->remove_node( 'new-link' );
    $wp_admin_bar->remove_node( 'new-media' );
    $wp_admin_bar->remove_node( 'new-user' );
}
add_action('admin_bar_menu', 'remove_wp_nodes', 999);

// Hide "Howdy" in admin bar
function replace_howdy( $wp_admin_bar ) {
    $my_account=$wp_admin_bar->get_node('my-account');
    $newtitle = str_replace( 'Hejsa,', '', $my_account->title );
    $wp_admin_bar->add_node( array(
        'id' => 'my-account',
        'title' => $newtitle,
    ) );
}
add_filter( 'admin_bar_menu', 'replace_howdy', 25 );

// Customize admin footer text
function custom_admin_footer() {
        echo '';
}
add_filter('admin_footer_text', 'custom_admin_footer');

// Add page Slug Body Class
function add_slug_body_class($classes) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = $post->post_type . '-' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );



/**************
 CONTENT
 **************/

// Disable default content types
// Disable support for comments and trackbacks in post types
function disable_post_types() {
	$post_types = get_post_types();
	foreach ($post_types as $post_type) {
		if(post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
}
add_action('admin_init', 'disable_post_types');

// Remove items in menu
function disable_admin_menu_items() {
    remove_menu_page('edit-comments.php');
    //remove_menu_page('edit.php');
	//remove_menu_page('tools.php');
}
add_action('admin_menu', 'disable_admin_menu_items');

// Redirect any user trying to access comments page
function disable_admin_menu_redirect() {
	global $pagenow;
	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url()); exit;
	}
}
add_action('admin_init', 'disable_admin_menu_redirect');

// Customize order of admin menu items
function loadMenuStructure() {
    return array('index.php', 'separator1', 'edit.php?post_type=item', 'edit.php?post_type=page', 'edit.php', 'upload.php', 'separator2' );
}
function allowMenuStructure() {
    return true;
}
add_filter("custom_menu_order", "allowMenuStructure");
add_filter("menu_order", "loadMenuStructure");


/*********************
 CUSTOM CONTENT TYPES
 *********************/


// Content type: Items
function custom_content_type_item() {
    $labels = array(
        'name'               => 'Værker',
        'singular_name'      => 'Værk',
        'add_new'            => 'Tilføj værk',
        'add_new_item'       => 'Tilføj nyt værk',
        'edit_item'          => 'Rediger',
        'new_item'           => 'Nyt værk',
        'all_items'          => 'Alle værker',
        'view_item'          => 'Vis alle værker',
        'search_items'       => 'Find værk',
        'not_found'          => 'Ingen værker fundet',
        'not_found_in_trash' => 'Ingen værker fundet',
        'parent_item_colon'  => '',
        'menu_name'          => 'Værker'
    );
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-format-image',
        'query_var'          => true,
        'rewrite'            => array('slug' => 'item'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => array('title', 'editor', 'thumbnail', 'revisions'),
    );
    register_post_type('item', $args );
}
add_action( 'init', 'custom_content_type_item' );

// Add custom post type to the 'At a glance' dashboard
function custom_glance_items( $items = array() ) {
    $post_types = array('items');    
    foreach( $post_types as $type ) {
        if( ! post_type_exists( $type ) ) continue;
        $num_posts = wp_count_posts( $type );
        
        if( $num_posts ) {
            
            $published = intval( $num_posts->publish );
            $post_type = get_post_type_object( $type );
            
            $text = _n( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $published, 'your_textdomain' );
            $text = sprintf( $text, number_format_i18n( $published ) );
            
            if ( current_user_can( $post_type->cap->edit_posts ) ) {
                $items[] = sprintf( '<a class="%1$s-count" href="edit.php?post_type=%1$s">%2$s</a>', $type, $text ) . "\n";
            } else {
                $items[] = sprintf( '<span class="%1$s-count">%2$s</span>', $type, $text ) . "\n";
            }
        }
    }
    return $items;
}
add_filter( 'dashboard_glance_items', 'custom_glance_items', 10, 1 );


// Add custom drawn icon if needed
function add_menu_icons_styles(){
	echo '<style>#adminmenu .menu-icon-project div.wp-menu-image:before { content: "\f480"; }</style>';
}
//add_action( 'admin_head', 'add_menu_icons_styles' );

// Custom taxonomy
function taxonomy_artist() {
  $labels = array(
    'name'              => 'Kunstnere',
    'singular_name'     => 'Kunstner',
    'search_items'      => 'Søg kunstner',
    'all_items'         => 'Alle kunstnere',
    'edit_item'         => 'Rediger kunstner',
    'update_item'       => 'Opdatér kunstner',
    'add_new_item'      => 'Tilføj ny kunstner',
    'new_item_name'     => 'Ny event kunstner',
    'menu_name'         => 'Kunstnere',
  );
  register_taxonomy('artist', array('item'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'kunstner', 'with_front' => false),
  ));
}
add_action('init', 'taxonomy_artist');




// Add featured thumbnail to admin post columns for "Værker"
function add_thumbnail_columns_item($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
     //   'featured_thumb' => '',
        'title' => 'Titel',
        'artist' => 'Kunstner',
        //'start_date' => __( 'Udstillingsperiode'),
        //'date' => 'Dato'
    );
    return $columns;
}
function add_thumbnail_columns_item_data($column, $post_id) {
    switch ($column) {
        case 'artist' :
            $terms = get_the_terms($post_id,'artist');
            if (!empty($terms)) {
                $out = array();
                foreach ($terms as $term) {
                    $out[] = sprintf( '<a href="%s">%s</a>',
                        esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'artist' => $term->slug ), 'edit.php' ) ),
                        esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'artist', 'display' ) )
                    );
                }
                echo join( ', ', $out );
            }
            else {
                _e( 'No Articles' );
            }
            break;
    }
}


// Add custom listings to admin post columns for "Events"
function add_gallery_columns($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'featured_thumb' => '',
        'title' => 'Titel',
    //    'post_date' => __('Arrangementsdato'),
        'date' => 'Dato'
    );
    return $columns;
}
function add_gallery_columns_data($column, $post_id) {
    switch ($column) {
        case 'featured_thumb' :
            echo '<a href="' . get_edit_post_link() . '">';
            echo the_post_thumbnail(array(50,50));
            echo '</a>';
            break;
        case 'post_date' :
            $date_start = get_field('post_date', false, false);
            $date_start = new DateTime($date_start);
            echo $date_start->format('d-m-Y');
            break;
    }
}
function custom_content_gallery_columns_sorting($columns) {
    $columns['post_date'] = 'post_date';
  return $columns;
}

function start_date_orderby($query) {
    if (!is_admin())
        return;
 
    $orderby = $query->get('orderby');
 
    if('post_date' == $orderby) {
        $query->set('meta_key','post_date');
        $query->set('orderby','meta_value');
    }
}

if (function_exists('add_theme_support')) {
    add_filter('manage_posts_columns', 'add_gallery_columns');
    add_action('manage_posts_custom_column', 'add_gallery_columns_data', 10, 2);
    add_action('manage_posts-sortable_columns', 'custom_content_gallery_columns_sorting', 10, 2);

    add_filter('manage_item_posts_columns', 'add_thumbnail_columns_item');
    add_action('manage_item_posts_custom_column', 'add_thumbnail_columns_item_data', 10, 2);

    add_action('pre_get_posts', 'start_date_orderby', 10);
}



/**************
 EDITOR
 **************/

// Add custom stylesheet to the website front-end with hook 'wp_enqueue_scripts'
function custom_editor_styles() {
    add_editor_style( 'editor-styles.css' );
}
add_action( 'init', 'custom_editor_styles' );

// Hide default content area media button
function z_remove_media_controls() {
     remove_action( 'media_buttons', 'media_buttons' );
}
add_action('admin_head','z_remove_media_controls');

function custom_mce_before_init( $settings ) {
    $style_formats = array(
        array(
            'title' => 'Link (download)',
            'selector' => 'a',
            'classes' => 'arrow down',
        ),
        array(
            'title' => 'Link (side)',
            'selector' => 'a',
            'classes' => 'arrow',
        ),
        array(
            'title' => 'Note',
            'selector' => 'p',
            'classes' => 'note',
        ),
        array(
            'title' => 'Opstilling med indryk',
            'inline' => 'span',
            'classes' => 'indent',
        ),
        array(
            'title' => 'Tabel: Indryk højre',
            'selector' => 'table',
            'classes' => 'indent-right',
        )
    );
    $settings['style_formats'] = json_encode( $style_formats );
    return $settings;
}
//add_filter( 'tiny_mce_before_init', 'custom_mce_before_init' );

// Custom TinyMCE
function format_TinyMCE($in) {
    $in['paste_remove_styles'] = true;
    $in['paste_remove_spans'] = true;
    $in['toolbar1'] = 'formatselect, alignleft, aligncenter, alignright, bold, italic, bullist, numlist, blockquote, link, unlink, pastetext, removeformat';
    $in['toolbar2'] = '';
    return $in;
}
add_filter('tiny_mce_before_init', 'format_TinyMCE');

function format_TinyMCE_ACF($toolbars) {
    unset($toolbars['Basic']);

    $toolbars['Basic' ] = array();
    $toolbars['Basic' ][1] = array('formatselect', 'alignleft', 'aligncenter', 'alignright', 'bold', 'italic', 'bullist', 'numlist', 'blockquote', 'link', 'unlink', 'pastetext', 'removeformat');

    return $toolbars;
}
add_filter('acf/fields/wysiwyg/toolbars' , 'format_TinyMCE_ACF');

?>