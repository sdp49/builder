<?php
/**
 * Post type/Shortcode to generate a property search form
 *
 */

class PL_Form_CPT extends PL_SC_Base {

	protected static $post_type = 'pl_form';

	protected static $shortcode = 'search_form';
	
	protected static $title = 'Search Form';

	protected static $options = array(
		'pl_cpt_template'	=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
		'ajax'				=> array( 'type' => 'checkbox', 'label' => 'Disable AJAX', 'default' => false ),
		'formaction'		=> array( 'type' => 'text', 'label' => 'Form URL when AJAX is disabled', 'default' => '' ),
		'modernizr'			=> array( 'type' => 'checkbox', 'label' => 'Drop Modernizr', 'default' => false ),
	);
	
	protected static $subcodes = array(
					'bedrooms',
					'min_beds',
					'max_beds',
					'bathrooms',
					'min_baths',
					'max_baths',
					'price',
					'half_baths',
					'property_type',
					'listing_types',
					'zoning_types',
					'purchase_types',
					'available_on',
					'cities',
					'states',
					'zips',
					'neighborhood',
					'county',
					'min_price',
					'max_price',
					'min_price_rental',
					'max_price_rental'	
	);
}

PL_Form_CPT::init(__CLASS__);
