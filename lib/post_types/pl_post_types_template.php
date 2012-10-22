<?php
	//add_action('wp_enqueue_scripts', 'pl_template_inc_scripts');
	wp_head();
// 	get_header();
// 	function pl_template_inc_scripts() {
// 		include_once PL_PARENT_DIR . '/helpers/js.php';
// 	}
	global $post;
	if( isset( $shortcode ) ) {
		echo do_shortcode( $shortcode );
	} else {
		echo do_shortcode( $post->post_content );
	}
// 	get_footer();
	wp_footer();
?>