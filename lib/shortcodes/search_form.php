<?php
/**
 * Post type/Shortcode to generate a property search form
 *
 */

class PL_Form_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_form';

	protected static $shortcode = 'search_form';

	protected static $title = 'Search Form';

	protected static $help = 
		'<p>
		You can insert your "activated" Search Form snippet by using the [search_form] shortcode in a page or a post. 
		This control is intended to be used alongside the [search_listings] shortcode to display the search 
		form\'s results.
		</p>';

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
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'HTML', 
				'default' => "Put subcodes here to build your form, e.g.:\n<br>Bedrooms: [bedrooms]<br>\nBathrooms: [bathrooms]" ),
		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 'default' => '' ),
		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the template', 'default' => '' ),
		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the template', 'default' => '' ),
	);
}

PL_Form_CPT::init(__CLASS__);
