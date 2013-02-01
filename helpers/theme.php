<?php

/*
 * Provides logic to wrap, interact with, and enhance WP theme functionality.
 */

PL_Theme_Helper::init();

class PL_Theme_Helper {

	public static function init () {
		add_action( 'wp_ajax_load_custom_styles', array(__CLASS__, 'load_custom_styles') );
		add_action( 'wp_ajax_load_theme_info', array(__CLASS__, 'load_theme_info') );
		add_action( 'wp_ajax_change_theme', array(__CLASS__, 'change_theme') );
		add_action( 'wp_ajax_get_theme_skins', array(__CLASS__, 'get_theme_skins_ajax') );
	}
	
	public static function load_custom_styles () {
		if ( isset($_POST['color']) )  {
		  	// This needs to be defined (ref'd by the template file we're about to load...)
		  	$color = $_POST['color'];

		  	$curr_theme = wp_get_theme()->Template;
		  	$skin_path = ( trailingslashit(PL_THEME_SKIN_DIR) . trailingslashit($curr_theme) . "{$color}.css" );

		  	// Read in CSS file contents as a sting...
		  	$styles = file_get_contents($skin_path);

			echo json_encode( array( 'styles' => $styles ) );
		}

		die();
	}

	public static function load_theme_info () {
		if ( isset($_POST['theme']) ) {
			$theme_name = $_POST['theme'];
			// switch_theme( $theme_name, $theme_name);

			$theme_obj = wp_get_theme( $theme_name );
			$screenshot = $theme_obj->get_screenshot();
			$description = $theme_obj->display('Description');
	       	    
			// echo json_encode(array('theme_info' => $new_html));
			echo json_encode(array('screenshot' => $screenshot, 'description' => $description));
		}

		die();
	}

	public static function change_theme () {
		if ( isset($_POST['new_theme']) ) {
			$new_theme = $_POST['new_theme'];

			// Assume stylesheet and template name are the same for now...
			switch_theme( $new_theme, $new_theme );

			echo json_encode(array('success' => 'true'));
		}
		
		die();
	}

	/*
	 * Get CSS skins (i.e., CSS file names) for any Placester theme.
	 */
	public static function get_theme_skins ( $template = null ) {
		// If no theme template is passed, use current theme...
		if ( empty($template) ) {
			$template = wp_get_theme()->Template;
		}
		
		$skins = array();

		// Construct file path to the theme's skins...	
	  	$skin_dir = ( trailingslashit(PL_THEME_SKIN_DIR) . trailingslashit($template) );
		
		// Generate list of available skins by filename...
		$dir = @opendir($skin_dir);
		if ( !empty($dir) ) {
			while ($filename = readdir($dir)) { 
				// Only look at files with a .css extension...
				if ( eregi("\.css", $filename) ) {
			    	$filename = substr( $filename, 0, -strlen('.css') ); // Omit file extension...
			    	$skins[ucfirst($filename)] = $filename;
			  	}
			}
		}

		return $skins;
	}

	public static function get_theme_skins_ajax () {
		if ( isset($_POST['template']) ) {
			$skins = self::get_theme_skins($_POST['template']);
			$skins = array_merge( array('---' => 'none', 'Default' => 'default'), $skins );

			echo json_encode(array('skins' => $skins));
		}

		die();
	}
}

?>