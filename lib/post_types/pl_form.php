<?php
/**
 * Post type/Shortcode to generate a property search form
 *
 */

class PL_Form_CPT extends PL_Post_Base {

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
				



	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Forms', 'pls' ),
						'singular_name' => __( 'pl_form', 'pls' ),
						'add_new_item' => __('Add New Form', 'pls'),
						'edit_item' => __('Edit Form', 'pls'),
						'new_item' => __('New Form', 'pls'),
						'all_items' => __('All Forms', 'pls'),
						'view_item' => __('View Forms', 'pls'),
						'search_items' => __('Search Forms', 'pls'),
						'not_found' => __('No forms found', 'pls'),
						'not_found_in_trash' => __('No forms found in Trash', 'pls')),
				'menu_icon' => trailingslashit(PL_IMG_URL) . 'featured.png',
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array('title', 'editor'),
				'taxonomies' => array('category', 'post_tag')
		);

		register_post_type('pl_form', $args );
	}
}

new PL_Form_CPT();