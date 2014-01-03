<?php

global $PL_API_LEAD;
$PL_API_LEAD = array(
	'get' => array(
		'request' => array(
			'url' => 'https://accounts.placester.com/v1/leads',
			'type' => 'GET'
		),
		'args' => array(
		),
		'returns' => array(
		)
	),
	'create' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2/people',
			'type' => 'POST'
		),
		'args' => array(
			'fav_listing_ids' => array()
		),
		'returns' => array(
			'id' => false
		)
	),
	'details' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2/people/',
			'type' => 'GET',
			'cache' => false
		),
		'args' => array(
			'id' => ''
		),
		'returns' => array(
			'id' => false,
			'relation' => '',
			'cust_relation' => '',
			'rel_people' => array(),
			'fav_listings' => array(),
			'saved_searches' => array()
		)
	),
	'update' => array(
		'request' => array(
			'url' => 'https://accounts.placester.com/v1/leads',
			'type' => 'POST'
		),
		'args' => array(
		),
		'returns' => array(
		)
	),
	'delete' => array(
		'request' => array(
			'url' => 'https://accounts.placester.com/v1/leads',
			'type' => 'POST'
		),
		'args' => array(
		),
		'returns' => array(
		)
	),
);

?>