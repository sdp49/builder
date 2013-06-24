<?php
/**
 * Base class for creating a custom post type based on a shortcode.
 * Subclass this for each shortcode to provide an admin suitable for that shortcode.
 */

abstract class PL_SC_Base {

	// subclass should use this to set its post type
	protected static $pl_post_type = '';
	// subclass should use this to set its shortcode
	protected static $shortcode = '';
	// subclass should use this for form/widget titles, etc
	protected static $title = '';
	// help text
	protected static $help = '';
	// subclass should use this for basic display options/shortcode arguments
	protected static $options = array(
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
	//	'<field_name>'		=> array(
	//			'type'		=> '[text|numeric|select|subgrp]'	// type of form control:
	//															// text:	text field
	//															// numeric:	integer field
	//															// select:	drop list
	//															// subgrp:	contains a subgroup of controls
	//			'label'		=> '<Pretty Form Name>',			// field label for use in a form
	//			'options'	=> array(							// present if control type is 'select'
	//				'<value>'	=> '<Pretty Form Name>',		// field label for use in a form
	//				...
	//			),
	//			'default'	=> '<default val>'					// default value - type should be appropriate to the control type
	//	),
	);
	// subclass should use this for a list of shortcode filter subcodes
	protected static $filters = array(
		//		'<field_name>'		=> array(
		//			'type'		=> '[text|select|subgrp]'		// type of form control
		//														// text:	text field
		//														// select:	drop list
		//														// subgrp:	contains a group of filters
		//			'label'		=> '<Pretty Form Name>',		// field label for use in a form
		//			'default'	=> '<default val>'				// default value - type should be appropriate to the control type
		//	),
	);
	// subclass should use this for a list of shortcode subcodes
	protected static $subcodes = array(
		//		'<subcode_name>'	=> array(
		//			'help'		=> '<help text>'				// description of what the subcode does
		//	),
	);
	// tags allowed inside text boxes
	protected static $allowable_tags = "<a><p><script><div><span><section><label><br><h1><h2><h3><h4><h5><h6><scr'+'ipt><style><article><ul><ol><li><strong><em><button><aside><blockquote><footer><header><form><nav><input><textarea><select>";
	// built in templates 
	// TODO: build dynamically
	protected static $default_tpls = array('twentyten', 'twentyeleven');
	// default layout for template
	protected static $template = array(							// defines template fields
		//		'snippet_body'	=> array(
		//		'type'		=> 'textarea',
		//		'label'		=> '<Pretty Form Name>',
		//		'default'	=> '',
		//	),
	);




	/**
	 * Create an instance and register it with the custom shortcode manager
	 */
	public static function init() {
		$class = get_called_class();
 		if (class_exists('PL_Shortcode_CPT')) {
 			PL_Shortcode_CPT::register_shortcode($class::$shortcode, new $class);
 		}
	}

	public function __construct() {
 		add_action( 'template_redirect', array( $this, 'post_type_templating' ) );
	}

	/**
	 * Return the parameters that describe this shortcode type
	 * @return multitype:
	 */
	public function get_args() {
		if (empty($this::$filters)) {
			$this::$filters = $this->_get_filters();
		}
		return array(
				'shortcode'		=> $this::$shortcode,
				'post_type'		=> $this::$pl_post_type,
				'title'			=> $this::$title,
				'help'			=> $this::$help,
				'options'		=> $this::$options,
				'filters'		=> $this::$filters,
				'subcodes'		=> $this::$subcodes,
				'default_tpls'	=> $this::$default_tpls,
				'template'		=> $this::$template,
		);
	}


	/*******************************************
	 * Override the following as necessary
	 *******************************************/


