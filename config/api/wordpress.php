<?php 

global $PL_API_WORDPRESS;
$PL_API_WORDPRESS = array(
	'set' => array(
		'request' => array(
			'url' => API_V2_URL.'wordpress/filters/',
			'type' => 'POST'
		),
		'args' => array(
			'url' => ''
		),
		'returns' => array()
	),
	'delete' => array(
		'request' => array(
			'url' => API_V2_URL.'wordpress/filters/',
			'type' => 'delete'
		),
		'args' => array(
			'url' => ''
		),
		'returns' => array()
	)
);