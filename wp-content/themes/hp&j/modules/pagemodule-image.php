<?php
/*
 * Module: Image
 *
 */

$image = get_sub_field('content_pagemodules_image_image'); 
$imageWidth = get_sub_field('content_pagemodules_image_width'); 

// Convert radiobutton options to classes
if ($imageWidth == 'span4suffix2') {
	$imageWidth = 'span4 suffix2';
} elseif ($imageWidth == 'span4prefix2') {
	$imageWidth = 'span4 prefix2';
} ?>

<div class="module <?= $imageWidth; ?>">
	<figure class="image">
		<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
		<?php if ($image['caption']) : ?>
		<figcaption>
			<p><?php echo $image['caption']; ?></p>
		</figcaption>
		<?php endif; ?>
	</figure>
</div>