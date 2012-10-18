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

add_action( 'add_meta_boxes', 'pl_forms_meta_box' );

function pl_forms_meta_box() {
	add_meta_box( 'my-meta-box-id', 'Page Subtitle', 'pl_forms_meta_box_cb', 'pl_form', 'normal', 'high' );
}

// add meta box for featured listings- adding custom fields
function pl_forms_meta_box_cb( $post ) {
	$values = get_post_custom( $post->ID );
	// get meta values from custom fields
	$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? unserialize($values['pl_featured_listing_meta'][0]) : '';
	$pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? '' : $pl_featured_listing_meta['featured-listings-type'];
	
	
	PL_Snippet_Template::prepare_template(
			array(
					'codes' => array( 'search_form' ),
					'p_codes' => array(
							'search_form' => 'Search Form'
					)
			)
		);
}

add_action( 'save_post', 'pl_forms_meta_box_save' );
function pl_forms_meta_box_save( $post_id ) {
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
