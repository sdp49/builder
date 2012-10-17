<?php

add_action( 'init', 'pl_register_slideshow_post_type' );

function pl_register_slideshow_post_type() {
	$args = array(
			'labels' => array(
					'name' => __( 'Slideshows', 'pls' ),
					'singular_name' => __( 'slideshow', 'pls' ),
					'add_new_item' => __('Add New Slideshow', 'pls'),
					'edit_item' => __('Edit Slideshow', 'pls'),
					'new_item' => __('New Slideshow', 'pls'),
					'all_items' => __('All Slideshows', 'pls'),
					'view_item' => __('View Slideshows', 'pls'),
					'search_items' => __('Search Slideshows', 'pls'),
					'not_found' =>  __('No slideshows found', 'pls'),
					'not_found_in_trash' => __('No slideshows found in Trash', 'pls')),
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

	register_post_type('pl_slideshow', $args );
}