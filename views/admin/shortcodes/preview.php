<?php
/**
 * Used to preview a shortcode using parameters provided in the url
 * instead of usng a shortcode post object
 */
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past


function pl_template_drop_modernizr() {
	wp_dequeue_script('form');
}

function pl_template_add_modernizr() {
	wp_register_script( 'modernizr', trailingslashit( PLS_JS_URL ) . 'libs/modernizr/modernizr.min.js' , array(), '2.6.1');
	wp_enqueue_script( 'modernizr' );
}

if (empty($shortcode) || empty($args)) {
	die;
}


$hash = md5($shortcode . serialize($sc_str));
$widget_cache = new PL_Cache("Embeddable_Widget_Preview");

if(0&& $widget_page = $widget_cache->get($hash) ) {
	echo $widget_page;
	return;
}

add_filter('show_admin_bar', '__return_false');

ob_start();

?><html style="margin-top: 0 !important; overflow: hidden;">
	<head>
		<style type="text/css">
			body {
				margin-top: 0px;
				overflow: hidden;
			}
			.pls_embedded_widget_wrapper {
				overflow: hidden;
			}
			#full-search .form-grp:first-child {
				margin-top: 0px;
			}
			.pls_embedded_widget_wrapper .pls_search_form_listings {
				margin-bottom: 0px;
			}
			p {
				margin-top: 0px;
			}
		</style>
		<script type="text/javascript">
			var pl_general_widget = true;
		</script>
		<?php wp_head(); ?>
	</head>
	<body>
	
		<div class="pls_embedded_widget_wrapper">
			<?php
			echo do_shortcode( $sc_str );
			?>
		<div>
		
		<?php wp_footer();?>
	</body>
</html>
<?php

$widget_page = ob_get_clean();
$widget_cache->save( $widget_page );

echo $widget_page;