<?php
/*
 * Module: Text
 *
 */

$text_position = get_sub_field('content_pagemodules_text_position');

?>

<div class="span4 module columns <?php echo $text_position . '2'; ?>">
	<?php the_sub_field('content_pagemodules_text_wysiwyg'); ?>
</div>