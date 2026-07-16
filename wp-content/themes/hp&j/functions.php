<?php

// Flush rewrites
// global $wp_rewrite;
// $wp_rewrite->flush_rules();


// Disable Gutenberg editor
add_filter('use_block_editor_for_post_type', '__return_false', 10);



if ( ! current_user_can( 'activate_plugins' ) ) {
	function remove_core_updates(){
	global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
	}
	add_filter('pre_site_transient_update_core','remove_core_updates');
	add_filter('pre_site_transient_update_plugins','remove_core_updates');
	add_filter('pre_site_transient_update_themes','remove_core_updates');
}

// Add editor the privilege to edit theme
$role_object = get_role( 'editor' );
$role_object->add_cap( 'edit_theme_options' );

// Update CSS within in Admin
function admin_style() {
  wp_enqueue_style('admin-styles', get_template_directory_uri().'/admin-styles.css');
}
add_action('admin_enqueue_scripts', 'admin_style');

// Add editor styles
function editor_styles() {
    add_editor_style('editor-styles.css');
}
add_action('admin_init', 'editor_styles');

// Enable thumbnails on posts
add_theme_support('post-thumbnails', array('item', 'post')); 

// Register menu
register_nav_menus(array(
    'primary'  => __('Menu'),
));

// Send mails via the WP AJAX hook (js)
function sendOrderToMail() {

    $fullname = ($_POST['fullname']);
    $email = ($_POST['email']);
    $address = ($_POST['address']);
    $cvr = ($_POST['cvr']);
    $item = ($_POST['item']);

    // Email defined in backend
    $sendto = get_field('shop_sendtoemail', 2);
    if ($sendto == '') {
    	$to = 'mathiasjespersen@gmail.com';
    } else {
    	$to = $sendto;
    }

	$subject = 'Bestilling af værk';

	$body = 'Navn: ' . $fullname . '<br>';
	$body .= 'Email: ' . $email . '<br>';
	$body .= 'Addresse: ' . $address . '<br>';
	if ($cvr != '') {
		$body .= 'CVR: ' . $cvr . '<br>';
	}
	$body .= 'Værk: ' . $item;

	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	$headers[] = 'From: Litografisk Website <mailer@litografisk.dk>';
	$headers[] = 'Bcc: mathiasjespersen@gmail.com';
	$headers[] = 'Reply-To: ' . $fullname . ' <' . $email . '>';

	//If title not defined
	// if ($item == '') {
	// 	return false;
	// }
	
	wp_mail($to, $subject, $body, $headers);

}
add_action('wp_ajax_nopriv_order_form', 'sendOrderToMail');
add_action('wp_ajax_order_form', 'sendOrderToMail');