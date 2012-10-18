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

	register_post_type('pl_map', $args );
}

add_action( 'add_meta_boxes', 'pl_maps_meta_box' );

function pl_maps_meta_box() {
	add_meta_box( 'my-meta-box-id', 'Maps', 'pl_maps_meta_box_cb', 'pl_map', 'normal', 'high' );
}

// add meta box for featured listings- adding custom fields
function pl_maps_meta_box_cb( $post ) {
	$values = get_post_custom( $post->ID );
	// get meta values from custom fields
	$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? unserialize($values['pl_featured_listing_meta'][0]) : '';
	$pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? '' : $pl_featured_listing_meta['featured-listings-type'];
}

add_action( 'save_post', 'pl_maps_meta_box_save' );
function pl_maps_meta_box_save( $post_id ) {
	// Avoid autosaves
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	// Verify nonces for ineffective calls
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_fl_meta_box_nonce' ) ) return;

	update_post_meta( $post_id, 'pl_static_listings_option', $static_listings_option );
	update_post_meta( $post_id, 'pl_listing_type', $_POST['pl_listing_type']);

	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;

	// Verify if the time field is set
	if( isset( $_POST['pl_featured_listing_meta'] ) ) {
		update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
	}
}
