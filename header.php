<?php
/**
 * @package ct
 *
 * Header for theme
 *
 */
?>
<?php
	global $wp;
	global $content_ajax;
	if ( $wp->extra_query_vars['ajax'] == 1 or ( isset($_GET['ajax']) and $_GET['ajax'] == '1' ) or ( isset($_POST['ajax']) and $_POST['ajax'] == '1' ) or ( substr($_SERVER['REQUEST_URI'], -6, strlen($_SERVER['REQUEST_URI'])) == 'ajax=1' )):
		$content_ajax = true;
	else:
		$content_ajax = false;
	endif;

	if ($content_ajax == false):
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script-->
<script src="<?php echo bloginfo('template_directory');?>/js/jquery.js" type="text/javascript"></script>
<?php
	if ( is_single() ) {
?>
<script>
<?php if ( is_user_logged_in() ) {?>
var is_logged_in = true;
<?php } else { ?>
var is_logged_in = false;
<?php } ?>
</script>
<script src="<?php echo bloginfo('template_directory');?>/js/single.js" type="text/javascript"></script>
<?php
	} 
	else {
?>
<script src="<?php echo bloginfo('template_directory');?>/js/archive.js" type="text/javascript"></script>
<script>
var	next_page_num = 2;
var get_more_avaiable = true;
var	url = window.location.href;
var grep = /\/page\/\d+/;
var match_pn = url.match(grep);
if ( match_pn != null ) {
	next_page_num = parseInt((match_pn[0]).substr(6));
	next_page_num += 1;
}
</script>
<?php } ?>

<style type="text/css" media="screen">

<?php
// Checks to see whether it needs a sidebar
if ( empty($withcomments) && !is_single() ) {
?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbg-<?php bloginfo('text_direction'); ?>.jpg") repeat-y top; border: none; }
<?php } else { // No sidebar ?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbgwide.jpg") repeat-y top; border: none; }
<?php } ?>

</style>

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
if ( is_single() ):
	$rss_link = home_url() . '/feed';
	$rss_title = 'Subscribe the blog defaultly.';
else:
	$uri = $_SERVER['REQUEST_URI'];
	$uri_parts = explode('?', $uri);
	if ( $uri_parts[0][strlen($uri_parts[0])-1] == '/' ):
		$rss_link = $uri_parts[0] . 'feed/';
	else:
		$rss_link = $uri_parts[0] . '/feed/';
	endif;
	if ( $uri_parts[1] != null )
		$rss_link .= '?' . $uri_parts[1];
	$rss_title = 'Subscribe this page/url.';
endif;
?>
<!--div id="right_conner_text">
	<span id="rss_feed"><a href="<?php echo $rss_link; ?>" title="<?php echo $rss_title?>">Rss</a></span>
	<span id="contact"><a href="" title="About/Contact">Contact</a></span>
</div-->
<?php
	global $my_ct_post_filter;
	if ( $my_ct_post_filter->get_current_user_role() == 'not_login' ):
?>
<div id="right_conner">
<?php else: ?>
<div id="right_conner_logined">
<?php endif; ?>
	<a href=""><img id="banner_contact" title="contact" src="<?php echo bloginfo('template_directory')?>/img/redbelt_about.png"/></a>
	<a href="<?php echo $rss_link; ?>" title="<?php echo $rss_title?>"><img id="banner_rss" src="<?php echo bloginfo('template_directory')?>/img/rss.jpg"/></a>
</div>

<div id="toTop">
<a href="#top"><img src="<?php echo bloginfo('template_directory'); ?>/img/up-bright.png" /></a>
</div>

<div id="page">

<?php
/*
<div id="header" role="banner">
	<div id="headerimg">
		<h1><a href="<?php echo home_url(); ?>/"><?php //bloginfo('name'); ?></a></h1>
		<div class="description"><a href="<?php echo home_url();?>/"><?php bloginfo('description'); ?></a></div>
	</div>
</div>
*/
?>
<?php endif;?>