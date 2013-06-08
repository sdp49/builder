<?php
/**
 * Main Post Base class
 *
 * Defines a skeleton for displaying and configuring our shortcodes
 */

abstract class PL_Post_Base {

	// subclass should use this to set its post type
	protected static $post_type = '';
	// subclass should use this to set its shortcode
	protected static $shortcode = '';
	// subclass should use this for basic display options/shortcode arguments
	protected static $options = array(
				'width'			=> array( 'type' => 'numeric', 'label' => 'Width', 'default' => 250 ),
				'height'		=> array( 'type' => 'numeric', 'label' => 'Height', 'default' => 250 ),
				'widget_class'	=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
		//		'<field_name>'		=> array( 
		//			'type'		=> '[text|numeric|select|subgrp|featured_listing_meta]'// type of form control
		//														// text:	text field
		//														// numeric:	integer field
		//														// select:	drop list
		//														// subgrp:	contains a subgroup of controls
		//														// featured_listing_meta: this field contains a list of featured listings
		//														// use the featured listings form to pick them
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
		//		'field_name'		=> array( 
		//			'type'		=> '[text|select|subgrp]'		// type of form control
		//														// text:	text field
		//														// select:	drop list
		//														// subgrp:	contains a group of filters		 
		//			'label'		=> 'Pretty Form Name',			// field label for use in a form 
		//			'default'	=> '<default val>'				// default value - type should be appropriate to the control type  
		//	),
		);
	// subclass should use this for a list of shortcode subcodes
	protected static $subcodes = array();




	public function __construct() {
		$this->init();
	}
	
	public function init() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'meta_box' ), 99999 );
 		add_action( 'save_post', array( $this, 'meta_box_save' ) );
 		add_action( 'template_redirect', array( $this, 'post_type_templating' ) );
	}	
	
	
	/*******************************************
	 * Override the following as necessary
	 *******************************************/
	
	/**
	 * Register the post type if desired
	 */
	public function register_post_type() {}
	
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
		
		// Verify we didn't get called for some other post type
		if ( empty($_POST['post_type']) || $_POST['post_type'] != 'pl_general_widget' || $_POST['pl_post_type'] != $this::$post_type ) {
			return;
		}

		if ($post_id) {
			// Save options
			foreach( $this::$options as $option => $values ) {
				if( !empty($_POST) && !empty($_POST[$option])) {
					switch($values['type']) {
						case 'subgrp':
							update_post_meta( $post_id, $option, (array)$_POST[$option] );
							break;
						case 'checkbox':
							update_post_meta( $post_id, $field, !empty($_POST[$field]) ? true : false);
							break;
						case 'text':
						case 'numeric':
						case 'featured_listing_meta':
						default:
							update_post_meta( $post_id, $option, $_POST[$option] );
					}
				}
				else {
					// save default in case default changes in the future
					update_post_meta( $post_id, $option, $values['default'] );
				}
			}

			// Save filters - only save if they diverge from default
			$filters = array();
			foreach( $this::$filters as $filter => $values ) {
				if( !empty($_POST) && !empty($_POST[$filter])) {
					if ($values['type'] == 'subgrp') {
						$filters[$filter] = (array)json_decode(stripslashes($_POST[$filter]), true);
					}
					elseif($_POST[$filter] !== $values['default']) {
						$filters[$filter] = $_POST[$filter];
					}
				}
			}
			update_post_meta($post_id, 'pl_static_listings_option', $filters);
		}
		
		// Save template
		if( isset( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template']);
		}
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
			if ($post->post_type === $this::$post_type ||
				($post->post_type == 'pl_general_widget' && !empty($meta_custom['pl_post_type']) && $meta_custom['pl_post_type'][0]==$this::$post_type)) {
				
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
				$filters = unserialize( $meta['pl_static_listings_option'][0] );
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
}