<?php
/**
 * @package ct
 *
 * side bar for the theme.
 *
 */
 
function ct_get_search_form($echo = true, $choice = 0) {
	do_action( 'get_search_form' );

	$search_form_template = locate_template('searchform.php');
	if ( '' != $search_form_template ) {
		require($search_form_template);
		return;
	}
	
	if ( $choice == 0):
		$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
		<div id="ss"><label class="screen-reader-text" for="s">' . __('Search') . '</label>
		<input type="text" value="' . get_search_query() . '" name="s" id="s" />
		</div>
		</form>';
	else:
		$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
		<div id="ss">
		<input type="text" value="' . get_search_query() . '" name="s" id="s" />
		<input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
		</div>
		</form>';
	endif;
		
	if ( $echo )
		echo apply_filters('get_search_form', $form);
	else
		return apply_filters('get_search_form', $form);
}

	global $content_ajax;
	global $wp;
	global $url_view;
	global $quality;
	global $quality_show;
	global $quality_show_a;
	if ( $content_ajax == false ):
?>
	<div id="sidebar" role="complementary">
		<div id="sdie_header">
			<a href="<?php echo home_url();?>/"><?php bloginfo('name'); ?></a>	
		</div>
		<?php if ( ! is_single() ) {
		global $wp;
		$url = $_SERVER['REQUEST_URI'];
		if ( isset($wp->extra_query_vars['view']) and $wp->extra_query_vars['view'] == 'cat') {
			$view = 'cat';
			$url_view = str_replace('view=cat', 'view=all', $url);
		}
		else {
			$view = 'all';
			if ( ! isset($wp->extra_query_vars['view']) ) {
				if ( strpos($url, '?') != null )
					$url_view = $url . '&view=cat';
				else if ( $url[strlen($url)-1] == '/')
					$url_view = $url . '?view=cat';
				else
					$url_view = $url . '/?view=cat';
			}
			else {
				$url_view = str_replace('view=all', 'view=cat', $url);
			}
		}
		if ( isset($wp->extra_query_vars['quality']) ) {
			$quality = $wp->extra_query_vars['quality'];
			for ( $i = 1; $i <= 5; $i ++ ) {
				$url_quality[$i] = preg_replace('/quality=\d+/', 'quality='.$i, $url);
			}
		}
		else {
			$quality = DEFAULT_QUALITY;
			if ( strpos($url, '?') != null ) {
				$url_quality_temp = '/' . trim($url, '/');
				$url_quality_temp .= '&quality=';
			}
			else if ( $url[strlen($url)-1] == '/' )
				$url_quality_temp = $url . '?quality=';
			else
				$url_quality_temp = $url . '/?quality=';
			for ($i = 1; $i <= 5; $i ++)
				$url_quality[$i] = $url_quality_temp . $i;
		}
		
		$view_all = ''; //'whole';
		$view_all_a = '<a class="link" title="Show in full text way." href="' . $url_view . '">Show full text</a>';
		$view_cat = ''; //'category';
		$view_cat_a = '<a class="link" title="Show all posts in only title way." href="' . $url_view . '">Catalog View</a>';
		$quality_show = array('1', '2', '3', '4', '5');
		$quality_show_a = array('<a class="qnum" title="Quality filter: lower." href="' . $url_quality[1] . '">1</a>', 
							'<a class="qnum" title="Quality filter: low." href="' . $url_quality[2] . '">2</a>', 
							'<a class="qnum" title="Quality filter: normal." href="' . $url_quality[3] . '">3</a>', 
							'<a class="qnum" title="Quality filter: high." href="' . $url_quality[4] . '">4</a>', 
							'<a class="qnum" title="Quality filter: higher." href="' . $url_quality[5] . '">5</a>');
?>
		<div id="view_bar">
<?php
		
		if ( $view == 'cat' )
			echo $view_all_a . ' ' . $view_cat;
		else
			echo $view_all . ' ' . $view_cat_a;
?>
		</div>
		<div id="white_black_bar">
<?php
		/*
		//white & black view controller
		
		$white = 'white';
		$white_a = '<a class="link" href="' . home_url() . '/white/">white</a>';
		$black = 'black';
		$black_a = '<a class="link" href="' . home_url() . '/black/">black</a>';
		if ( ! isset($wp->query_vars['white_black']) ):
			echo $white_a . ' ' . $black_a . '<br/>';
		elseif ( $wp->query_vars['white_black'] == '2' ):
			echo $white_a . ' ' . $black . '<br/>';
		else:
			echo $white . ' ' . $black_a . '<br/>';
		endif;
		*/
?>
		</div>
		<div id="quality_bar">
<?php
		for ($i = 1; $i <= 5; $i ++ ) {
			if ( $i == $quality )
				echo $quality_show[$i-1] . ' ';
			else
				echo $quality_show_a[$i-1] . ' ';
		}
?>
		</div>
<?php
		}
		?>
		
		<div id="searchform">
		<ul>
			
			<li>
				<?php ct_get_search_form(); ?>
			</li>

		</ul>
		</div>
		
		<div id="categoryform">
		<ul role="navigation">
			<li class="categories"><h2>Categories</h2>
			<ul>
			<?php 
				foreach ( array(1, 6, 9, 19, 12, 23) as $pid ){
					wp_list_categories(array('title_li' => '','show_count' => 1, 'hide_empty' => 0,'include' => $pid));
					?>
					<ul class="children">
					<?php
					wp_list_categories(array('title_li' => '', 'show_count' => 1, 'hide_empty' => 1, 'child_of' => $pid, 'depth' => 2));
					?>
					</ul>
					<?php
				}
				//$category = wp_list_categories(array('show_count' => 1, 'title_li' => '<h2>' . __('Categories') . '</h2>', 'echo' => 1, 'depth' => 3, 'current_category' => 1)); 
			?>
			</ul>
			</li>
		</ul>
		</div> 
		
		<?php 
		$args = array(
			'numberposts' => 10,
			'offset' => 0,
			'category' => 0,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'post',
			//'post_status' => 'draft, publish, future, pending, private',
			'post_status' => 'publish',
			'suppress_filters' => true );
		$recent_posts = wp_get_recent_posts( $args, $output = ARRAY_A ); 
		?>
		<div id="latest"><div class="title">Latest</div><ul>
		<?php
			global $my_ct_post_filter;
			foreach ($recent_posts as $post) {
				$post_filter = $my_ct_post_filter->front_post_fileter_latest($post);
				if ( $post_filter == true )
					continue;
				?>
				<li><a href="<?php echo bloginfo('home'); ?>/<?php echo $post['post_name']; ?>"> <?php echo $post['post_title']; ?> </a></li>
				<?php
			}
		?>
		</ul></div>
		
		<div id="archive"><div class="title"><?php _e( 'Archives', 'ct' ); ?></div>
			<ul>
				<?php 
					$archive = wp_get_archives( array( 'type' => 'yearly' , 'show_post_count' => true, 'echo' => 0) );
					$archive = str_replace('\' title=\'', '?view=cat&quality=1\' title=\'Show all posts in ', $archive);
					echo $archive;
				?>
			</ul>
		</div>
		
	</div>
<?php endif;?>