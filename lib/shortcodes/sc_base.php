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
		'pl_cpt_template'	=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
	//	'<field_name>'		=> array(
	//			'type'		=> '[text|numeric|select|subgrp]' // type of form control:
	//														// text:	text field
	//														// numeric:	integer field
	//														// select:	drop list
	//														// subgrp:	contains a subgroup of controls
	//			'label'		=> '<Pretty Form Name>',		// field label for use in a form
	//			'options'	=> array(						// present if control type is 'select'
	//				'<value>'	=> '<Pretty Form Name>',	// field label for use in a form
	//				...
	//			),
	//			'default'	=> '<default val>'				// default value - type should be appropriate to the control type
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
	protected static $default_tpl = array('twentyten', 'twentyeleven');
	// default layout for template
	protected static $template = array(							// defines template fields and how they hook in to the shortcode template render
//			'snippet_body'	=> array(
//				'type'		=> 'textarea',
//				'label'		=> '<Pretty Form Name>',
//				'default'	=> '',
//				'hook'		=> '<filter_hook>', 				// optional, requires 'handle_as', name of filter hook prefix used to render this field
//				'handle_as'	=> '[body|before|after|css]',		// optional, describes how we will render the content of this field
//			),
	);




	/**
	 * Create an instance and register it with the shortcode manager
	 */
	public static function init() {
		$class = get_called_class();
 		if (class_exists('PL_Shortcode_CPT')) {
 			PL_Shortcode_CPT::register_shortcode($class::$shortcode, new $class);
 		}
	}

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'meta_box' ), 99999 );
 		add_action( 'save_post', array( $this, 'meta_box_save' ) );
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
				'options'		=> $this::$options,
				'filters'		=> $this::$filters,
				'default_tpl'	=> $this::$default_tpl,
				'template'		=> $this::$template,
		);
	}


	/*******************************************
	 * Override the following as necessary
	 *******************************************/


	/**
	 * Called when the admin form is being displayed for this post type
	 */
	public function meta_box() {}

	/**
	 * Called when saving from the shortcode edit forms
	 * @param int $post_id
	 */
	public function meta_box_save($post_id) {

		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// Verify nonces for ineffective calls
		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_cpt_meta_box_nonce' ) ) {
			return;
		}

		$this->_save($post_id, $_POST);
	}

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
						elseif( $option == 'pl_cpt_template' ) {
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
	protected function _get_filters() {}


	/*******************************************
	 * Private
	 *******************************************/


	/**
	 * Save all the postmeta fields
	 * @param int $post_id		: post id
	 * @param array $args		: $_POST data to be validated and saved
	 * @param bool $test		: true to just generate the array of postmeta without actually saving
	 * 							  useful if we want to generate a shortcode for preview without actually
	 * 							  updating the record
	 * @return array			: returns an array of all the post meta data that would be saved
	 */
	protected function _save($post_id, $args, $test=false) {

		$record = array();

		// Verify we didn't get called for some other post type
		if ( empty($args['post_type']) ||
			($args['post_type'] != $this::$pl_post_type &&
					($args['post_type'] != 'pl_general_widget' || empty($args['shortcode_type'])))) {
			return $record;
		}

		if ($post_id) {

			if ($args['post_type'] == 'pl_general_widget') {
				// we are using one of our shortcode types so fetch the class so we can validate the data
				$class = 'PL_'.ucfirst(substr($args['shortcode_type'],3)).'_CPT';
				if (!class_exists($class)) {
					return $record;
				}
				// our field values are in an array based on the shortcode type
				$args = array_merge($args, $args[ $args['shortcode_type']]);
				if (!$test) {
					update_post_meta( $post_id, 'pl_post_type', $args['shortcode_type']);
				}
			}
			else {
				$class = $this;
			}

			// Save options
			foreach( $class::$options as $option => $values ) {
				if( !empty($args) && !empty($args[$option])) {
					switch($values['type']) {
						case 'checkbox':
							update_post_meta( $post_id, $option, !empty($args[$option]) ? true : false);
							break;
						case 'numeric':
							$args[$option] = (int)$args[$option];
						case 'select':
						case 'text':
							if (!$test) {
								update_post_meta( $post_id, $option, $args[$option] );
							}
							$record[$option] = $args[$option];
					}
				}
				else {
					// save default in case default changes in the future
					if (!$test) {
						update_post_meta( $post_id, $option, $values['default'] );
					}
				}
			}

			// Save filters - only save if they diverge from default
			$filters = array();
			foreach( $class::$filters as $filter => $values ) {
				if( !empty($args) && !empty($args[$filter])) {
					if ($values['type'] == 'subgrp') {
						$subargs = $args[$filter];
						foreach($values['subgrp'] as $subfilter => $sf_values) {
							if(!empty($subargs[$subfilter]) && $subargs[$subfilter] !== $sf_values['default']) {
								$filters[$filter][$subfilter] = $subargs[$subfilter];
							}
						}
					}
					elseif($args[$filter] !== $values['default']) {
						$filters[$filter] = $args[$filter];
					}
				}
			}
			if (!$test) {
				update_post_meta($post_id, 'pl_static_listings_option', $filters);
			}
			$record['pl_static_listings_option'] = $filters;
		}

		// Save template id
		if( isset( $args['pl_cpt_template'] ) ) {
			if (!$test) {
				update_post_meta( $post_id, 'pl_cpt_template', $args['pl_cpt_template']);
			}
			$record['pl_cpt_template'] = $args['pl_cpt_template'];
		}
		return $record;
	}

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
		$class_options = array_merge(array('context'=>array('type'=>'text', 'default'=>'')), $class::$options);
		foreach($args as $option => $value) {
			if (!empty($value)) {
				// only output options that are valid for this type and not default
				if (!empty($class_options[$option])
					&& $class_options[$option]['default'] != $value
					&& $class_options[$option]['type'] != 'featured_listing_meta'
					) {
					$sc_args .= ' '.$option."='".$value."'";
				}
			}
		}

		$shortcode = '[' . $class::$shortcode . $sc_args;

		// prepare filters
		$filters = !empty($args['pl_static_listings_option']) ? $args['pl_static_listings_option'] : array();
		$subcodes = '';
		if( is_array( $filters) ) {
			$class_filters = $class::$filters;
			foreach($filters as $filter => $values) {
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
			$shortcode = $shortcode . ']'.$subcodes.'[/'.$class::$shortcode.']';
		}
		else {
			$shortcode .= ']';
		}

		return $shortcode;
	}
}
