<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="initial-scale=1" />
	<title><?php global $page, $paged; wp_title( '|', true, 'right' ); bloginfo( 'name' ); echo ' | '; bloginfo( 'description' ); ?></title>

 	<meta name="keywords" content="" />
	<meta name="description" content="På Litografisk producerer og sælger vi grafiske tryk i samarbejde med kunstnere fra den danske og internationale billedkunstscene. Alle er velkomne i vores atelier, showroom og rammeværksted." />
	<meta name="author" content="Litografisk" />
	<meta name="copyright" content="Litografisk" />

	<meta property="og:site_name" content="Litografisk">
	<meta property="og:title" content="Litografisk">
	<meta property="og:description" content="På Litografisk producerer og sælger vi grafiske tryk i samarbejde med kunstnere fra den danske og internationale billedkunstscene. Alle er velkomne i vores atelier, showroom og rammeværksted.">

	<meta name="format-detection" content="telephone=no">
	
	<link rel="preload" href="//cloud.typenetwork.com/projects/2347/fontface.css/" as="style">
	<link href="//cloud.typenetwork.com/projects/2347/fontface.css/" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>?v=1.035" />

	<script type="text/javascript" src="<?php bloginfo( 'template_url' ); ?>/js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="<?php bloginfo( 'template_url' ); ?>/js/modernizr-custom.js"></script>
	
	<?php wp_head(); ?>
	
	<?php if (is_page(33)) :
		$streetnumber = "14";
	else :
		$streetnumber = "13C";
	endif;
	?>

</head>

<body <?php body_class(); ?>>

	<header>
		<nav class="span4">
			<ul>
				<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => '')); ?>
			</ul>
		</nav>
		<address class="span1">
			<p>+45 36172014<br>
			<a href="mailto:info@litografisk.dk">info@litografisk.dk</a></p>
			<p>Valhøjvej <?= $streetnumber; ?><br>
				2500 Valby<br>
				København</p>
		</address>
		<div class="span1 engage">
			<p>Åbningstider<br>Hverdage 9-17</p>
			<p>
				<a href="https://www.facebook.com/litografisk.dk" class="title">Facebook</a>
				<a href="https://www.instagram.com/litografisk/" class="title">Instagram</a>
				<a href="mailto:info@litografisk.dk" class="title">Send mail</a>
			</p>
		</div>
		<div id="title">Litografisk</div>
	</header>