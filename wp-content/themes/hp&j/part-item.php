<?php 

$item_artist = get_field('item_artist');
if ($item_artist) :
	foreach($item_artist as $artist) :
		$artist_name = $artist->name;
		$artist_id = $artist->slug;
	endforeach;
endif;
?>

<article class="item" data-timestamp="<?= get_post_time('U', true); ?>" data-id="<?= $artist_id; ?>">
	<a href="/kunstner/<?= $artist_id; ?>" class="title nowrap" data-title="<?= $artist_name; ?>">
		<?php // Image thumbnail
		if (get_the_post_thumbnail()) :

			$filetype = get_post_mime_type( get_post_thumbnail_id($post->ID )); 
			$image_title = get_the_title() . ' ' . $artist_name . ' ' . wp_strip_all_tags( get_the_content());

			?>
			<figure>
				<?php if ($filetype == 'image/gif') {
					the_post_thumbnail('full');
				} else {
					//the_post_thumbnail('medium');
					$preload_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium' );
					$url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large' );
					echo '<img src="' . $preload_url[0] . '" data-src="' . $url[0] . '" class="preload" title="' . preg_replace( "/\r|\n/", " ", $image_title) . '" alt=""/>';
				} ?>
			</figure>
		<?php endif; ?>	
		<h2><?php the_title(); ?></h2>
		<?php  ?>
			<div class="artist<?= count($item_artist) > 1 ? ' several' : '' ?>">
				<span><?= $artist_name; ?></span>
			</div>
		<div class="info">
			<?php the_content(); ?>
		</div>
	</a>
</article>
