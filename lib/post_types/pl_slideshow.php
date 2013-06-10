<?php
/**
 * Post type/Shortcode for displaying the slideshow
 *
 */

class PL_Slideshow_CPT extends PL_Post_Base {

	protected static $post_type = 'pl_slideshow';

	protected static $shortcode = 'listing_slideshow';

	protected static $title = 'Slideshow';

	protected static $options = array(
		'pl_cpt_template'	=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
		'animation' 		=> array( 'type' => 'select', 'label' => 'Animation', 'options' => array(
				'fade' => 'fade',
				'horizontal-slide' => 'horizontal-slide',
				'vertical-slide' => 'vertical-slide',
				'horizontal-push' => 'horizontal-push',
			),
			'default' => 'fade' ),
		'animationSpeed'	=> array( 'type' => 'numeric', 'label' => 'Animation Speed', 'default' => 4000 ),
		'timer'				=> array( 'type' => 'checkbox', 'label' => 'Timer', 'default' => true),
		'pauseOnHover'		=> array( 'type' => 'checkbox', 'label' => 'Pause on hover', 'default' => true ),
		'pl_featured_listing_meta' => array( 'type' => 'featured_listing_meta', 'default' => '' ),
	);

	protected static $subcodes = array(
		'ls_index',
		'ls_url',
		'ls_address',
		'ls_beds',
		'ls_baths',
	);




	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Slideshows', 'pls' ),
						'singular_name' => __( 'slideshow', 'pls' ),
						'add_new_item' => __('Add New Slideshow', 'pls'),
						'edit_item' => __('Edit Slideshow', 'pls'),
						'new_item' => __('New Slideshow', 'pls'),
						'all_items' => __('All Slideshows', 'pls'),
						'view_item' => __('View Slideshows', 'pls'),
						'search_items' => __('Search Slideshows', 'pls'),
						'not_found' =>  __('No slideshows found', 'pls'),
						'not_found_in_trash' => __('No slideshows found in Trash', 'pls')),
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

		register_post_type('pl_slideshow', $args );
	}
}

new PL_Slideshow_CPT();