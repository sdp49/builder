<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
class PL_Search_Listing_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_search_listing';

	protected static $shortcode = 'search_listings';

	protected static $title = 'Search Listings';

	protected static $help = 
		'<p>
        You can insert your "activated" Listings snippet by using the [search_form] shortcode in a page or a post.
        The listings view is intended to be used alongside the [search_form] shortcode defined above as a container
        for the results of the search, with the snippet representing how an <i>individual</i> listing that matches
        the search criteria will be displayed.
		</p>';

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

	protected static $filters = array();

	protected static $template = array(
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'HTML', 'default' => 'Put subcodes here customize your list of properties...' ),
		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 'default' => '' ),
		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the template', 'default' => '' ),
		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the template', 'default' => '' ),
	);




	protected function _get_filters() {
		if (class_exists('PL_Config')) {
			return PL_Config::PL_API_LISTINGS('get', 'args');
		}
		else {
			return array();
		}
	}
}

PL_Search_Listing_CPT::init(__CLASS__);
