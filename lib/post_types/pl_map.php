<?php

add_action( 'init', 'pl_register_map_post_type' );

function pl_register_map_post_type() {
	$args = array(
			'labels' => array(
					'name' => __( 'Maps', 'pls' ),
					'singular_name' => __( 'pl_map', 'pls' ),
					'add_new_item' => __('Add New Map', 'pls'),
					'edit_item' => __('Edit Map', 'pls'),
					'new_item' => __('New Map', 'pls'),
					'all_items' => __('All Maps', 'pls'),
					'view_item' => __('View Maps', 'pls'),
					'search_items' => __('Search Maps', 'pls'),
					'not_found' =>  __('No maps found', 'pls'),
					'not_found_in_trash' => __('No maps found in Trash', 'pls')),
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

	register_post_type('pl_map', $args );
}