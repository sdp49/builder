<?php

PL_Admin_Sidebar::init();

class PL_Admin_Sidebar {

	const CLASS_PREFIX = 'PL_Admin_';

	public static function init () {

	}

	public static function constructNav ( $type ) {
		global $PL_ADMIN_NAVS;
		// Make sure nav config exists...
		$config = $PL_ADMIN_NAVS[$type]
		if ( empty($config) ) { return null; }

		$nav_entities = array();
		foreach ( $config as $section => $args ) {
			$entity = self::CLASS_PREFIX . $section;
			if ( class_exists($entity) ) {
				$nav_entities[] = new $entity();
			}
		}
	}
}

?>