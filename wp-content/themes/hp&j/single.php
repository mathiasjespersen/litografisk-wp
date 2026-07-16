<?php get_header(); ?>

<section data-title="<?php the_title(); ?>">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<article class="section">
			<div class="span2 header">
				<h2><?php the_title(); ?></h2>
				<?php if (get_field('post_date')) : ?>
					<div class="date"><?php the_field('post_date'); ?></div>
				<?php endif; ?>
				<?php if (get_field('post_time')) : ?>
					<?php the_field('post_time'); ?>
				<?php endif; ?>
			</div>
			<div class="span4">
				<div class="columns module">
					<?php the_content(); ?>
				</div>
				
				<div class="gallery-images">
				<?php 
				$images = get_field('post_gallery');
				if ($images) :
					$i = 1;
					foreach($images as $image) : ?>
						<figure>
							<img src="<?= $image['url']; ?>" alt="<?= $image['alt']; ?>" data-src="<?= $image['url']; ?>" data-number="<?= $i; ?>"/>
						</figure>
						<?php $i++;
					endforeach; 
				endif;
				?>
				</div>
			</div>
		</article>

	<?php endwhile; ?>		
	<?php endif; ?>		
</section>

<?php get_footer(); ?>