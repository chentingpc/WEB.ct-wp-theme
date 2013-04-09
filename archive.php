<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
 
	global $content_ajax;
	
	get_header(); 
	
	if ( $content_ajax == false ):
?>

	<div id="content">

	<?php 
	get_sidebar(); 
	endif;
	?>
	
	<?php if ( have_posts() ) : ?>

		<?php if ( $content_ajax == false ): ?>
			<header class="page-header">
				<h1 class="page-title">
					<?php if ( is_day() ) : ?>
						<?php printf( __( 'Daily Archives: %s', 'ct' ), '<span>' . get_the_date() . '</span>' ); ?>
					<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Monthly Archives: %s', 'ct' ), '<span>' . get_the_date( 'F Y' ) . '</span>' ); ?>
					<?php elseif ( is_year() ) : ?>
						<?php printf( __( 'Yearly Archives: %s', 'ct' ), '<span>' . get_the_date( 'Y' ) . '</span>' ); ?>
					<?php else : ?>
						<?php _e( 'Blog Archives', 'ct' ); ?>
					<?php endif; ?>
				</h1>
			</header>
		<?php endif;?>

		<?php /* Start the Loop */ ?>
		<?php while ( have_posts() ) : 
				the_post(); 
				global $my_ct_post_filter;
				$post_filter = $my_ct_post_filter->front_post_fileter_main();
				if ( $post_filter == true )
					continue;
		?>

			<?php
				/* Include the Post-Format-specific template for the content.
				 * If you want to overload this in a child theme then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'content-archive', get_post_format() );
			?>

		<?php endwhile; ?>


	<?php else : 
			get_nothing_found();
		endif; ?>

	<?php if ( $content_ajax == false ): ?>
	</div><!-- #content -->

<?php 
	get_footer(); 
	endif;
?>