<?php
/**
* ct functions and definitions
*
* To setup this theme:
* First, you need to hack the kernel file (wp-blog-header.php) of WP first, change:
	wp();
* to
	$url_array = explode('?', $_SERVER['REQUEST_URI']);
	if ( isset($_GET['s']) and ! isset($_GET['view']) ):
		$url_array[1] .= '&view=cat';
		$_SERVER['REQUEST_URI'] .= '&view=cat';
	endif;
	wp($url_array[1]);
* Second, you active the theme like a normal theme.
*
* @package ct
* @since ct 0.0
*/



/**
*	Configurations & global variable
*/

// use the quality/importance/white_black attributes for the post
define('ENABLE_EXTRA_ATTR', true);
// turn on when you want to show all not classed (in quality/importance/white_black) posts when a
define('SHOW_NOT_CLASSED', true);
// 1, 2, 3, 4, 5 is normal option, equal and bigger than the value will be shown, 0 means show all (including not classed post)
define('DEFAULT_QUALITY', 3);
// 1, 2, 3, 4, 5 is normal option, equal and bigger than the value will be shown, 0 means show all (including not classed post)
define('DEFAULT_IMPORTANCE', 0);
// 0 means show all (including not classed post), 1 means show white, 2 means show black
define('DEFAULT_WHITE_BLACK', 0);
// the number per page of posts under the category view, 0 means the same as not category views
define('POST_NUMBER_CAT_VIEWING', 100000);
// hide the protected (passord required) posts from any one but administrator
define('HIDE_PROTECTED', true);
// turn on or off the white black rule rewriter to add/delete the support for white/black view
define('WHITE_BLACK_REWRITE', true);
// determine whether the request is through ajax
$content_ajax = false;
// category view year counter
$date_counter_cat = '0';

/**
*	ct_post_filter class
*/

class ct_post_filter{
	var		$db_name;
	
	function __construct( $arg = '' ) {
		global	$wpdb;
		$this->db_name = $wpdb->prefix . 'posts_extra_attr';
		$this->add_hook();
	}
	
	/**
	*	Pluging activation operations
	*/
	
	function install () {
		global $wp_version;
		if (version_compare ( $wp_version, "3.0", "<" )) { 
			wp_die("This plugin requires WordPress version 3.0.1 or higher.");
		}

		$sql = "CREATE TABLE ". $this->db_name." (
				  `ID` bigint(20) NOT NULL,
				  `white_black` int(11),
				  `importance` int(11),
				  `quality` int(11),
				  PRIMARY KEY  (`ID`)
			) CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	/**
	*  Get current user role
	*	@return 6 possible strings:
	*		not_login
	*		subscriber
	*		contributor
	*		author
	*		editor
	*		administrator
	*/

	function get_current_user_role() {
		if(!function_exists('wp_get_current_user')) {
			include(ABSPATH . "wp-includes/pluggable.php"); 
		}
		$current_user = wp_get_current_user();
		if ( $current_user->id == 0 )
			return 'not_login';
		return $current_user->roles[0];
	}
	
	/**
	*	Add basic hooks 
	*/
	
	function add_hook () {
		if ( ENABLE_EXTRA_ATTR ) {
			add_filter('query_vars', array(&$this, 'add_public_query_var_for_extra_attrs'));
			add_filter('posts_join_request', array(&$this, 'filter_the_article_by_modifying_join'));
			add_filter('posts_where_request', array(&$this, 'filter_the_article_by_modifying_where'), 10, 2);
			add_action('admin_menu', array(&$this, 'ct_plugin_add_custom_box'));
			add_action('save_post', array(&$this, 'save_post_extra_attrs'),10,2);
		}

		add_action('query_vars', array(&$this, 'add_public_query_var_for_cat_view'));
		add_action('pre_get_posts', array(&$this, 'number_per_page_under_cat_view'));
		
		if ( WHITE_BLACK_REWRITE ) {
			add_filter('root_rewrite_rules', array(&$this, 'white_black_rules'));
		}
	}
	
	/**
	* @for adding the query var for extra attributes
	*/

	function add_public_query_var_for_extra_attrs( $public_query_vars ){
		array_push($public_query_vars, 'quality');
		array_push($public_query_vars, 'importance');
		array_push($public_query_vars, 'white_black');
		return $public_query_vars;
	}
	
	/**
	*	modifying the join query
	*/
	
	function filter_the_article_by_modifying_join ( $join ){
		if ( is_single() or is_admin())
			return $join;
			
		global $wpdb;
		return $join . ' LEFT JOIN ' . $this->db_name . ' ON (' . $wpdb->prefix . 'posts.ID = ' . $this->db_name . '.ID' . ')';	
	}

	/**
	*	modifying the where query
	*/
	
