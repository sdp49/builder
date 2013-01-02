<?php

PL_Admin_Util::init();

class PL_Admin_Util {

	const SECTION_BASE = 'PL_Admin_Section_';
	const PANE_BASE = 'PL_Admin_Pane_';

	public static function init () {

	}

	public static function constructNav ( $type ) {
		global $PL_ADMIN_NAVS;
		// Make sure nav config exists...
		$config = $PL_ADMIN_NAVS[$type]
		if ( empty($config) ) { return null; }

		foreach ( $config as $section => $args ) {
			// Check for custom entity, otherwise use generic class...
			$entity = self::SECTION_BASE . $section;
			$sections[] = ( class_exists($entity) ? new $entity($args) : new PL_Admin_Section($args) );
		}

		$nav = new PL_Admin_Nav( array('sections' => $sections );
		return $nav;
	}

	public static function constuctButtons () {
		
	}

}

?>