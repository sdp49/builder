<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
class PL_Search_Listing_CPT extends PL_Post_Base {

	protected static $post_type = 'pl_search_listing';
	
	protected static $shortcode = 'search_listings';
	
	protected static $title = 'Search Listings';

	protected static $filters = array(
		'listing_types'		=> array( 'type' => 'select', 'label' => 'Listing Types', 'default' => 'false' ),
		'location'			=> array( 'type' => 'subgrp', 'label' => 'Location', 'default' => array(),
			'subgrp' => array(
				'postal'			=> array( 'type' => 'select', 'label' => 'Zip', 'default' => 'false' ),
				'region'			=> array( 'type' => 'select', 'label' => 'State', 'default' => 'false' ),
				'locality'			=> array( 'type' => 'select', 'label' => 'City', 'default' => 'false' ),
				'neighborhood'		=> array( 'type' => 'select', 'label' => 'Neighborhood', 'default' => 'false' ),
				'county'			=> array( 'type' => 'select', 'label' => 'County', 'default' => 'false' ),
			)),
		'metadata'			=> array( 'type' => 'subgrp', 'label' => 'Metadata', 'default' => array(),
			'subgrp' => array(
				'beds'				=> array( 'type' => 'select', 'label' => 'Beds', 'default' => 'false' ),
				'baths'				=> array( 'type' => 'select', 'label' => 'Baths', 'default' => 'false' ),
				'half_baths'		=> array( 'type' => 'select', 'label' => 'Half Baths', 'default' => 'false' ),
				'max_price'			=> array( 'type' => 'select', 'label' => 'Max Price', 'default' => 'false' ),
				'min_price'			=> array( 'type' => 'select', 'label' => 'Min Price', 'default' => 'false' ),
				'max_avail_on_picker'	=> array( 'type' => 'select', 'label' => 'Latest Available Date', 'default' => 'false' ),
				'min_avail_on_picker'	=> array( 'type' => 'select', 'label' => 'Earliest Available Date', 'default' => 'false' ),
				'max_sqft'			=> array( 'type' => 'numeric', 'label' => 'Max Sqft', 'default' => '' ),
				'max_sqft'			=> array( 'type' => 'numeric', 'label' => 'Min Sqft', 'default' => '' ),
				'max_lt_sz'			=> array( 'type' => 'numeric', 'label' => 'Max Lot Size', 'default' => '' ),
				'min_lt_sz'			=> array( 'type' => 'numeric', 'label' => 'Min Lot Size', 'default' => '' ),
				'desc'				=> array( 'type' => 'checkbox', 'label' => 'Has Description', 'default' => '' ),
				'agency_only'		=> array( 'type' => 'checkbox', 'label' => 'My Offices Listings', 'default' => '' ),
				'non_import'		=> array( 'type' => 'checkbox', 'label' => 'Non MLS Listings', 'default' => '' ),
			)),
	);

	protected static $subcodes = array(
		'price',
		'sqft',
		'beds',
		'baths',
		'half_baths',
		'avail_on',
		'url',
		'address',
		'locality',
		'region',
		'postal',
		'neighborhood',
		'county',
		'country',
		'coords',
		'unit',
		'full_address',
		'email',
		'phone',
		'desc',
		'image',
		'mls_id',
		'map',
		'listing_type',
		'img_gallery',
		'amenities',
		'price_unit',
		//'compliance'
	);




	public function register_post_type() {
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
						'not_found' => __('No search listings found', 'pls'),
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
}

new PL_Search_Listing_CPT();