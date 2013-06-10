<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
include_once(PL_LIB_DIR . 'post_types/pl_post_base.php');

class PL_Static_Listing_CPT extends PL_Search_Listing_CPT {

	protected static $post_type = 'pl_static_listing';
	
	protected static $shortcode = 'static_listings';
	
	protected static $title = 'List of Listings';





	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Search Listings', 'pls' ),
						'singular_name' => __( 'search_listing', 'pls' ),
						'add_new_item' => __('Add New Static Listing', 'pls'),
						'edit_item' => __('Edit Static Listing', 'pls'),
						'new_item' => __('New Static Listing', 'pls'),
						'all_items' => __('All Static Listings', 'pls'),
						'view_item' => __('View Static Listings', 'pls'),
						'search_items' => __('Search Static Listings', 'pls'),
						'not_found' => __('No static listings found', 'pls'),
						'not_found_in_trash' => __('No static listings found in Trash', 'pls')),
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

		register_post_type('pl_static_listing', $args );
	}
}

new PL_Search_Listing_CPT();