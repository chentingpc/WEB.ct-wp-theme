<?php
/**
* Home for theme ct
*/

	get_header();
?>
<div id="content">
	<?php
	get_sidebar();
	?>
	<?php
	if (have_posts()) :
		the_post();
		global $my_ct_post_filter;
		$post_filter = $my_ct_post_filter->front_post_fileter_main();
		if ( $post_filter == true )
			continue;
		get_template_part( 'content-single', get_post_format() );
		comments_template( '', true );
	endif;
	?>
</div>
<?php
	get_footer(); 
?>