	function filter_the_article_by_modifying_where( $where, $class_wp ) {
		if ( is_single() or is_admin())
			return $where;
			
		if ( !isset($class_wp->query_vars['quality']) )
			$quality = DEFAULT_QUALITY;
		else
			$quality = (int)$class_wp->query_vars['quality'];
		if ( !isset($class_wp->query_vars['importance']) )
			$importance = DEFAULT_IMPORTANCE;
		else
			$importance = (int)$class_wp->query_vars['importance'];
		if ( !isset($class_wp->query_vars['white_black']) )
			$white_black = DEFAULT_WHITE_BLACK;
		else 
			$white_black  = (int)$class_wp->query_vars['white_black'];
		
		$give_it_a_break_quality = 'quality is NULL OR';
		$give_it_a_break_importance = 'importance is NULL OR';
		$give_it_a_break_white_black = 'white_black is NULL OR';
		
		if ( SHOW_NOT_CLASSED ) {
			$where .= " AND (" . $give_it_a_break_quality . " quality >= " . $quality . ") ";
			$where .= " AND (" . $give_it_a_break_importance . " importance >= " . $importance . ") ";
			if ( $white_black != 0 )
				$where .= " AND (" . $give_it_a_break_white_black . " white_black = " . $white_black . ") ";
		} else {
			if ( $quality != 0 )
				$where .= " AND (quality >= " . $quality . ") ";	
			if ( $importance != 0 )
				$where .= " AND (importance >= " . $importance . ") ";
			if ( $white_black != 0 )
				$where .= " AND (white_black = " . $white_black . ") ";
		}
		
		return $where;
	}
	
	/**
	*	add things in dash channel
	*/
	
	function article_filter_dashboard_widgets(){
		global $wpdb;
		if ( isset($_GET['post']) )
			$post_id = (int) $_GET['post'];
		elseif ( isset($_POST['post_ID']) )
			$post_id = (int) $_POST['post_ID'];
		else
			$post_id = 0;
			
		$result = $wpdb->get_results( $wpdb->prepare( "SELECT quality, importance, white_black FROM " . $this->db_name . ' WHERE ID = ' . $post_id ) );
		$quality = 0;
		$importance = 0;
		$white_black = 0;
		if ( ! empty($result) ) {
			if ( isset($result[0]->quality) )
				$quality = (int)$result[0]->quality;
			if ( isset($result[0]->importance) )
				$importance = (int)$result[0]->importance;
			if ( isset($result[0]->white_black) )
				$white_black = (int)$result[0]->white_black;
		}
	?>
		<center>
		<select name="quality" id="quality">
			<option value="illegal" disabled="disabled" <?php if ( $quality == 0 ) echo 'selected="selected"';?>>---Quality---</option>
			<option value="1" <?php if ( $quality == 1 ) echo 'selected="selected"';?>>Lower</option>
			<option value="2" <?php if ( $quality == 2 ) echo 'selected="selected"';?>>Low</option>
			<option value="3" <?php if ( $quality == 3 ) echo 'selected="selected"';?>>Normal</option>
			<option value="4" <?php if ( $quality == 4 ) echo 'selected="selected"';?>>High</option>
			<option value="5" <?php if ( $quality == 5 ) echo 'selected="selected"';?>>Higher</option>
		</select>
		<br/>
		<select name="importance" id="importance">
			<option value="illegal" disabled="disabled" <?php if ( $importance == 0 ) echo 'selected="selected"';?>>---Importance---</option>
			<option value="1" <?php if ( $importance == 1 ) echo 'selected="selected"';?>>Nothing</option>
			<option value="2" <?php if ( $importance == 2 ) echo 'selected="selected"';?>>little</option>
			<option value="3" <?php if ( $importance == 3 ) echo 'selected="selected"';?>>Normal</option>
			<option value="4" <?php if ( $importance == 4 ) echo 'selected="selected"';?>>Very</option>
			<option value="5" <?php if ( $importance == 5 ) echo 'selected="selected"';?>>Extreme</option>
		</select>
		<br/>
		<select name="white_black" id="white_black">
			<option value="illegal" disabled="disabled" <?php if ( $white_black == 0 ) echo 'selected="selected"';?>>---White/Black---</option>
			<option value="1" <?php if ( $white_black == 1 ) echo 'selected="selected"';?>>White</option>
			<option value="2" <?php if ( $white_black == 2 ) echo 'selected="selected"';?>>Black</option>
		</select>
		</center>
	<?php
	}

	/**
	*	add customed box in dash channel
	*/
	
	function ct_plugin_add_custom_box() {
		$screens = array( 'post', 'page' );
		foreach ($screens as $screen) {
			add_meta_box(
				'ctPostExtraAttributes',
				__( 'Extra Attributes', 'Extra Attributes' ),
				array($this, 'article_filter_dashboard_widgets'),
				$screen
			);
		}
	}
	
	/**
	*	save the extra attributes (quality/importance/white_black) for the posts
	*/
	
