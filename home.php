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
	if (have_posts()) :
		while (have_posts()) :
			the_post();
			global $my_ct_post_filter;
			$post_filter = $my_ct_post_filter->front_post_fileter_main();
			if ( $post_filter == true )
				continue;
			get_template_part( 'content', get_post_format() );
	   endwhile;
	else:
		get_nothing_found();
	endif;
	
	if ( $content_ajax == false ):
	?>
	</div>
<?php
	endif;
	get_footer(); 
?>