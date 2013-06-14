<?php
/**
 * Post type/Shortcode to generate a property search form
 *
 */

class PL_Form_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_form';

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

	//TODO build from the api
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

	protected static $template = array(
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'HTML', 'default' => 'Put subcodes here to build your form...',
								'hook'=>'pls_listings_search_form_outer_', 'handle_as'=>'body' ),
		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 'default' => '',
								'hook'=>'search_form_pre_header', 'handle_as'=>'css' ),
		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the widget', 'default' => '',
								'hook'=>'search_form_pre_header', 'handle_as'=>'header' ),
		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the widget', 'default' => '',
								'hook'=>'search_form_post_footer', 'handle_as'=>'footer' ),
	);
}

PL_Form_CPT::init(__CLASS__);
