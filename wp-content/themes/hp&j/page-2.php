<?php 

/*
 * Shop
 *
 */

get_header(); 

// Introduction
$introduction = get_field('introduction');
if ($introduction) : ?>
	<div class="introduction">
		<article class="fixed"><?= $introduction; ?></article>
	</div>
<?php endif; ?>

<section class="shop items loading" data-title="<?php the_title(); ?>">
	<nav class="subnav">
		<a href="#"class="title" data-sort="random">Tilfældig</a>	
		<a href="#"class="title" data-sort="alphabetical">Alfabetisk</a>	
		<a href="#"class="title" data-sort="recent">Nyeste først</a>	
		<a href="#"class="title" data-sort="list">Liste</a>
	</nav>

	<?php 

	// Items
	$args = array(
		'post_type'			=> 'item',
		'posts_per_page'	=> -1,
	);

	$work_query = new WP_Query($args);
	if ($work_query->have_posts()) :
		while ($work_query->have_posts()) : $work_query->the_post(); ?>
	
			<?php get_template_part('part', 'item'); ?>

		<?php endwhile; 
		wp_reset_postdata();
	endif; ?>

	<div class="list">
		<div class="span3 suffix1" id="artist-placeholder"></div>
		<div class="span2">
			<?php // List artists
			$terms_artist = get_terms(array('taxonomy' => 'artist', 'hide_empty' => true,));
			if (!empty($terms_artist) && !is_wp_error($terms_artist)) :
				foreach ($terms_artist as $term) : ?>
					<a href="/kunstner/<?= $term->slug; ?>" class="title" data-id="<?= $term->slug; ?>"><?= $term->name; ?></a>
				<?php endforeach; 
			endif; ?>
		</div>
	</div>

</section>

<?php get_footer(); ?>