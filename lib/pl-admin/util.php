<?php

PL_Admin_Util::init();

class PL_Admin_Util {

	const SECTION_BASE = 'PL_Admin_Section_';
	const PANE_BASE = 'PL_Admin_Pane_';

	public static function init () {
		add_action( 'template_redirect', array( __CLASS__, 'load_framework') );
	}

	public static function load_framework () {
	    // Try to retrieve this object to ensure that the request is NOT coming from the customizer...
	    global $wp_customize;

	    if ( current_user_can('manage_options') && empty($_GET['container']) && empty($wp_customize) ) {
	        // Load PL admin panel...
	        PL_Router::load_builder_view('main.php', trailingslashit(PL_VIEWS_DIR) . 'pl-admin/');

	        // Make sure WP doesn't load any other templates...
	        exit;
	    }    
	} 

	public static function constructNav ( $id ) {
		global $PL_ADMIN_NAVS;
		// Make sure nav config exists...
		$config = $PL_ADMIN_NAVS[$id];
		if ( empty($config) ) { return null; }

		// Constuct an empty Nav...
		$nav = new PL_Admin_Nav($id);

		foreach ( $config as $section => $args ) {
			// Check for custom entity, otherwise use generic class...
			$entity = self::SECTION_BASE . $section;
			$new_section = ( class_exists($entity) ? new $entity($args) : new PL_Admin_Section($args) );
			$nav->add_section($new_section);
		}

		return $nav;
	}

	public static function constuctButtons () {
		
	}

}

?>