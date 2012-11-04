<?php

global $PL_CUSTOMIZER_THEMES;
$PL_CUSTOMIZER_THEMES = array(
							    'Agency' => array(
												'Columbus' => 'columbus',
												'Highland' => 'highland',
												'Manchester' => 'manchester',
												'Tampa' => 'tampa',
												'Ventura' => 'ventura'
												),
							    'Single Property' => array(
												'Bluestone' => 'bluestone',
												'Slate' => 'slate',
												)
							 );

global $PL_CUSTOMIZER_THEME_DETAILS;
$PL_CUSTOMIZER_THEME_STYLES = array(
										'bluestone'  => array(
													 'pls-site-title' => 'header h1 a',
													 'pls-site-subtitle' => '',
													 'pls-user-email' => '',
													 'pls-user-phone' => ''	
													 ),
										'slate' 	 => array(
													 'pls-site-title' => 'header h1 a',
													 'pls-site-subtitle' => '',
													 'pls-user-email' => '',
													 'pls-user-phone' => ''	
													 ),
										'columbus' 	 => array(
													 'pls-site-title' => 'header h1 a',
													 'pls-site-subtitle' => 'header h2',
													 'pls-user-email' => 'header p.h-email a, .widget-pls-agent .email',
													 'pls-user-phone' => 'header p.h-phone, .widget-pls-agent .phone'	
													 ),
										'highland'	 => array(
													 'pls-site-title' => 'header h1 a',
													 'pls-site-subtitle' => 'header h2',
													 'pls-user-email' => ', .widget-pls-agent .email',
													 'pls-user-phone' => ', .widget-pls-agent .phone'	
													 ),
										'manchester' => array(
													 'pls-site-title' => 'header h1 a',
													 'pls-site-subtitle' => '',
													 'pls-user-email' => ', .widget-pls-agent .email',
													 'pls-user-phone' => ', .widget-pls-agent .phone'	
													 ),
										'tampa' 	 => array(
													 'pls-site-title' => 'header h1 a',
													 'pls-site-subtitle' => '',
													 'pls-user-email' => ', .widget-pls-agent .email',
													 'pls-user-phone' => ', .widget-pls-agent .phone'	
													 ),
										'ventura' 	 => array(
													 'pls-site-title' => 'header h1 a',
													 'pls-site-subtitle' => '',
													 'pls-user-email' => ', .widget-pls-agent .email',
													 'pls-user-phone' => ', .widget-pls-agent .phone'	
													 )
							 		);


?>