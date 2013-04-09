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
 
 
 

/**
 * Display search form.
 *
 * Will first attempt to locate the searchform.php file in either the child or
 * the parent, then load it. If it doesn't exist, then the default search form
 * will be displayed. The default search form is HTML, which will be displayed.
 * There is a filter applied to the search form HTML in order to edit or replace
 * it. The filter is 'get_search_form'.
 *
 * This function is primarily used by themes which want to hardcode the search
 * form into the sidebar and also by the search widget in WordPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'get_search_form'. This can be useful for outputting JavaScript that the
 * search relies on or various formatting that applies to the beginning of the
 * search. To give a few examples of what it can be used for.
 *
 * @since 2.7.0
 * @param boolean $echo Default to echo and not return the form.
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
	
	<?php if ( have_posts() ) : 
	
		if ( $content_ajax == false ) :?>

			<header class="page-header">
				<h1 class="page-title">
					<?php if ( is_day() ) : ?>
						<?php printf( __( 'Daily Archives: %s', 'ct' ), '<span>' . get_the_date() . '</span>' ); ?>
					<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Monthly Archives: %s', 'ct' ), '<span>' . get_the_date( 'F Y' ) . '</span>' ); ?>
					<?php elseif ( is_year() ) : ?>
						<?php printf( __( 'Yearly Archives: %s', 'ct' ), '<span>' . get_the_date( 'Y' ) . '</span>' ); ?>
					<?php else : ?>
						<?php _e( 'Search Results', 'ct' ); ?>
					<?php endif; ?>
				</h1>
			</header>
		
		<?php endif; ?>


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
		endif; 
		
	if ( $content_ajax == false ):
		?>

	</div><!-- #content -->

<?php 
	get_footer(); 
	endif;
?>