	function save_post_extra_attrs($post_id, $post) {
		global $wpdb;

		if ($post->post_parent == 0)
			$id = $post->ID;
		else
			$id = $post->post_parent;

		if( isset( $_POST['importance'] ) )
			$importance = $_POST['importance'];
		else
			$importance = 0;
		if ( isset( $_POST['quality'] ) )
			$quality = $_POST['quality'];
		else
			$quality = 0;
		if ( isset( $_POST['white_black'] ) )
			$white_black = $_POST['white_black'];
		else
			$white_black = 0;
		
		if ( isset($_POST['autosave']) or ( isset($_POST['action']) and $_POST['action'] == 'inline-save') )
			return;
		
		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO " . $this->db_name . " (`ID`, `white_black`, `importance`, `quality`) VALUE ( %d, %d, %d, %d )", 
					$id, $white_black, $importance, $quality) );
		if ( ! $result ) {
			$result = $wpdb->query( $wpdb->prepare( "UPDATE " . $this->db_name . " SET white_black = %d, importance = %d, quality = %d WHERE ID = %d", 
					$white_black, $importance, $quality, $id ) );
		}
	}
	
	/**
	*	add public vars for the number per page in category view
	*/
	
	function add_public_query_var_for_cat_view( $public_query_vars ) {
		array_push($public_query_vars, 'view');
		return $public_query_vars;
	}
	
	/**
	*	set the number per page under the category vew
	*/
	
	function number_per_page_under_cat_view( $class_wp ) {
		if ( isset($class_wp->query_vars['view']) and 'cat' == $class_wp->query_vars['view']) {
			$class_wp->set( 'posts_per_page', POST_NUMBER_CAT_VIEWING );
		}
	}
	
	/**
	*	Add white/black view rule to url rewrite
	*/
	
	function white_black_rules( $rules ) {
		foreach ( $ruels as $key => $value ) {
			$rules['white/' . $key] = $value . '&white_black=1';
			$rules['black/' . $key] = $value . '&white_black=2';
		}
		$rules['white/?$'] = 'index.php?white_black=1';
		$rules['black/?$'] = 'index.php?white_black=2';
		return $rules;
	}
	
	/**
	*	post filter for in the front end (the query is been done), for the main pages (home.php, archive.php, search.php, single.php, etc)	
	*/
	
	function front_post_fileter_main() {
		global $post;
		if ( HIDE_PROTECTED == true and $post->post_password != '' and !is_admin() and !is_single() and !is_page() and 'administrator' != $this->get_current_user_role() )
			return true;
		return false;
	}
	
	/**
	*	post filter for in the front end (the query is been done), for the sidebar latest display
	*/
	
	function front_post_fileter_latest( $post ) {
		if ( HIDE_PROTECTED == true and $post['post_password'] != '' and 'administrator' != $this->get_current_user_role() )
			return true;
		return false;
	}
};


/**
* when the theme is added, add the plugin, create new instance of the class\
* Note: it's before the query, so can't use something like 'is_single()'
*/


$my_ct_post_filter = new ct_post_filter();
add_action("after_switch_theme", array(&$my_ct_post_filter,'install'));


/*****************************************************
 * Up is the post filter plugin (and some constants), 
 *the downside is useful funcitons for the theme.
 *****************************************************/


/**
 *	get current page number
 */

function get_page_num() {
	$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
	return $page;
}


/**
 *	get url view option
 */

function get_view() {
	global $wp;
	if ( isset($wp->query_vars['view']) and 'cat' == $wp->query_vars['view']) {
		return 'cat';
	}
	return 'all';
}


/*****************************************************
 *	Common components for the thems
 *****************************************************/


function ct_post_meta_on() {
	printf( __( '<span class="sep"><a href="%1$s" title="%2$s" rel="bookmark"></span><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a>', 'ct' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
}

/**
*	while nothing is found, show this
*/

function get_nothing_found() {
	global $quality;
	global $quality_show;
	global $quality_show_a;
	global $content_ajax;
	
	if ( $content_ajax == true ):
		return ;
	endif;
	
	
	global $quality_show;
	global $quality_show_a;
	
	for ($i = 1; $i <= 5; $i ++ ) {
	if ( $i == $quality )
		$quality_show_final .= $quality_show[$i-1] . ' ';
	else
		$quality_show_final .= $quality_show_a[$i-1] . ' ';
	}
	
	$quality_show_final = '<div class="quality_filter">Quality Filter &nbsp;&nbsp;' . $quality_show_final . '</div>';
	
	echo $quality_show_final;
	
?>
	<article id="post-0" class="post no-results not-found">
		<header class="entry-header">
			<h1 class="entry-title"><?php _e( 'Nothing Found', 'ct' ); ?></h1>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<p><?php _e( 'Apologies, but no results were found for the requested archive <b>at this level of quality</b>.', 'ct' ); ?></p>
			<?php ct_get_search_form(true, 1); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-0 -->
<?php
}	
?>