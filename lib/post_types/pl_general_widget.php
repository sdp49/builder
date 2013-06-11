<?php
/**
 * The custom shortcodes are stored as a custom post type of pl_general_widget. 
 * Each references a shortcode template/layout that controls how its drawn. The templates
 * come from a custom theme or are user defined using custom post type pl_widget_template 
 *
 */
include_once(PL_LIB_DIR . 'post_types/pl_post_base.php');

class PL_General_Widget_CPT extends PL_Post_Base {

	protected static $title='Shortcodes/Widgets';
	// holds the shortcodes we have installed
	protected static $shortcodes = array(); 
	// default widget type
	protected $default_post_type = 'pl_map';
	



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
	
	public function __construct() {
		parent::__construct();
		
		// get list of shortcodes that can be widgetized:
		$path = trailingslashit( PL_LIB_DIR ) . 'post_types/';
		$ignore = array(
				'pl_post_type_manager.php',
				'pl_post_types_template.php',
				'pl_post_base.php' );
		if ($handle = opendir($path)) {
			while (false !== ($file = readdir($handle))) {
				if (pathinfo($file, PATHINFO_EXTENSION) == 'php' && !(in_array($file, $ignore))) {
					$class = 'PL_'.ucfirst(substr(substr($file, 0, -4), 3)).'_CPT';
					if (!class_exists($class)) {
						include($path . $file);
						$obj = new $class();
					}
					if (class_exists($class)) {
						$info = $class::get_type();
						if ($info['shortcode'] && $info['post_type']) {
							self::$shortcodes[$info['post_type']] = $info;
						}
					}
				}
			}
			closedir($handle);
		}

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'wp_ajax_pl_widget_changed', array( $this, 'widget_changed' ) );
		add_action( 'wp_ajax_pl_widget_preview', array( $this, 'widget_preview' ) );
		
		
	//TODO: review:		
	
