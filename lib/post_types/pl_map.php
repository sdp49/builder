<?php
/**
 * Post type/Shortcode to display Google maps
 *
 */

class PL_Map_CPT extends PL_Post_Base {

	protected static $post_type = 'pl_map';

	protected static $shortcode = 'search_map';

	protected static $title = 'Map';

	protected static $options = array(
		'pl_cpt_template'	=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
//		'type' 				=> array( 'type' => 'select', 'label' => 'Map Type',
//				'options' => array('listings' => 'listings', 'lifestyle' => 'lifestyle', 'lifestyle_polygon' => 'lifestyle_polygon' ),
//				'default' => '' ),
	);




	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Maps', 'pls' ),
						'singular_name' => __( 'pl_map', 'pls' ),
						'add_new_item' => __('Add New Map', 'pls'),
						'edit_item' => __('Edit Map', 'pls'),
						'new_item' => __('New Map', 'pls'),
						'all_items' => __('All Maps', 'pls'),
						'view_item' => __('View Maps', 'pls'),
						'search_items' => __('Search Maps', 'pls'),
						'not_found' => __('No maps found', 'pls'),
						'not_found_in_trash' => __('No maps found in Trash', 'pls')),
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

		register_post_type('pl_map', $args );
	}
}

new PL_Map_CPT();