<?php
/**
 * Post type/Shortcode to display Google maps
 *
 */

class PL_Map_CPT extends PL_SC_Base {

	protected $shortcode = 'search_map';

	protected $title = 'Map';

	protected $options = array(
		'context'		=> array( 'type' => 'select', 'label' => 'Template', 'default' => '' ),
		'width'			=> array( 'type' => 'int', 'label' => 'Width', 'default' => 600, 'description' => '(px)' ),
		'height'		=> array( 'type' => 'int', 'label' => 'Height', 'default' => 400, 'description' => '(px)' ),
		'widget_class'	=> array( 'type' => 'text', 'label' => 'CSS Class', 'default' => '', 'description' => '(optional)' ),
//		'type'			=> array( 'type' => 'select', 'label' => 'Map Type',
//				'options' => array('listings' => 'listings', 'lifestyle' => 'lifestyle', 'lifestyle_polygon' => 'lifestyle_polygon' ),
//				'default' => '' ),
	);

	protected $template = array(
		'css' => array(
			'type' => 'textarea',
			'label' => 'CSS',
			'css' => 'mime_css',
			'description' => 'You can use any valid CSS in this field to customize your map, which will also inherit the CSS from the theme.'
		),

		'before_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content before the map',
			'css' => 'mime_html',
			'description' => 'You can use any valid HTML in this field and it will appear before the map.
For example, you can wrap the whole map with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.'
		),

		'after_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content after the map',
			'css' => 'mime_html',
			'description' => 'You can use any valid HTML in this field and it will appear after the map.'
		),
	);




	public static function init() {
		parent::_init(__CLASS__);
	}

	public static function shortcode_handler($atts, $content) {
		$content = PL_Component_Entity::search_map_entity( $atts );

		return self::wrap( 'search_map', $content );
	}
}

PL_Map_CPT::init();