	/**
	 * Called when the post is being formatted for display
	 * @param unknown $single
	 * @param string $skipdb
	 */
	public function post_type_templating( $single, $skipdb = false ) {
		global $post;

		if( ! empty( $post ) ) {
			$meta_custom = get_post_custom( $post->ID );
			if ($post->post_type === $this::$pl_post_type ||
				($post->post_type == 'pl_general_widget' && !empty($meta_custom['shortcode_type']) && $meta_custom['shortcode_type'][0]==$this::$pl_post_type)) {

				unset( $_GET['skipdb'] );
				$meta = $_GET;

				// verify if skipdb param is passed
				if( ! $skipdb ) {
					$meta = array_merge( $meta_custom, $meta );
				}

				// prepare args
				$args = '';
				$class_options = $this::$options;
				foreach($meta as $option=>$value) {
					if (!empty($value) && $value[0]) {
						// only output options that are valid for this type and not default
						if (!empty($class_options[$option])
							&& $class_options[$option]['default']!=$value[0]
							&& $class_options[$option]['type'] != 'featured_listing_meta'
						) {
							$args .= ' '.$option."='".$value[0]."'";
						}
						elseif( $option == 'context' ) {
							$args .= " context='search_listings_{$value[0]}'";
						}
					}
				}

				$shortcode = '[' . $this::$shortcode . $args;

				// prepare filters
				$filters = !empty($meta['pl_static_listings_option']) ? unserialize( $meta['pl_static_listings_option'][0] ) : array();
				$subcodes = '';
				if( is_array( $filters) ) {
					$class_filters = $this::$filters;
					foreach($filters as $filter=>$values) {
						if (!empty($class_filters[$filter])) {
							if( $class_filters[$filter]['type'] == 'subgrp' && is_array($values)) {
								foreach( $values as $key => $value ) {
									$subcodes .= ' [pl_filter group="' . $filter. '" filter="' . $key . '" value="' . $value . '"] ';
								}
							} else {
								$subcodes .= ' [pl_filter filter="' . $filter . '" value="'. $values . '"] ';
							}
						}
					}
				}

				// build the shortcode
				if ($subcodes) {
					$shortcode = $shortcode . ']'.$subcodes.'[/'.$this::$shortcode.']';
				}
				else {
					$shortcode .= ']';
				}

				include PL_LIB_DIR . '/post_types/pl_post_types_template.php';

				die();
			}
		}
	}

	/**
	 * Return array of filters used to configure this shortcode
	 */
	protected function _get_filters() {return array();}


	/**
	 * Generate a shortcode for this shortcode type from arguments
	 * @param string $shortcode_type	: shortcode type we will be generating
	 * @param array $args				: shortcode post type record including postmeta values
	 * @return string					: returned shortcode
	 */
	public static function generate_shortcode_str($args) {
		
		$class = get_called_class();

		// prepare args
		$sc_args = '';
		$class_options = $class::$options;
		foreach($args as $option => $value) {
			if (!empty($value)) {
				// only output options that are valid for this type and not default
				if (!empty($class_options[$option])
					//&& $class_options[$option]['default'] != $value
					&& $class_options[$option]['type'] != 'featured_listing_meta'
					) {
					$sc_args .= ' '.$option."='".$value."'";
				}
			}
		}

		$shortcode = '[' . $class::$shortcode . $sc_args;

		// prepare filters
		$subcodes = '';
		$class_filters = $class::$filters;
		foreach($class_filters as $f_id => $f_atts) {
			if (!empty($args[$f_id])) {
				if(count($f_atts) && empty($f_atts['type'])) {
					// probably group filter
					if (is_array($args[$f_id])) {
						foreach( $f_atts as $key => $value ) {
							if (!empty($args[$f_id][$key]) && $args[$f_id][$key]!='false') { 
								$subcodes .= " [pl_filter group='" . $f_id. "' filter='" . $key . "' value='" . $args[$f_id][$key] . "'] ";
							}
						}
					}
				} 
				else {
					if (!empty($f_atts['type']) && $f_atts['type']=='multiselect') {
						if (is_array($args[$f_id])) {
							$subcodes .= " [pl_filter filter='" . $f_id . "' value='". implode(',', $args[$f_id]) . "'] ";
						}
					}
					else {
						if (!is_array($args[$f_id]) && $args[$f_id]!='false') {
							$subcodes .= " [pl_filter filter='" . $f_id . "' value='". $args[$f_id] . "'] ";
						}
					}
				}
			}
		}

		// build the shortcode
		if ($subcodes) {
			$shortcode = $shortcode . ']'.$subcodes."[/".$class::$shortcode."]";
		}
		else {
			$shortcode .= ']';
		}

		return $shortcode;
	}
}
