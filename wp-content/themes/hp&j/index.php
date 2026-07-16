<?php 

/*
 * Galleri
 *
 */

get_header(); 

// Introduction
$introduction = get_field('introduction', 41);
if ($introduction) : ?>
	<div class="introduction">
		<article class="fixed"><?= $introduction; ?></article>
	</div>
<?php endif; ?>

<section data-title="Galleri">

	<?php 

	// Items
	$args = array(
		'post_type'			=> 'post',
		'posts_per_page'	=> -1,
		'order' 			=> 'DESC',
        'orderby' 			=> 'meta_value',
        'meta_key' 			=> 'post_date',
     //    'meta_query' => array(
     //        'key' => 'event_date_start',
     //        'compare' => '<=',
     //        'value' => date('Y-m-d'),
    	// ),		
	);

	$post_query = new WP_Query($args);
	if ($post_query->have_posts()) :
		while ($post_query->have_posts()) : $post_query->the_post(); ?>
	
			<?php get_template_part('part', 'gallery'); ?>

		<?php endwhile; 
		wp_reset_postdata();
	endif; ?>

</section>

<?php get_footer(); ?>