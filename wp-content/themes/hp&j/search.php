<?php
/**
 * The template for displaying Search Results pages.
 */

get_header(); ?>

	<div class="regular-page list">

	<?php if ( have_posts() ) : ?>
	
    <h1 class="page-title">You've searched for: "<?php echo get_search_query(); ?>"</h1>

    <?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part('template', 'list'); ?>
    <?php endwhile; ?>
	
	<?php else : ?>
	
    <h1 class="page-title">No hit's on "<?php echo get_search_query(); ?>", try again:</h1>
		<?php get_search_form(); ?>
		
	<?php endif; ?>

	</div>

<?php get_footer(); ?>