<?php 

$title = get_the_title();

if ($post->ID == 55) {
	$title = 'Hostrup-Pedersen &&nbsp;Johansen';
}

get_header(); ?>

<section class="page section" data-title="<?= $title; ?>">
	
	<?php while (have_posts()) : the_post(); ?>

		<?php while(has_sub_field('content_pagemodules')):

			// layout: Text
			if (get_row_layout() == 'content_pagemodules_text') :
				get_template_part('modules/pagemodule', 'text');

			// layout: Textnote
			elseif (get_row_layout() == 'content_pagemodules_textnote') :
				get_template_part('modules/pagemodule', 'textnote');

			// layout: Image
			elseif(get_row_layout() == 'content_pagemodules_image'):
				get_template_part('modules/pagemodule', 'image');

			// layout: product
			elseif(get_row_layout() == 'content_pagemodules_product'):
				get_template_part('modules/pagemodule', 'product');

			endif;
		endwhile; ?>

	<?php endwhile; ?>

</section>

<?php get_footer(); ?>
