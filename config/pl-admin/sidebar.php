<?php
	
global $PL_ADMIN_NAVS;
$PL_ADMIN_NAVS = array(
	// Page editing, etc.
	'utilities' => array(
		'home' => array(
			'title' => 'Home',
			'css_class' => 'home',
			'priority' => 10,
			'content_uri' => ''
		),
		'listings' => array(
			'title' => 'Listings',
			'css_class' => 'listings',
			'priority' => 20,
			'content_uri' => '/listings'
		),
		'agents' => array(
			'title' => 'Agents',
			'css_class' => 'agents',
			'priority' => 30,
			'content_uri' => '/agents'
		),
		'about' => array(
			'title' => 'About',
			'css_class' => 'about',
			'priority' => 40,
			'content_uri' => '/about'
		),
		'contact' => array(
			'title' => 'Contact',
			'css_class' => 'contact',
			'priority' => 50,
			'content_uri' => '/contact'
		),
		// 'custom' => array(
		// 	'title' => 'Custom Search',
		// 	'css_class' => 'custom',
		// 	'priority' => 60,
		// 	'content_uri' => ''
		// ),
		// 'mls' => array(
		// 	'title' => 'MLS Search',
		// 	'css_class' => 'mls',
		// 	'priority' => 70,
		// 	'content_uri' => ''
		// ),
		'blog' => array(
			'title' => 'Blog',
			'css_class' => 'blog',
			'priority' => 80,
			'content_uri' => '/blog'
		),
		'services' => array(
			'title' => 'Services',
			'css_class' => 'services',
			'priority' => 90,
			'content_uri' => '/services'
		),
		'testimonials' => array(
			'title' => 'Testimonials',
			'css_class' => 'testimonials',
			'priority' => 100,
			'content_uri' => '/testimonials'
		),
		'neighborhoods' => array(
			'title' => 'Neighborhoods',
			'css_class' => 'neighborhoods',
			'priority' => 110,
			'content_uri' => '/neighborhoods'
		)
	),

	// Conditionally loading for onboarding process
	'onboarding' => array(
		'title' => array(
			'title' => 'Site Title & Slogan',
			'css_class' => 'title',
			'priority' => 10
		),
		'color' => array(
			'title' => 'Color Palette & Styling',
			'css_class' => 'color',
			'priority' => 20
		),
		'mls' => array(
			'title' => 'MLS Integration',
			'css_class' => 'mls-int',
			'priority' => 30
		),
		'social' => array(
			'title' => 'Social Integration',
			'css_class' => 'social',
			'priority' => 40
		),
		'demo' => array(
			'title' => 'Demo Data',
			'css_class' => 'demo',
			'priority' => 50
		)
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

global $PL_ADMIN_CARDS;
$PL_ADMIN_CARDS = array(
	// Indexed by section id (see above...)
	'theme' => array(
		'theme_select' => array(
			'title' => 'Select Theme',
			'priority' => 10
		),
		'theme_skin' => array(
			'title' => 'Select Skin',
			'priority' => 20
		)
	),
);

?>