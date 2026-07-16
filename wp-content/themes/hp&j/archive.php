<?php 

$term = get_queried_object();

get_header(); ?>

<section class="artist-items" data-title="<?= $term->name; ?>">

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<article data-timestamp="<?= get_post_time('U', true); ?>" data-id="<?= $term->slug; ?>">
			<div class="span4">
				<?php // Image thumbnail
				if (get_the_post_thumbnail()) : 

					$filetype = get_post_mime_type( get_post_thumbnail_id($post->ID)); 
					$image_title = $term->name . ' ' . wp_strip_all_tags( get_the_content()); 
					?>

					<figure>
						<?php if ($filetype == 'image/gif') {
							the_post_thumbnail('full');
						} else {
							//the_post_thumbnail('large', array( 'title' => preg_replace( "/\r|\n/", " ", $image_title)));
						} ?>
					</figure>

				<?php endif; ?>
			</div>
			<div class="span2">			
				<h2><?php the_title(); ?></h2>
				<div class="artist">
					<span><?= $term->name; ?></span>
				</div>
				<div class="info">
					<?php the_content(); ?>
				</div>
				<div class="info">
					<a href="#" id="buy">Køb</a>
				</div>
			</div>
		</article>


		<?php endwhile; 
	endif; ?>
	
</section>

<?php get_footer(); ?>