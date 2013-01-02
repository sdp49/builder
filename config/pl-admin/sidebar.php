<?php
	
global $PL_ADMIN_NAVS;
$PL_ADMIN_NAVS = array(
	// Page editing, etc.
	'utilities' => array(
		'home' => array(
			'title' => 'Home',
			'css_class' => 'home',
			'priority' => 10
		),
		'listings' => array(
			'title' => 'Listings',
			'css_class' => 'listings',
			'priority' => 20
		),
		'agents' => array(
			'title' => 'Agents',
			'css_class' => 'agents',
			'priority' => 30
		),
		'about' => array(
			'title' => 'About',
			'css_class' => 'about',
			'priority' => 40
		),
		'contact' => array(
			'title' => 'Contact',
			'css_class' => 'contact',
			'priority' => 50
		),
		'custom' => array(
			'title' => 'Custom Search',
			'css_class' => 'custom',
			'priority' => 60
		),
		'mls' => array(
			'title' => 'MLS Search',
			'css_class' => 'mls',
			'priority' => 70
		),
		'blog' => array(
			'title' => 'Blog',
			'css_class' => 'blog',
			'priority' => 80
		),
		'services' => array(
			'title' => 'Services',
			'css_class' => 'services',
			'priority' => 90
		),
		'testimonials' => array(
			'title' => 'Testimonials',
			'css_class' => 'testimonials',
			'priority' => 100
		),
		'neighborhoods' => array(
			'title' => 'Neighborhoods',
			'css_class' => 'neighborhoods',
			'priority' => 110
		)
	),

	// Conditionally loading for onboarding process
	'onboarding' => array(
	),

	// General settings that apply to most/all areas of the site
	'settings' => array(
		'theme' => array(
			'title' => 'Theme & Skin',
			'css_class' => 'theme',
			'priority' => 10
		),
		'css' => array(
			'title' => 'CSS Editor',
			'css_class' => 'css',
			'priority' => 20
		),
		'menus' => array(
			'title' => 'Menus',
			'css_class' => 'menus',
			'priority' => 30
		),
		'settings' => array(
			'title' => 'Settings',
			'css_class' => 'settings',
			'priority' => 40
		)
	),
);

?>