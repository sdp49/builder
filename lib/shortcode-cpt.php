<?php
/**
 * The customized shortcodes are stored as a custom post type of pl_general_widget. 
 * Each references a shortcode template/layout that controls how its drawn. 
 * The templates come from a file in the (Placester aware) theme or are user defined. 
 */

class PL_Shortcode_CPT {

	// holds the shortcodes we have installed
	protected static $shortcodes = array(); 
	// holds the shortcode classes we have installed
	protected static $shortcode_config = array(); 
	
	protected $preview_tpl;


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
		add_action( 'wp_ajax_pl_sc_template_preview', array( $this, 'template_preview') );
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
	 * Return an array of shortcodes with their respective arguments that can be used to
	 * construct admin pages for creating a custom instance of a shortcode 
	 * @return array	: array of shortcode type arrays
	 */
	public static function get_shortcodes() {
		if (empty(self::$shortcode_config)) {
			foreach(self::$shortcodes as $shortcode => $instance){
				self::$shortcode_config[$shortcode] = $instance->get_args();
			}
		}
		return self::$shortcode_config;
	}

	
	/***************************************************
	 * Admin pages
	 ***************************************************/
	
	
	/**
	 * Helper function to generate a shortcode string from a set of arguments
	 */
	public function generate_shortcode_str($shortcode, $args) {
		if (empty($shortcode) || empty(self::$shortcodes[$shortcode])) {
			return '';
		}
		return self::$shortcodes[$shortcode]->generate_shortcode_str($args);
	}

	/**
	 * We have to save settings as a template in order for ajax driven forms such as search listings
	 * to work. We always use the same name '_preview' for the tmplate name.
	 */
	public function template_preview() {
		
		$shortcode = (!empty($_GET['shortcode']) ? stripslashes($_GET['shortcode']) : '');
		$shortcode_args = $this->get_shortcodes();
		if (!$shortcode || empty($shortcode_args[$shortcode]) || empty($_GET[$shortcode])) {
			die;
		}
		// set the defaults
		$template_id = 'pls_'.$shortcode.'___preview';
		$args = wp_parse_args($_GET, array('context'=>$template_id, 'width'=>'250','height'=>'250'));
		$sc_str = $this->generate_shortcode_str($shortcode, $args);
		$args = wp_parse_args($_GET[$shortcode], array('shortcode'=>$shortcode, 'title'=>'_preview'));
		$this->save_shortcode_template($template_id, $args);
		
		include(PL_VIEWS_ADMIN_DIR . 'shortcodes/preview.php');
		die;
	}

	public function _get_template_body() {
		foreach($this->preview_tpl as $field => $values) {
			if (!empty($values['handle_as']) && $values['handle_as'] == 'body') {
				return do_shortcode($values['value']);
			}
		}
	}

	public function _get_template_header() {
		foreach($this->preview_tpl as $field => $values) {
			if (!empty($values['handle_as']) && !empty($values['value'])) {
				if ($values['handle_as'] == 'css') {
					echo '<style type="text/css">'.$values['value'].'</style>';
				}
				elseif ($values['handle_as'] == 'header') {
					echo do_shortcode($values['value']);
				}
			}
		}
	}

	public function _get_template_footer() {
		foreach($this->preview_tpl as $field => $values) {
			if (!empty($values['handle_as']) && $values['handle_as'] == 'footer') {
				echo do_shortcode($values['value']);
			}
		}
	}

	/***************************************************
	 * Shortcode Template storage functions
	 ***************************************************/
	
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
	public static function save_shortcode_template($id, $data) {
		$data = (array)$data;
		// sanity check
		$shortcode = empty($data['shortcode'])?'':$data['shortcode'];
		if (!$shortcode || empty(self::$shortcodes[$shortcode])) {
			return '';
		}
		// if we change the shortcode of an existing record create a new one
		if (empty($id) || strpos($id, 'pls_'.$shortcode.'__')!==0) {
			$id = ('pls_' . $shortcode . '__' . time() . rand(10,99));
		}
		$sc_args = self::get_shortcodes();
		$defaults = $sc_args[$shortcode]['template'] + array('shortcode'=>'', 'title'=>'');
		foreach($defaults as $key => &$val) {
			if (isset($data[$key])) {
				$val = stripslashes($data[$key]);
			}
		}
		update_option($id, $defaults);

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
	public static function delete_shortcode_template($id) {
		// sanity check
		$parts = explode('_', $id, 2);
		if (count($parts) < 4 || $parts[0]!=='pls' || empty(self::$shortcodes[$parts[1]]) || !empty($parts[2]) || empty($parts[3])) {
			return;
		}
		delete_option($id);
	
		// Remove from the list of custom template IDs for this shortcode...
		$tpl_list_DB_key = ('pls_' . $parts[0] . '_list');
		$tpl_list = get_option($tpl_list_DB_key, array()); // If it doesn't exist, create a blank array to append...
		unset($tpl_list[$id]);
		update_option($tpl_list_DB_key, $tpl_list);
	}

	/**
	 * Load a template
	 * @param string $id
	 * @return array 
	 */
	public static function load_shortcode_template($id) {
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
	 * Return the list of available templates for the given shortcode.
	 * List includes default templates and user created ones
	 * @param string $shortcode
	 * @return array
	 */
	public static function template_list($shortcode) {
		// sanity check
		if (empty(self::$shortcodes[$shortcode])) {
			return array();
		}
	
		$tpl_type_map = array();
		
		$sc_args = self::get_shortcodes();

		// add default templates
		$default_tpls = !empty($sc_args[$shortcode]['default_tpl']) ? $sc_args[$shortcode]['default_tpl'] : array();
		foreach ($default_tpls as $name) {
			$tpl_type_map[$name] = array('type'=>'default', 'name'=>$name);
		}
	
		// get custom templates
		$snippet_list_DB_key = ('pls_' . $shortcode . '_list');
		$tpl_list = get_option($snippet_list_DB_key, array());
		foreach ($tpl_list as $id => $name) {
			$tpl_type_map[$id] = array('type'=>'custom', 'name'=>$name);
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
			if(!isset($tpl_data['title'])) {
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
