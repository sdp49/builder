<?php
add_action( 'init', 'pl_register_neighborhood_post_type' );

function pl_register_neighborhood_post_type() {
	$args = array(
			'labels' => array(
					'name' => __( 'Neighborhoods', 'pls' ),
					'singular_name' => __( 'neighborhood', 'pls' ),
					'add_new_item' => __('Add New Neighborhood', 'pls'),
					'edit_item' => __('Edit Neighborhood', 'pls'),
					'new_item' => __('New Neighborhood', 'pls'),
					'all_items' => __('All Neighborhoods', 'pls'),
					'view_item' => __('View Neighborhoods', 'pls'),
					'search_items' => __('Search Neighborhoods', 'pls'),
					'not_found' =>  __('No neighborhoods found', 'pls'),
					'not_found_in_trash' => __('No neighborhoods found in Trash', 'pls')),
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

	register_post_type('pl_neighborhood', $args );
}