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
}