			//add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'admin_head', array( $this, 'admin_head_plugin_path' ) );
			add_filter( 'manage_edit-pl_general_widget_columns' , array( $this, 'widget_edit_columns' ) );
	 		add_filter( 'manage_pl_general_widget_posts_custom_column', array( $this, 'widget_custom_columns' ) );
			add_action( 'wp_ajax_ve', array( $this, 'autosave_refresh_iframe' ), 1 );
			add_action( 'wp_ajax_autosave_widget_template', array( $this, 'autosave_save_template' ) );
			add_action( 'wp_ajax_handle_widget_script', array( $this, 'handle_iframe_cross_domain' ) );
			add_action( 'wp_ajax_nopriv_handle_widget_script', array( $this, 'handle_iframe_cross_domain' ) );
			// add_filter( 'pl_form_section_after', array( $this, 'filter_form_section_after' ), 10, 3 );
			add_filter( 'post_row_actions', array( $this, 'remove_quick_edit_view'), 10, 1 );
			add_action( 'restrict_manage_posts', array( $this, 'listing_posts_add_filter_widget_type' ) );
			add_filter( 'parse_query', array( $this, 'widget_type_posts_filter' ) );
			add_filter( 'get_edit_post_link', array( $this, 'shortcode_edit_link' ), 10, 3);
		}
	
		

	/**
	 * Return an array of shortcode types that can be widgetized
	 * @return array	: array of shortcode type arrays
	 */
	public static function get_shortcodes() {
		return self::$shortcodes;
	}

	
	/***************************************************
	 * Admin pages
	 ***************************************************/
	
	
	/**
	 * Called via wp_ajax when user changes some shortcode settings
	 */
	public function widget_changed( ) {
		$response = array('shortcode'=>'');
		if ( isset($_POST['shortcode_type']) && !empty($_POST[$_POST['shortcode_type']])) {
			$args = array_merge($_POST, $_POST[$_POST['shortcode_type']]);
			$record = $this::_save($_POST['post_ID'], $args, false);
			$response['shortcode'] = $this::_generate_shortcode($_POST['shortcode_type'], $record);
		}
		header( "Content-Type: application/json" );
		echo json_encode($response);
		die;
	}
	
	/**
	 * Called via via wp_ajax to get a preview of a shortcode supplied by GET 
	 */
	public static function widget_preview() {
		include(PL_VIEWS_ADMIN_DIR . 'shortcodes/preview.php');
		die;
	}
	
	
	
	
	
	
	
	/**
	 * Handle cross-domain script insertion and pass back to the embedded script for the iwdget
	 */
	public function handle_iframe_cross_domain() {
		// don't process if widget ID is missing
 		if( ! isset( $_GET['id'] ) ) {
 			die();
 		}

 		// defaults
 		$args['width'] = '250';
 		$args['height'] = '250';

 		// get the post and the meta
 		$post_id = $_GET['id'];
		$meta = get_post_custom( $post_id );

		// default GET should have at least id, callback and action
		$ignore_array = array(
			'pl_static_listings_option',
			'pl_featured_listings_option',
		);

		foreach( $meta as $key => $value ) {
			// ignore several options that we don't need to pass
			if( ! in_array( $key, $ignore_array ) ) {
				// ignore underscored private meta keys from WP
				if( strpos( $key, '_', 0 ) !== 0 && is_array( $value ) && ! empty( $value[0] ) ) {
					$args[$key] = $value[0];
				}
			}
		}

		$args['width'] = ! empty( $_GET['width'] ) ? $_GET['width'] : $args['width'];
		$args['height'] = ! empty( $_GET['height'] ) ? $_GET['height'] : $args['height'];
		$args['widget_class'] = ! empty( $meta['widget_class'] ) && is_array( $meta['widget_class'] ) ? $meta['widget_class'][0] : '';

		unset( $args['action'] );
		unset( $args['callback'] );

		$args['post_id'] = $_GET['id'];

		if( isset( $args['widget_original_src'] ) ) {
			$args['widget_url'] =  $args['widget_original_src'] . '/?p=' . $_GET['id'];
			unset( $args['widget_original_src'] );
		} else {
			$args['widget_url'] =  home_url() . '/?p=' . $_GET['id'];
		}

		header("content-type: application/javascript");
		echo $_GET['callback'] . '(' . json_encode( $args ) . ');';
	}

	public function admin_head_plugin_path( ) {
	?>
		<script type="text/javascript">
			var placester_plugin_path = '<?php echo PL_PARENT_URL; ?>';
		</script>
	<?php
	}

	public function widget_edit_columns( $columns ) {
		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns['title'] = $columns['title'];
		$new_columns['type'] = "Widget";
		$new_columns['date'] = $columns['date'];

		return $new_columns;
	}

	public function widget_custom_columns( $column ) {
		global $post;
		$widget_type = get_post_meta( $post->ID, 'pl_post_type', true );

		switch ($column) {
			case "type":
				if ($widget_type) {
					echo self::$shortcodes[$widget_type]['title'];
				}
				break;
		}
	}

 	public function save_shortcode( ) {
		if ( isset($_POST['shortcode_type']) ) {
			$id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0;

			if ( ! $id )
				wp_die( -1 );
				
			$this->_save($id, $_POST);
		}
	}

	public static function autosave_save_template()	{
		if ($_POST['shortcode'] && $_POST['title'] && $_POST['snippets']) {

			// Format & sanitize snippet_body...
			$snippets = array();
			parse_str($_POST['snippets'], $snippets);
			foreach($snippets as $snippet=>&$snippet_body) {
				$snippet_body = preg_replace('/<\?.*?(\?>|$)/', '', strip_tags($snippet_body, self::$allowable_tags));
				$snippet_body = htmlentities($snippet_body, ENT_QUOTES);
			}

			$id = self::save_shortcode_template($_POST['shortcode'], $_POST['title'], $snippets);
			echo json_encode(array('unique_id' => $id));

			// Blow-out the cache so the changes to the snippet can take effect...
			PL_Cache::clear();
		} else {
			echo array();
		}

		die();
	}


	public function update_template_block_styles( ) {
		ob_start();
	?>
	<style type="text/css">
		.snippet_container {
			width: 400px;
			margin-top: 0px;
		}
		.shortcode_container {
			width: 100%;
		}
	</style>
	<?php
		echo ob_get_clean();
	}

	public static function get_context_template( $post_type ) {
		switch( $post_type ) {
			case 'pl_search_listings':		return 'search_listings';
			case 'pl_map':					return 'search_map';
			case 'pl_form':					return 'search_form';
			case 'pl_slideshow':			return 'listing_slideshow';
			case 'pl_static_listings':		return 'static_listings';

			// for all the others with the same name
			default:
				return $post_type;
		}
	}

	public function filter_form_section_after( $form, $index, $count ) {
		if( $index < $count ) {
			return $form . '<div class="section-after"></div>';
		}
		return $form;
	}

	/**
	 * Remove quick edit and view
	 */
	public function remove_quick_edit_view( $actions ) {
		global $post;

		if( $post->post_type === 'pl_general_widget' ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );
		}
		return $actions;
	}

	/**
	 * Display widget types filter
	 */
	public function listing_posts_add_filter_widget_type($arg = '') {
		$type = 'pl_general_widget';
		if ($type != $arg && (! isset( $_GET['post_type'] ) || $_GET['post_type'] != 'pl_general_widget')) {
			return;
		}

		$values = $this->get_shortcodes();
		?>
        <select name="pl_widget_type">
        <option value="">All widget types</option>
        <?php
            $current_v = isset($_GET['pl_widget_type'])? $_GET['pl_widget_type']:'';
            foreach ($values as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <?php
	}

	/**
	 * Filter by widget types
	 */
	public function widget_type_posts_filter( $query ) {
		global $pagenow;
		$type = 'pl_general_widget';

		if ( is_admin() && ! empty( $_GET['pl_widget_type'] ) ) {
			$query->query_vars['meta_key'] = 'pl_post_type';
			$query->query_vars['meta_value'] = $_GET['pl_widget_type'];
		}
	}

	public function shortcode_edit_link($url, $ID, $context) {
		global $pagenow;
		if (get_post_type($ID) == 'pl_general_widget') {
			if ($pagenow == 'admin.php') {
				return admin_url('admin.php?page=placester_shortcodes_shortcode_edit&post='.$ID);
			}
			elseif ($pagenow == 'post.php') {
				return admin_url('admin.php?page=placester_shortcodes');
			}
		}
		return $url;
	}


	/* Template storage functions */


	public static function save_shortcode_template($shortcode, $title, $data) {
		if (empty(self::$codes[$shortcode])) {
			return '';
		}
		$snippet_DB_key = ('pls_' . $shortcode . '__' . $title);
		$data = array_merge(array('before_widget'=>'','after_widget'=>'','snippet_body'=>'','widget_css'=>''), (array)$data);
		update_option($snippet_DB_key, $data);

		// Add to the list of custom snippet IDs for this shortcode...
		$snippet_list_DB_key = ('pls_' . $shortcode . '_list');
		$snip_arr = get_option($snippet_list_DB_key, array()); // If it doesn't exist, create a blank array to append...
		$snip_arr[] = $title;
		$snip_arr = array_unique($snip_arr);

		// Update (or add) list in (to) DB...
		update_option($snippet_list_DB_key, $snip_arr);

		return $snippet_DB_key;
	}

	public static function delete_shortcode_template($id) {
		$parts = explode('-', $id, 2);
		if (count($parts) != 2 || empty(self::$codes[$parts[0]])) {
			return '';
		}
		$snippet_DB_key = ('pls_' . $parts[0] . '__' . $parts[1]);
		delete_option($snippet_DB_key);

		// Add to the list of custom snippet IDs for this shortcode...
		$snippet_list_DB_key = ('pls_' . $parts[0] . '_list');
		$snip_arr = get_option($snippet_list_DB_key, array()); // If it doesn't exist, create a blank array to append...
		unset($snip_arr[$parts[1]]);

		// Update (or add) list in (to) DB...
		update_option($snippet_list_DB_key, $snip_arr);
	}

	public static function load_shortcode_template($shortcode, $title) {
		$snippet_DB_key = 'pls_' . $shortcode . '__' . $title;
		$vals = array('before_widget'=>'','after_widget'=>'','snippet_body'=>'Cannot find custom snippet...','widget_css'=>'');
		return get_option($snippet_DB_key, $vals);
	}

	public static function template_list($shortcode) {
		// Get list of custom snippet ids for this shortcode...
		$snippet_list_DB_key = ('pls_' . $shortcode . '_list');
		$snip_arr = get_option($snippet_list_DB_key, array());

		$default_snippets = !empty(PL_Shortcodes::$defaults[$shortcode]) ? PL_Shortcodes::$defaults[$shortcode] : array();

		$snippet_type_map = array();

		foreach ($default_snippets as $snippet) {
			$snippet_type_map[$snippet] = array('type'=>'default', 'name'=>$snippet);
		}

		// Add Custom snippets..
		foreach ($snip_arr as $snippet) {
			$snippet_type_map[$snippet] = array('type'=>'custom', 'name'=>$snippet);
		}

		return $snippet_type_map;
	}
	
}

new PL_General_Widget_CPT();
