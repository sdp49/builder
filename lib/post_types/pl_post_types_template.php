<?php
// 	add_action('wp_enqueue_scripts', 'pl_template_inc_scripts');
	if( isset( $drop_modernizr )) {
		add_action('wp_enqueue_scripts', 'pl_template_drop_modernizr');
	} else {
		add_action('wp_enqueue_scripts', 'pl_template_add_modernizr');
	}
 	
	wp_head();
	
	global $post;
	if( isset( $shortcode ) ) {
		echo do_shortcode( $shortcode );
	} else {
		echo do_shortcode( $post->post_content );
	}
	wp_footer();

 	function pl_template_drop_modernizr() {
 			wp_dequeue_script('form');
 	}
 	
 	function pl_template_add_modernizr() {
 		wp_register_script( 'modernizr', trailingslashit( PLS_JS_URL ) . 'libs/modernizr/modernizr.min.js' , array(), '2.6.1');
 		wp_enqueue_script( 'modernizr' );
 	}
?>