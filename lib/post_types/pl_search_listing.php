<?php
add_action( 'init', 'pl_register_search_listing_post_type' );

function pl_register_search_listing_post_type() {
	$args = array(
			'labels' => array(
					'name' => __( 'Search Listings', 'pls' ),
					'singular_name' => __( 'search_listing', 'pls' ),
					'add_new_item' => __('Add New Search Listing', 'pls'),
					'edit_item' => __('Edit Search Listing', 'pls'),
					'new_item' => __('New Search Listing', 'pls'),
					'all_items' => __('All Search Listings', 'pls'),
					'view_item' => __('View Search Listings', 'pls'),
					'search_items' => __('Search Search Listings', 'pls'),
					'not_found' =>  __('No search listings found', 'pls'),
					'not_found_in_trash' => __('No search listings found in Trash', 'pls')),
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

	register_post_type('pl_search_listing', $args );
}