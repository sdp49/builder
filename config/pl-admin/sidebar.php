<?php
	
global $PL_ADMIN_NAVS;
$PL_ADMIN_NAVS = array(
	// Page editing, etc.
	'utilities' => array(
		'home' => array(
			'title' => 'Home',
			'css_class' => 'home',
			'priority' => 10,
			'iframe_target' => ''
		),
		'listings' => array(
			'title' => 'Listings',
			'css_class' => 'listings',
			'priority' => 20,
			'iframe_target' => '/listings'
		),
		'agents' => array(
			'title' => 'Agents',
			'css_class' => 'agents',
			'priority' => 30,
			'iframe_target' => '/agents'
		),
		'about' => array(
			'title' => 'About',
			'css_class' => 'about',
			'priority' => 40,
			'iframe_target' => '/about'
		),
		'contact' => array(
			'title' => 'Contact',
			'css_class' => 'contact',
			'priority' => 50,
			'iframe_target' => '/contact'
		),
		'custom' => array(
			'title' => 'Custom Search',
			'css_class' => 'custom',
			'priority' => 60,
			'iframe_target' => ''
		),
		'mls' => array(
			'title' => 'MLS Search',
			'css_class' => 'mls',
			'priority' => 70,
			'iframe_target' => ''
		),
		'blog' => array(
			'title' => 'Blog',
			'css_class' => 'blog',
			'priority' => 80,
			'iframe_target' => '/blog'
		),
		'services' => array(
			'title' => 'Services',
			'css_class' => 'services',
			'priority' => 90,
			'iframe_target' => '/services'
		),
		'testimonials' => array(
			'title' => 'Testimonials',
			'css_class' => 'testimonials',
			'priority' => 100,
			'iframe_target' => '/testimonials'
		),
		'neighborhoods' => array(
			'title' => 'Neighborhoods',
			'css_class' => 'neighborhoods',
			'priority' => 110,
			'iframe_target' => '/neighborhoods'
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