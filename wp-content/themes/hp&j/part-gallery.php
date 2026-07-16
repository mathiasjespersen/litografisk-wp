<div class="module section gallery">
	<figure class="image span4" data-url="<?php the_permalink(); ?>">

		<?php // Image thumbnail
		if (get_the_post_thumbnail()) :
			$filetype = get_post_mime_type( get_post_thumbnail_id($post->ID ));
			if ($filetype == 'image/gif') :
				the_post_thumbnail('full');
			else :
				the_post_thumbnail('large');
			endif;
		endif; ?>
	
	</figure>
	<div class="span2">
		<a href="<?php the_permalink(); ?>" class="title nowrap" data-title="<?php the_title(); ?>">
			<div class="header">
				<h2><?php the_title(); ?></h2>
				<?php if (get_field('post_date')) : ?>
					<div class="date"><?php the_field('post_date'); ?></div>
				<?php endif; ?>
				<?php if (get_field('post_time')) : ?>
					<?php the_field('post_time'); ?>
				<?php endif; ?>
			</div>
			<?php the_field('post_excerpt'); ?>
		</a>
	</div>
</div>

