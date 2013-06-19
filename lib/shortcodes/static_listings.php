<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
include_once(PL_LIB_DIR . 'shortcodes/search_listings.php');

class PL_Static_Listing_CPT extends PL_Search_Listing_CPT {

	protected static $pl_post_type = 'pl_static_listing';

	protected static $shortcode = 'static_listings';

	protected static $title = 'List of Listings';

	protected static $help =
		'<p>
		You can insert your Static Listings snippet by using the [static_listings id="<em>listingid</em>"] shortcode in a page or a post.
		The shortcode require an ID parameter of the static listing ID number published in your
		Featured Listings post type control on the left side of the admin panel.
		</p>';
}

PL_Static_Listing_CPT::init(__CLASS__);