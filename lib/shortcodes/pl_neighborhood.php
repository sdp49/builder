<?php
/**
 * Post type/Shortcode to display neighbourhood search form
 *
 */

class PL_Neighborhood_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_neighborhood';

	protected static $shortcode = 'search_neighborhood';

	protected static $title = 'Neighborhood';

	protected static $options = array(
		'pl_cpt_template'	=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'text', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'text', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
	);

	protected static $subcodes = array(
			'nb_title',
			'nb_featured_image',
			'nb_description',
			'nb_link',
			'nb_map'
	);

	public function get_args() {
		return array(
				'shortcode'	=> $this::$shortcode,
				'post_type'	=> $this::$pl_post_type,
				'title'		=> $this::$title,
				'options'	=> $this::$options,
				'filters'	=> $this::$filters,
				'template'	=> $this::$template,
		);
	}

	protected static $template = array(
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'HTML', 'default' => 'Put subcodes here to build your form...' ),
		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 'default' => '' ),
		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the widget', 'default' => '' ),
		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the widget', 'default' => '' ),
	);
}

PL_Neighborhood_CPT::init(__CLASS__);
