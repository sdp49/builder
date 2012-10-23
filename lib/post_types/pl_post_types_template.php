<?php
	add_action('wp_enqueue_scripts', 'pl_template_inc_scripts');
	wp_head();
// 	get_header();
 	function pl_template_inc_scripts() {
 		wp_register_script( 'modernizr', trailingslashit( PLS_JS_URL ) . 'libs/modernizr/modernizr.min.js' , array(), '2.6.1');
 		wp_enqueue_script( 'modernizr' );
 	}
	global $post;
	if( isset( $shortcode ) ) {
		echo do_shortcode( $shortcode );
	} else {
		echo do_shortcode( $post->post_content );
	}
// 	get_footer();
	wp_footer();
?>