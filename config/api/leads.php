<?php 

global $PL_API_LEAD;
$PL_API_LEAD = array(
	'create' => array(
		'request' => array(
			'url' => '', //coming soon,
			'type' => 'POST' //coming soon
		),
		'args' => array(
			'id' => '',
			'email' => array('type' => 'text','group' => '', 'label' => 'Email'),
			'first_name' => array('type' => 'text','group' => '', 'label' => 'First Name'),
			'last_name' => array('type' => 'text','group' => '', 'label' => 'Last Name'),
			'phone' => array('type' => 'text','group' => '', 'label' => 'Phone'),
			'created' => '',
			'updated' => '',
			'saved_searches' => '',
			'favorited_listings' => ''
		),
		'returns' => array(
			'id' => false
		)
	),
	// 'details' => array(
	// 	'request' => array(
	// 		'url' => 'https://api.placester.com/v2/leads/',
	// 		'type' => 'GET',
	// 		'cache' => false
	// 	),
	// 	'args' => array(
	// 		'id' => ''
	// 	),
	// 	'returns' => array(
	// 		'id' => false,
	// 		'relation' => '',
	// 		'cust_relation' => '',
	// 		'cur_data' => array(),
	// 		'uncur_data' => array(),
	// 		'rel_leads' => array(),
	// 		'fav_listings' => array()
	// 	)
	// )
);