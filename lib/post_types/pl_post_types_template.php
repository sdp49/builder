<html>
	<head>
		<?php wp_head(); ?>
	</head>
	<body>
	<?php
		add_filter('show_admin_bar', '__return_false');
		add_action('wp_enqueue_scripts', isset( $drop_modernizr ) ? 'pl_template_drop_modernizr': 'pl_template_add_modernizr' );

		global $post;
		
		echo do_shortcode( isset( $shortcode ) ? $shortcode : $post->post_content );
	
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