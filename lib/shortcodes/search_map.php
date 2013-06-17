<?php
/**
 * Post type/Shortcode to display Google maps
 *
 */

class PL_Map_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_map';

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

	protected static $template = array(
		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 'default' => '',
								'hook'=>'', 'handle_as'=>'css' ),
		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the widget', 'default' => '',
								'hook'=>'', 'handle_as'=>'header' ),
		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the widget', 'default' => '',
								'hook'=>'', 'handle_as'=>'footer' ),
	);
}

PL_Map_CPT::init(__CLASS__);
