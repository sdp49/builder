<?php
/**
 * Post type/Shortcode to generate a list of featured listings
 *
 */
include_once(PL_LIB_DIR . 'shortcodes/search_listings.php');

class PL_Featured_Listings_CPT extends PL_Search_Listing_CPT {

	protected $pl_post_type = 'featured_listings';

	protected $shortcode = 'featured_listings';

	protected $title = 'Featured Listings';

	protected $help =
		'<p>
		</p>';

	protected $options = array(
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'pl_featured_listing_meta' => array( 'type' => 'featured_listing_meta', 'default' => '' ),
	);

	// Use the same subcodes, template as search listings shortcode
	// protected $subcodes = array();
	// protected $template = array();




	public static function init() {
		parent::_init(__CLASS__);
	}

	/**
	 * No filters
	 * @see PL_SC_Base::_get_filters()
	 */
	protected function _get_filters() {
		return array();
	}
}

PL_Featured_Listings_CPT::init();
