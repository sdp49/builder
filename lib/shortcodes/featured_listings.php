<?php
/**
 * Post type/Shortcode to generate a list of featured listings
 *
 */
include_once(PL_LIB_DIR . 'shortcodes/search_listings.php');

class PL_Featured_Listings_CPT extends PL_Search_Listing_CPT {

	protected $shortcode = 'featured_listings';

	protected $title = 'Featured Listings';

	protected $help =
		'<p>
		</p>';

	protected $options = array(
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => '' ),
		'width'				=> array( 'type' => 'int', 'label' => 'Width', 'default' => 250, 'description' => '(px)' ),
		'height'			=> array( 'type' => 'int', 'label' => 'Height', 'default' => 250, 'description' => '(px)' ),
		'pl_featured_listing_meta' => array( 'type' => 'featured_listing_meta', 'default' => '' ),
	);

	// Use the same subcodes, template as search listings shortcode
	// protected $subcodes = array();

	protected $template = array(
		'snippet_body' => array(
			'type' => 'textarea',
			'label' => 'HTML to format each individual listing',
			'css' => 'mime_html',
			'description' => 'You can use the template tags with any valid HTML in this field to lay out each listing. Leave this field empty to use the built in template.'
		),

		'css' => array(
			'type' => 'textarea',
			'label' => 'CSS',
			'css' => 'mime_css',
			'description' => 'You can use any valid CSS in this field to style the listings, which will also inherit the CSS from the theme.'
		),

		'before_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content before the listings',
			'css' => 'mime_html',
			'description' => 'You can use any valid HTML in this field and it will appear before the listings. For example, you can wrap the whole list with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.'
		),

		'after_widget' => array(
			'type' => 'textarea',
			'label' => 'Add content after the listings',
			'css' => 'mime_html',
			'description' => 'You can use any valid HTML in this field and it will appear after the listings.
For example, you might want to include the [compliance] shortcode.'
		),
	);




	public static function init() {
		parent::_init(__CLASS__);
	}

	/**
	 * No additional options
	 */
	public function get_options_list($with_choices = false) {
		return $this->options;
	}

	/**
	 * No filters
	 */
	public function get_filters_list($with_choices = false) {
		return array();
	}

	public static function shortcode_handler($atts, $content) {
		$content = PL_Component_Entity::featured_listings_entity($atts);

		return self::wrap('featured_listings', $content);
	}
}

PL_Featured_Listings_CPT::init();
