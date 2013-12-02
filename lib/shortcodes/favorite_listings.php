<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
include_once(PL_LIB_DIR . 'shortcodes/search_listings.php');

class PL_Favorite_Listing_CPT extends PL_Search_Listing_CPT {

	protected $shortcode = 'favorite_listings';

	protected $title = 'Favorite Listings';

	protected $help =
		'<p>
		You can insert your Favorite Listings snippet by using the [favorite_listings id="<em>listingid</em>"] shortcode in a page or a post.
		</p>';

	protected $options = array(
		'context'				=> array( 'type' => 'select', 'label' => 'Template', 'default' => '' ),
		'width'					=> array( 'type' => 'int', 'label' => 'Width', 'default' => 250, 'description' => '(px)' ),
		'height'				=> array( 'type' => 'int', 'label' => 'Height', 'default' => 250, 'description' => '(px)' ),
		'widget_class'	=> array( 'type' => 'text', 'label' => 'CSS Class', 'default' => '', 'description' => '(optional)' ),
		'sort_by_options'		=> array( 'type' => 'multiselect', 'label' => 'Items in "Sort By" list', 
			'options'	=> array(	// options we always want to show even if they are not part of the filter set
				'location.address'	=> 'Address', 
				'cur_data.price'	=> 'Price',
				'cur_data.sqft'		=> 'Square Feet',
				'cur_data.lt_sz'	=> 'Lot Size',
				'compound_type'		=> 'Listing Type',
				'cur_data.avail_on'	=> 'Available On',
			),
			'default'	=> array('cur_data.price','cur_data.beds','cur_data.baths','cur_data.sqft','location.locality','location.postal'), 
		),
		'sort_by'				=> array( 'type' => 'select', 'label' => 'Default sort by', 'options' => array(), 'default' => 'cur_data.price' ),
		'sort_type'				=> array( 'type' => 'select', 'label' => 'Default sort direction', 'options' => array('asc'=>'Ascending', 'desc'=>'Descending'), 'default' => 'desc' ),
		'hide_sort_by'			=> array( 'type' => 'checkbox', 'label' => 'Hide "Sort By" dropdown', 'default' => false ),
		'hide_sort_direction'	=> array( 'type' => 'checkbox', 'label' => 'Hide "Sort Direction" dropdown', 'default' => false ),
		'hide_num_results'		=> array( 'type' => 'checkbox', 'label' => 'Hide "Show # entries" dropdown', 'default' => false ),
		// TODO: sync up with js list			
		'query_limit'			=> array( 'type' => 'int', 'label' => 'Number of results to display', 'default' => 10 ),
	);

	private $_template = array(
		'no_listings'	=> array(
				'type' => 'textarea',
				'label' => 'HTML to display if the user has not set any favorites',
				'css' => 'mime_html',
				'default' => '<p>You have not added any properties to your favorites list yet.</p>',
				'description' => 'You can use any valid HTML in this field.'
		),
				
		'not_logged_in'	=> array(
				'type' => 'textarea',
				'label' => 'HTML to display if the user is not logged in',
				'css' => 'mime_html',
				'default' => '<p>Please login to view your favorite listings.</p>',
				'description' => 'You can use any valid HTML in this field.'
		)
	);



	public static function init() {
		parent::_init(__CLASS__);
	}

	public function __construct() {
		parent::__construct(__CLASS__);
		$this->template = $this->_template + $this->template;
		add_shortcode($this->shortcode, array($this, 'shortcode_handler'));
	}

	public function get_filters_list($with_choices = false) {
		return array();
	}

	/**
	 * Called when a shortcode is found in a post.
	 * @param array $atts
	 * @param string $content
	 */
	public function shortcode_handler($atts) {
		if (!empty($atts['id'])) {
			// if we are a custom shortcode fetch the record so we can display the correct options
			$options = PL_Shortcode_CPT::get_shortcode_options('favorite_listings', $atts['id']);
			if ($options!==false) {
				$atts = wp_parse_args($atts, $options);
			}
			else {
				unset($atts['id']);
			}
		}

		$atts = wp_parse_args($atts, array('limit' => 0, 'sort_type' => ''));
		$context = empty($atts['context']) ? 'shortcode' : $atts['context'];
		$atts['context'] = 'favorite_listings_'.$context;
		$atts['property_ids'] = PL_People_Helper::get_favorite_ids();
		
		if (!has_filter('pls_listings_' . $atts['context'])) {
			add_filter('pls_listings_' . $atts['context'], array('PL_Component_Entity','pls_listings_callback'), 10, 5);
			add_filter('pls_listing_' . $atts['context'], array('PL_Component_Entity','pls_listing_callback'), 10, 4);
		}

		if (empty($atts['property_ids']) && !is_admin()) {
			$template = PL_Shortcode_CPT::load_template($context, 'favorite_listings');
			if (is_user_logged_in()) {
				if (isset($template['no_listings'])) {
					return $template['no_listings'];
				}
				// TODO: move to load_template
				return $this->template['no_listings']['default'];
			}
			if (isset($template['not_logged_in'])) {
				return $template['not_logged_in'];
			}
			// TODO: move to load_template
			return $this->template['not_logged_in']['default'];
		}

		return PLS_Partials::get_listings($atts);
	}
}

PL_Favorite_Listing_CPT::init();