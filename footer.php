<?php
/**
* @package ct
*
* Footer for the theme.
*
*/

global $content_ajax;
if ( $content_ajax == false ):
?>


<div id="footer" role="contentinfo">
<!-- If you'd like to support WordPress, having the "powered by" link somewhere on your blog is the best way; it's our only promotion or advertising. -->
	<p>
		Theme by <a href="<?php echo home_url();?>">chentingpc</a>.<br />
		<?php printf(__('Blog kernel by %1$s'), 
		'<a href="http://wordpress.org/">WordPress</a>'); ?>
		<?php //printf(__('%1$s and %2$s.'), '<a href="' . get_bloginfo('rss2_url') . '">' . __('Entries (RSS)') . '</a>', '<a href="' . get_bloginfo('comments_rss2_url') . '">' . __('Comments (RSS)') . '</a>'); ?>
		<?php 
			if (is_user_logged_in())
				printf(__('<br/>%d queries. %s seconds.'), get_num_queries(), timer_stop(0, 3));
		?>
	</p>
</div>
</div>
		<?php wp_footer(); ?>
</body>
</html>
<?php
	endif;
	
?>

<?php

/**
* Google Analytics Code
*/

/*

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-39897845-1', 'tingchen.info');
  ga('send', 'pageview');

</script>

*/

/**
* Baidu Analytics Code
*/

?>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?787bd5545ca6f27beaf553daac2a32e9";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>