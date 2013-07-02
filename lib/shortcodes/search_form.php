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
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
		'ajax'				=> array( 'type' => 'checkbox', 'label' => 'Disable AJAX', 'default' => false ),
		'formaction'		=> array( 'type' => 'text', 'label' => 'Form URL when AJAX is disabled', 'default' => '' ),
		'modernizr'			=> array( 'type' => 'checkbox', 'label' => 'Drop Modernizr', 'default' => false ),
	);

	//TODO build from the api
	protected static $subcodes = array(
		'bedrooms'			=> array('help' => 'Drop list to select an exact number of bedrooms.'),
		'min_beds'			=> array('help' => 'Drop list to select the minimum number of bedrooms.'),
		'max_beds'			=> array('help' => 'Drop list to select the maximum number of bedrooms.'),
		'bathrooms'			=> array('help' => 'Drop list to select an exact number of bathrooms.'),
		'min_baths'			=> array('help' => 'Drop list to select the minimum number of bathrooms.'),
		'max_baths'			=> array('help' => 'Drop list to select the maximum number of bathrooms.'),
		'property_type'		=> array('help' => 'Drop list to select the property type.'),
		'listing_types'		=> array('help' => 'Drop list to select the listing type (housing/land/etc).'),
		'zoning_types'		=> array('help' => 'Drop list to select the zoning (commercial/residential/etc).'),
		'purchase_types'	=> array('help' => 'Drop list to select the purchase type (rent/buy).'),
		'available_on'		=> array('help' => 'Drop list to select date of when the property should be available.'),
		'cities'			=> array('help' => 'Drop list to select a city.'),
		'states'			=> array('help' => 'Drop list to select a state.'),
		'zips'				=> array('help' => 'Drop list to select a zip/postal code.'),
		'min_price'			=> array('help' => 'Drop list to select the minimum price.'),
		'max_price'			=> array('help' => 'Drop list to select the maximum price.'),
		'min_price_rental'	=> array('help' => 'Drop list to select the minimum rental price.'),
		'max_price_rental'	=> array('help' => 'Drop list to select the maximum rental price.'),
	);

	protected static $template = array(
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'HTML', 
			'default' => '
Put subcodes here to build your form, e.g.:

<div><label>Bedrooms:</label>[bedrooms]</div>
<div><label>Bathrooms:</label>[bathrooms]</div>',
			'description'	=> '
You can use any valid HTML in this field to format the subcodes.' ),

		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 
			'default' => '
/* sample div used to wrap the slideshow plus any addiitonal html */
.my-searchform {
	float: left;
	border: 1px solid #000;
	padding: 10px;
}
/* line up the drop lists */
.my-searchform label {
	display: block;
	float: left;
	width: 10em;
}',
			'description'	=> '
You can use any valid CSS in this field to customize the form, which will also inherit the CSS from the theme.' ),

		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the form', 'default' => '<div class="my-searchform">',
			'description'	=> '
You can use any valid HTML in this field and it will appear before the form.
For example, you can wrap the whole form with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.' ),

		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the form', 'default' => '</div>',
			'description'	=> '
You can use any valid HTML in this field and it will appear after the form.' ),
	);
}

PL_Form_CPT::init(__CLASS__);
