<?php
/**
* Home for theme ct
*/

	global $content_ajax;
	get_header();
	if ( $content_ajax == false ):
?>

	<div id="content">
	<?php
	endif;
	
	get_sidebar();
	?>
	<?php
	$display_count = 0;
	
	if (have_posts()) :
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
		
		while (have_posts()) :
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
	else:
		$display_count = -1;
		get_nothing_found();
	endif;
	
	if ( $display_count == 0 )
		get_nothing_found();
	
	
	if ( $content_ajax == false ):
	?>
	</div>
<?php
	endif;
	get_footer(); 
?>