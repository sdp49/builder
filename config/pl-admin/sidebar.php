<?php
	
global $PL_ADMIN_NAVS;
$PL_ADMIN_NAVS = array(
	// Page editing, etc.
	'utilities' => array(
	),

	// Conditionally loading for onboarding process
	'onboarding' => array(
	),

	// General settings that apply to most/all areas of the site
	'settings' => array(
		'theme' => array(
			'title' => 'Theme & Skin',
			'css-class' => 'theme'
		),
		'css' => array(
			'title' => 'CSS Editor',
			'css-class' => 'css'
		),
		'menus' => array(
			'title' => 'Menus',
			'css-class' => 'menus'
		),
		'settings' => array(
			'title' => 'Settings',
			'css-class' => 'settings'
		)
	),
);

?>