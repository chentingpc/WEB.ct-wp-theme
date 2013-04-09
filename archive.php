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
	
	<?php
		$display_count = 0;
	?>
	
	<?php if ( $content_ajax == false ): ?>
		<header class="page-header">
			<h1 class="page-title">
				<?php if ( is_day() ) : ?>
					<?php printf( __( 'Daily Archives: %s', 'ct' ), '<span>' . get_the_date() . '</span>' ); ?>
				<?php elseif ( is_month() ) : ?>
					<?php printf( __( 'Monthly Archives: %s', 'ct' ), '<span>' . get_the_date( 'F Y' ) . '</span>' ); ?>
				<?php elseif ( is_year() ) : ?>
					<?php printf( __( 'Yearly Archives: %s', 'ct' ), '<span>' . get_the_date( 'Y' ) . '</span>' ); ?>
				<?php elseif ( is_search() ): ?>
					<?php _e( 'Search Results', 'ct' ); ?>
				<?php else : ?>
					<?php _e( 'Blog Archives', 'ct' ); ?>
				<?php endif; ?>
			</h1>
		</header>
	<?php endif;?>
	
	<?php if ( have_posts() ) : ?>

		<?php /* Start the Loop */ ?>
		<?php
			$view = get_view();
			if ( $view == 'cat' ){
				global $date_counter_cat;
				$date_counter_cat = '0';	
				
				global $quality_show;
				global $quality_show_a;
				
				for ($i = 1; $i <= 5; $i ++ ) {
				if ( $i == $quality )
					$quality_show_final .= $quality_show[$i-1] . ' ';
				else
					$quality_show_final .= $quality_show_a[$i-1] . ' ';
				}
				
				$quality_show_final = '<div class="quality_filter">Quality Filter &nbsp;&nbsp;' . $quality_show_final . '</div>';
			}

			while ( have_posts() ) : 
				the_post(); 
				global $my_ct_post_filter;
				$post_filter = $my_ct_post_filter->front_post_fileter_main();
				if ( $post_filter == true )
					continue;
				$display_count += 1;
				if ( $display_count == 1 )
					echo $quality_show_final;
				get_template_part( 'content-archive', get_post_format() );
			endwhile; 
		?>


	<?php else : 
			$display_count = -1;
			get_nothing_found();
		endif; ?>
		
	<?php 
		if ( $display_count == 0 )
			get_nothing_found();
	?>

	<?php if ( $content_ajax == false ): ?>
	</div><!-- #content -->

<?php 
	get_footer(); 
	endif;
?>