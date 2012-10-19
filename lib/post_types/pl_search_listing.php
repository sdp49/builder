<?php

PL_Search_Listing_CPT::init();

class PL_Search_Listing_CPT {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public static $fields = array(
			'field1' => array( 'type' => 'text', 'label' => 'Field 1' ),
			'field2' => array( 'type' => 'select', 'label' => 'Field 2', 'options' => array( 'one' => 'one', 'two' => 'two' ) ),
			'field3' => array( 'type' => 'checkbox', 'label' => 'Field 3' ),
	);

	public function init() {
		add_action( 'init', array( __CLASS__, 'pl_register_search_listing_post_type' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'pl_search_listings_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'pl_search_listings_meta_box_save' ) );
	}

	public static function pl_register_search_listing_post_type() {
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
	
		register_post_type('pl_search_listing', $args );
	}
	
	
	public static function pl_search_listings_meta_box() {
		add_meta_box( 'my-meta-box-id', 'Page Subtitle', 'pl_search_listings_meta_box_cb', array( __CLASS__, 'pl_search_listing' ), 'normal', 'high' );
	}
	
	// add meta box for featured listings- adding custom fields
	public static function pl_search_listings_meta_box_cb( $post ) {
		$values = get_post_custom( $post->ID );
		// get meta values from custom fields
		$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? unserialize($values['pl_featured_listing_meta'][0]) : '';
		$pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? '' : $pl_featured_listing_meta['featured-listings-type'];
	
		PL_Snippet_Template::prepare_template(
			array(
				'codes' => array( 'search_listings' ),
				'p_codes' => array(
					'search_form' => 'Search Listings'
				)
			)
		);
	}
	
	public static function pl_search_listings_meta_box_save( $post_id ) {
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
}