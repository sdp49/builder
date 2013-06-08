<?php
/**
 * Post type/Shortcode to generate a property search form
 *
 */

class PL_Form_CPT extends PL_Post_Base {

	protected static $post_type = 'pl_form';

	protected static $shortcode = 'search_form';

	protected static $options = array(
			'width'			=> array( 'type' => 'numeric', 'label' => 'Width', 'default' => 250 ),
			'height'		=> array( 'type' => 'numeric', 'label' => 'Height', 'default' => 250 ),
			'ajax'			=> array( 'type' => 'checkbox', 'label' => 'Disable AJAX', 'default' => false ),
			'formaction'	=> array( 'type' => 'text', 'label' => 'Form URL when AJAX is disabled', 'default' => '' ),
			'modernizr'		=> array( 'type' => 'checkbox', 'label' => 'Drop Modernizr', 'default' => false ),
	);




	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Forms', 'pls' ),
						'singular_name' => __( 'pl_form', 'pls' ),
						'add_new_item' => __('Add New Form', 'pls'),
						'edit_item' => __('Edit Form', 'pls'),
						'new_item' => __('New Form', 'pls'),
						'all_items' => __('All Forms', 'pls'),
						'view_item' => __('View Forms', 'pls'),
						'search_items' => __('Search Forms', 'pls'),
						'not_found' => __('No forms found', 'pls'),
						'not_found_in_trash' => __('No forms found in Trash', 'pls')),
				'menu_icon' => trailingslashit(PL_IMG_URL) . 'featured.png',
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array('title', 'editor'),
				'taxonomies' => array('category', 'post_tag')
		);

		register_post_type('pl_form', $args );
	}
}

new PL_Form_CPT();