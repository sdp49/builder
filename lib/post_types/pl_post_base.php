<?php

/**
 * Main Post Base class
 * 
 * Defines a skeleton for registering post type and adding meta boxes (used by all post types)
 * 
 * @author nofearinc
 *
 */
abstract class PL_Post_Base {
	
	public function __construct() {
		$this->init();
	}
	
	public function init() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'meta_box' ) );
		add_action( 'save_post', array( $this, 'meta_box_save' ) );
		add_action( 'template_redirect', array( $this, 'post_type_templating' ) );
		
	}	
	
	public abstract function register_post_type( );
	public abstract function meta_box( );
	public abstract function meta_box_save( $post_id );

	public function post_type_templating( $single ) {
		global $post;

		// apply only to our post types
		if( in_array( $post->post_type, PL_Post_Type_Manager::$post_types ) ) {
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
			die();
		}
	
		return;
	}
}