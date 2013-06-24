<?php
/**
 * Manage available shortcodes that can be customized via the admin pages.
 * The customized shortcodes are stored as a custom post type of pl_general_widget.
 * Each references a shortcode template/layout that controls how its drawn.
 * The templates come from a file in the (Placester aware) theme or are user defined.
 */

class PL_Shortcode_CPT {

	// holds instances of shortcodes we have installed
	private static $shortcodes = array();
	// holds the configuration parameters for the shortcode classes we have installed
	private static $shortcode_config = array();

	
	

	/**
	 * Called by shortcode object to register itself
	 * @param string $shortcode	: shortcode
	 * @param object $instance	: instance of shortcode object
	 */
	public static function register_shortcode($shortcode, $instance) {
		self::$shortcodes[$shortcode] = $instance;
	}

	public function __construct() {

		// get list of shortcodes that can be widgetized:
		$path = trailingslashit( PL_LIB_DIR ) . 'shortcodes/';
		$ignore = array('sc_base.php', 'pl_neighborhood.php');
		include_once($path . 'sc_base.php');
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if (pathinfo($file, PATHINFO_EXTENSION) == 'php' && !(in_array($file, $ignore))) {
					include($path . $file);
				}
			}
			closedir($handle);
		}

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'wp_ajax_pl_sc_changed', array( $this, 'ajax_shortcode_changed') );
		add_action( 'wp_ajax_pl_sc_preview', array( $this, 'shortcode_preview') );
		add_action( 'wp_ajax_pl_sc_template_preview', array( $this, 'template_preview') );
		add_filter( 'get_edit_post_link', array( $this, 'shortcode_edit_link' ), 10, 3);
	}

	/**
	 * Register the CPT used to create customized shortcodes
	 */
	public function register_post_type() {

		// custom post type to hold a customized shortcode
		$args = array(
			'labels' => array(
				'name' => __( 'Placester Widget', 'pls' ),
				'singular_name' => __( 'pl_map', 'pls' ),
				'add_new_item' => __('Add New Placester Widget', 'pls'),
				'edit_item' => __('Edit Placester Widget', 'pls'),
				'new_item' => __('New Placester Widget', 'pls'),
				'all_items' => __('All Placester Widgets', 'pls'),
				'view_item' => __('View Placester Widgets', 'pls'),
				'search_items' => __('Search Placester Widgets', 'pls'),
				'not_found' =>  __('No widgets found', 'pls'),
				'not_found_in_trash' => __('No widgets found in Trash', 'pls')),
			'menu_icon' => trailingslashit(PL_IMG_URL) . 'logo_16.png',
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => false,
			'query_var' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title'),
		);

		register_post_type('pl_general_widget', $args );
	}


	/**
	 * Return a list of available shortcodes
	 * @return array	: array of shortcode types
	 */
	public static function get_shortcode_list() {
		return self::$shortcodes;
	}

	/**
	 * Return an array of shortcodes with their respective arguments that can be used to
	 * construct admin pages for creating a custom instance of a shortcode
	 * @return array	: array of shortcode type arrays
	 */
	public static function get_shortcodes($shortcode='') {
		if (empty(self::$shortcode_config)) {
			foreach(self::$shortcodes as $shortcode => $instance){
				self::$shortcode_config[$shortcode] = $instance->get_args();
			}
		}
		if ($shortcode) {
			return isset(self::$shortcode_config[$shortcode]) ? self::$shortcode_config[$shortcode] : array();
		}
		return self::$shortcode_config;
	}


	/***************************************************
	 * Admin pages
	 ***************************************************/


	public function shortcode_edit_link($url, $ID, $context) {
		global $pagenow;
		if (get_post_type($ID) == 'pl_general_widget') {
			if ($pagenow == 'admin.php') {
				return admin_url('admin.php?page=placester_shortcodes_shortcode_edit&ID='.$ID);
			}
			elseif ($pagenow == 'post.php') {
				return admin_url('admin.php?page=placester_shortcodes');
			}
		}
		return $url;
	}


	/***************************************************
	 * Custom Shortcode helper functions
	 ***************************************************/
	
	public function ajax_shortcode_changed() {
		$response = array('sc_str'=>'');

		// generate shortcode string
		if ( isset($_POST['shortcode']) && !empty($_POST[$_POST['shortcode']])) {
			$args = array_merge($_POST, $_POST[$_POST['shortcode']]);
			$response['sc_str'] = $this->generate_shortcode_str($_POST['shortcode'], $args);
			$response['width'] = $args['width'];
			$response['height'] = $args['height'];
		}
		
		header( "Content-Type: application/json" );
		echo json_encode($response);
		die;
	}
	
	
	/**
	 * Helper function to generate a shortcode string from a set of arguments
	 */
	public static function generate_shortcode_str($shortcode, $args) {
		if (empty($shortcode) || empty(self::$shortcodes[$shortcode])) {
			return '';
		}
		return self::$shortcodes[$shortcode]->generate_shortcode_str($args);
	}

	/**
	 * Generate preview for the shortcode edit page.
	 */
	public function shortcode_preview() {
	
		$sc_str = '';
		$sc_id = (!empty($_GET['sc_id']) ? stripslashes($_GET['sc_id']) : '');
		if ($sc_id) {
			$sc_attrs = $this->load_shortcode($sc_id);
			if (!empty($sc_attrs)) {
				$sc_str = $this->generate_shortcode_str($sc_attrs['shortcode'], $sc_attrs);
			}
		}
		if (!empty($_GET['sc_str'])) {
			$sc_str = stripslashes($_GET['sc_str']);
		}
			
		include(PL_VIEWS_ADMIN_DIR . 'shortcodes/preview.php');
		die;
	}
	
	/**
	 * Get filter settings for the custom shortcode
	 * @param string $id	: id of a saved custom shortcode
	 * @return array
	 */
	public static function get_shortcode_filters($id) {
		if ($post = get_post($id, ARRAY_A, array('post_type'=>'pl_general_widget'))) {
			$postmeta = get_post_meta($id);
			$p_shortcode = $postmeta['shortcode'][0];
			if (!empty($postmeta['pl_'.$p_shortcode.'_option'])) {
				$filters = maybe_unserialize($postmeta['pl_'.$p_shortcode.'_option'][0]);
				return $filters;
			}
		}
		return array();
	}
	
	/**
	 * Get option settings for the custom shortcode
	 * @param string $id	: id of a saved custom shortcode
	 * @return array
	 */
	public static function get_shortcode_options($id) {
		if ($post = get_post($id, ARRAY_A, array('post_type'=>'pl_general_widget'))) {
			$postmeta = get_post_meta($id);
			$p_shortcode = $postmeta['shortcode'][0];
			$sc_attrs = self::get_shortcodes($p_shortcode);
			$options = array();
			foreach($sc_attrs['options'] as $attr=>$vals) {
				if ($attr=='context') {
					$key = 'pl_cpt_template';
				}
				else {
					$key = $attr;
				}
				if (isset($postmeta[$key])) {
					$options[$attr] = maybe_unserialize($postmeta[$key][0]);
				}
			}
			return $options;
		}
		return array();
	}
	
	
	/***************************************************
	 * Custom Shortcode storage functions
	 ***************************************************/


	/**
	 * Fetch custom shortcode attributes using record id.
	 * @param int $id			: shortcode record id
	 * @param string $shortcode	: optional shortcode type as sanity check
	 * @return array			: custom shortcode's attributes
	 */
	public static function load_shortcode($id, $shortcode='') {
		if ($post = get_post($id, ARRAY_A, array('post_type'=>'pl_general_widget'))) {
			$postmeta = get_post_meta($id);
			$p_shortcode = $postmeta['shortcode'][0];
			if (!$shortcode || $p_shortcode==$shortcode) {
				$options = array();
				foreach($postmeta as $key=>$val) {
					if ($key=='pl_'.$p_shortcode.'_option') {
						// filters
						$options = maybe_unserialize($val[0]);
						continue;
					}
					elseif ($key=='pl_cpt_template') {
						$key = 'context';
					}
					$post[$key] = maybe_unserialize($val[0]);
				}
				$post = array_merge($post, $options);
				return $post;
			}
		}
		return array();
	}	
	
	/**
	 * Save custom shortcode attributes.
	 * @param int $id			: id of record to update, 0 for new record
	 * @param string $shortcode	: shortcode type as sanity check
	 * @param array $args		: custom shortcode's attributes
	 * @return int				: record id if saved
	 */
	public static function save_shortcode($id, $shortcode, $args) {
		$sc_attrs = self::get_shortcodes($shortcode);
		if (!empty($sc_attrs)) {
			if ($id) {
				// sanity check and make sure we are not changing the shortcode type
				$post = get_post($id);
				$p_shortcode = get_post_meta($id, 'shortcode', true);
				if (empty($post) || $post->post_type!='pl_general_widget' || $p_shortcode != $shortcode) {
					$id = 0;
				}
			}
			if (!$id) {
				// creating new one or changing type
				$id = wp_insert_post(array('post_type'=>'pl_general_widget'));
			}
			if ($id) {
			
				$sc_str = self::generate_shortcode_str($shortcode, $args);
				wp_update_post(array('ID'=>$id, 'post_title'=>$args['post_title'], 'post_content'=>$sc_str, 'post_status'=>'publish'));
				update_post_meta( $id, 'shortcode', $shortcode);
			
				// Save options
				foreach( $sc_attrs['options'] as $option => $values ) {
					if ($option=='context') {
						$key = 'pl_cpt_template';
					}
					else {
						$key = $option;
					}
					switch($values['type']) {
						case 'checkbox':
							// in some places having the option set counts as on.. 
							if (empty($args[$option])) {
								// so delete if not set
								delete_post_meta($id, $key);	
							}
							else {
								update_post_meta($id, $key, 'true');
							}
							break;
						case 'numeric':
							if( !empty($args) && !empty($args[$option])) {
								$args[$option] = (int)$args[$option];
							}
						case 'select':
						case 'text':
						default:
							if( !empty($args) && !empty($args[$option])) {
								update_post_meta($id, $key, trim($args[$option]));
							}
							else {
								// save default in case default changes in the future
								update_post_meta( $id, $key, $values['default'] );
							}
					}
				}
			
				// Save filters - only save if they diverge from default
				$filters = array();
				foreach( $sc_attrs['filters'] as $filter => $values ) {
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
				$db_key = 'pl_'.$shortcode.'_option';
				update_post_meta($id, $db_key, $filters);
			}
			
			return $id;
		}
		return 0;
	}
	
	
	/***************************************************
	 * Shortcode Template helper functions
	 ***************************************************/


	/**
	 * We have to save settings as a template in order for ajax driven forms such as search listings
	 * to work. We always use the same name '_preview' for the template name.
	 */
	public function template_preview() {

		$shortcode = (!empty($_GET['shortcode']) ? stripslashes($_GET['shortcode']) : '');
		$shortcode_args = $this->get_shortcodes();
		if (!$shortcode || empty($shortcode_args[$shortcode]) || empty($_GET[$shortcode])) {
			die;
		}
		// set the defaults
		$template_id = 'pls_'.$shortcode.'___preview';
		$args = wp_parse_args($_GET, array('context'=>$template_id, 'width'=>'250', 'height'=>'250'));
		$sc_str = $this->generate_shortcode_str($shortcode, $args);
		$args = wp_parse_args($_GET[$shortcode], array('shortcode'=>$shortcode, 'title'=>'_preview'));
		$this->save_custom_template($template_id, $args);

		include(PL_VIEWS_ADMIN_DIR . 'shortcodes/preview.php');
		die;
	}

	/**
	 * Checks if the given template is being used and returns the number of custom shortcodes using it
	 * @param string $id
	 * @return int
	 */
	public static function template_in_use($id) {
		global $wpdb;

		return $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(*)
			FROM $wpdb->posts, $wpdb->postmeta
			WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id
			AND $wpdb->postmeta.meta_key = 'pl_cpt_template'
			AND $wpdb->postmeta.meta_value = '%s'
			AND $wpdb->posts.post_type = 'pl_general_widget'", $id));
	}

	
	/***************************************************
	 * Shortcode Template storage functions
	 * TODO: move to model
	 ***************************************************/


	/**
	 * Load a template
	 * @param string $id
	 * @return array
	 */
	public static function load_template($id, $shortcode) {
		$default = array('shortcode'=>'', 'title'=>'');
		
		if ($shortcode && !empty(self::$shortcodes[$shortcode])) {
			// Get template from shortcode's template list in case we are using
			// default or builtin template

			// get custom template list
			$option_key = ('pls_' . $shortcode.'_list');
			$tpl_list = get_option($option_key, array());
			if (!empty($tpl_list) && !empty($tpl_list[$id])) {
				return self::load_custom_template($id);
			}

			// get builtin/default templates
			$sc_attrs = self::get_shortcodes($shortcode);
			if (!empty($sc_attrs['default_tpls']) && in_array($id, $sc_attrs['default_tpls'])) {
				ob_start();
				$filename = (trailingslashit(PL_VIEWS_SHORT_DIR) . trailingslashit($shortcode) . $id . '.php');
				include $filename;
				$default['snippet_body'] = ob_get_clean();
			}
		}		
		return $default;		
	}
	
	public static function load_custom_template($id) {
		$default = array('shortcode'=>'', 'title'=>'');
		if (strpos($id, 'pls_') !== 0) {
			return $default;
		}
		$data = get_option($id, $default);
		if (!is_array($data) || empty($data['shortcode']) || empty($data['title'])) {
			return $default;
		}
		return $data;
	}
	
	
	/**
	 * Save a shortcode template
	 * We save it in the options table using the name:
	 * pls_<shortcode_type>__<unique identifier>
	 * and also track it in a list stored in the option table using the shortcode:
	 * pls_<shortcode_type>_list
	 * @param string $id		: template id
	 * @param string $shortcode	: shortcode name
	 * @param string $title		: user name for the shortcode template
	 * @param array $data		:
	 * @return string			: unique id used to reference the template
	 */
	public static function save_custom_template($id, $atts) {
		$atts = (array)$atts;
		// sanity check
		$shortcode = empty($atts['shortcode'])?'':$atts['shortcode'];
		if (!$shortcode || empty(self::$shortcodes[$shortcode]) || empty($atts['title'])) {
			return '';
		}
		// if we change the shortcode of an existing record create a new one with new shortcode
		if (empty($id) || strpos($id, 'pls_'.$shortcode.'__')!==0) {
			$id = ('pls_' . $shortcode . '__' . time() . rand(10,99));
		}
		$sc_args = self::get_shortcodes();
		$data = $sc_args[$shortcode]['template'] + array('shortcode'=>'', 'title'=>'');
		foreach($data as $key => &$val) {
			if (isset($atts[$key])) {
				$val = stripslashes($atts[$key]);
			}
		}
		update_option($id, $data);

		// Add to the list of custom snippet IDs for this shortcode...
		$tpl_list_DB_key = ('pls_' . $shortcode . '_list');
		$tpl_list = get_option($tpl_list_DB_key, array()); // If it doesn't exist, create a blank array to append...
		$tpl_list[$id] = $data['title'];

		// sort alphabetically
		uasort($tpl_list, array(__CLASS__, '_tpl_list_sort'));
		update_option($tpl_list_DB_key, $tpl_list);
		self::_build_tpl_list($shortcode);
		return $id;
	}

	/**
	 * Delete a template
	 * @param string $id
	 * @return void
	 */
	public static function delete_custom_template($id) {
		// sanity check
		$parts = explode('_', $id);
		if (count($parts) < 4 || $parts[0]!=='pls') {die;
			return;
		}
		$shortcode = implode('_', array_slice($parts, 1, -2));
		if (empty(self::$shortcodes[$shortcode])) {die;
			return;
		}

		delete_option($id);

		// Remove from the list of custom template IDs for this shortcode...
		$tpl_list_DB_key = ('pls_' . $shortcode . '_list');
		$tpl_list = get_option($tpl_list_DB_key, array()); // If it doesn't exist, create a blank array to append...
		unset($tpl_list[$id]);
		update_option($tpl_list_DB_key, $tpl_list);
	}

	/**
	 * Return the list of available templates for the given shortcode.
	 * List includes default templates and user created ones
	 * @param string $shortcode
	 * @param bool $all			: true to include hidden templates like the preview one
	 * @return array
	 */
	public static function template_list($shortcode, $all = false) {
		// sanity check
		if (empty(self::$shortcodes[$shortcode])) {
			return array();
		}

		$tpl_type_map = array();

		$sc_args = self::get_shortcodes($shortcode);

		// add default templates
		$default_tpls = !empty($sc_args['default_tpls']) ? $sc_args['default_tpls'] : array();
		foreach ($default_tpls as $name) {
			$tpl_type_map[$name] = array('type'=>'default', 'title'=>$name, 'id'=>$name);
		}

		// get custom templates
		$snippet_list_DB_key = ('pls_' . $shortcode . '_list');
		$tpl_list = get_option($snippet_list_DB_key, array());
		foreach ($tpl_list as $id => $name) {
			if ($id == 'pls_' . $shortcode . '___preview' && !$all) continue;
			$tpl_type_map[$id] = array('type'=>'custom', 'title'=>$name, 'id'=>$id);
		}
		return $tpl_type_map;
	}

	/**
	 * Comparator to sort template list in alphabetical order
	 */
	public static function _tpl_list_sort($a, $b) {
		return strcasecmp($a, $b);
	}

	/**
	 * Rebuild template list for the given shortcode
	 * @param string $shortcode	:
	 * @return array			: updated template list
	 */
	private static function _build_tpl_list($shortcode) {
		global $wpdb;
		// sanity check
		if (empty(self::$shortcodes[$shortcode])) {
			return array();
		}
		$tpls = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name LIKE %s", 'pls\_'.$shortcode.'\_\__%'));
		$tpl_list = array();
		foreach($tpls as $tpl){
			$tpl_data = get_option($tpl->option_name, array());
			if(empty($tpl_data['title'])) {
				$tpl_data['title'] = '';
			}
			$tpl_list[$tpl->option_name] = $tpl_data['title'];
		}

		uasort($tpl_list, array(__CLASS__, '_tpl_list_sort'));
		$tpl_list_DB_key = ('pls_' . $shortcode . '_list');
		update_option($tpl_list_DB_key, $tpl_list);
		return $tpl_list;
	}
}

new PL_Shortcode_CPT();
