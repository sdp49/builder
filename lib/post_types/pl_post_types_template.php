<?php 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?><html>
	<head>
		<style type="text/css">
			body {
				margin-top: 0px;
				overflow: hidden;
			}
			.pls_embedded_widget_wrapper {
				overflow: hidden;
			}
		</style>
		<?php wp_head(); ?>
	</head>
	<body>
	<?php
		add_filter('show_admin_bar', '__return_false');
		add_action('wp_enqueue_scripts', isset( $drop_modernizr ) ? 'pl_template_drop_modernizr': 'pl_template_add_modernizr' );

		global $post;
		
		echo '<div class="pls_embedded_widget_wrapper">';
		echo do_shortcode( isset( $shortcode ) ? $shortcode : $post->post_content );
		echo '<div>';
		
		wp_footer();
		
	 	function pl_template_drop_modernizr() {
	 			wp_dequeue_script('form');
	 	}
	 	
	 	function pl_template_add_modernizr() {
	 		wp_register_script( 'modernizr', trailingslashit( PLS_JS_URL ) . 'libs/modernizr/modernizr.min.js' , array(), '2.6.1');
	 		wp_enqueue_script( 'modernizr' );
	 	}
	?>
</body>
</html>