<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
include_once(PL_LIB_DIR . 'shortcodes/search_listings.php');

class PL_Static_Listing_CPT extends PL_Search_Listing_CPT {

	protected static $post_type = 'pl_static_listing';
	
	protected static $shortcode = 'static_listings';
	
	protected static $title = 'List of Listings';
}

PL_Static_Listing_CPT::init(__CLASS__);