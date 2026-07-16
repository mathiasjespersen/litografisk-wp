<?php
/*
 * Module: Product
 *
 */

$title = get_sub_field('content_pagemodules_product_title'); 
$image = get_sub_field('content_pagemodules_product_image'); 
$text = get_sub_field('content_pagemodules_product_text'); 

?>

<div class="module section product">
	<figure class="image span4">
		<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
		<?php if ($image['caption']) : ?>
		<figcaption>
			<p><?php echo $image['caption']; ?></p>
		</figcaption>
		<?php endif; ?>
	</figure>
	<div class="span2">
		<h2><?= $title; ?></h2>
		<div><?= $text; ?></div>
	</div>
</div>