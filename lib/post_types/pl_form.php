<?php

add_action( 'init', 'pl_register_form_post_type' );

function pl_register_form_post_type() {
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
					'not_found' =>  __('No forms found', 'pls'),
					'not_found_in_trash' => __('No forms found in Trash', 'pls')),
			'menu_icon' => trailingslashit(PL_IMG_URL) . 'featured.png',
			'public' => true,
			'publicly_queryable' => false,
			'show_ui' => false,
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title'),
			'taxonomies' => array('category', 'post_tag')
	);

	register_post_type('pl_form', $args );
}