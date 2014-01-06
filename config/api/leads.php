<?
global $PL_API_LEAD;
$PL_API_LEAD = array(
	'create' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2/people',
			'type' => 'POST'
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
			)
		),
	'get' => array(
		'request' => array(
			'url' => 'https://accounts.placester.com/v1/leads',
			'type' => 'GET'
		),
		'args' => array(
			'email' => array('type' => 'text','group' => 'Basic', 'label' => 'Email'),
			'first_name' => array('type' => 'text','group' => 'Basic', 'label' => 'First Name'),
			'last_name' => array('type' => 'text','group' => 'Basic', 'label' => 'Last Name'),
			'created' => array('type' => 'date','group' => 'Basic', 'label' => 'Date Created'),
			'saved_searches' => array('type' => 'text','group' => 'Basic', 'label' => '# of Saved Searches'),
			'favorited_listings' => array('type' => 'text','group' => 'Basic', 'label' => '# of Favorites')
		),
		'returns' => array(
			'id' => false
		)
	)
);
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

// 	'details' => array(
// 		'request' => array(
// 			'url' => 'https://api.placester.com/v2/people/',
// 			'type' => 'GET',
// 			'cache' => false
// 		),
// 		'args' => array(
// 			'id' => ''
// 		),
// 		'returns' => array(
// 			'id' => false,
// 			'relation' => '',
// 			'cust_relation' => '',
// 			'rel_people' => array(),
// 			'fav_listings' => array(),
// 			'saved_searches' => array()
// 		)
// 	),
// 	'update' => array(
// 		'request' => array(
// 			'url' => 'https://accounts.placester.com/v1/leads',
// 			'type' => 'POST'
// 		),
// 		'args' => array(
// 		),
// 		'returns' => array(
// 		)
// 	),
// 	'delete' => array(
// 		'request' => array(
// 			'url' => 'https://accounts.placester.com/v1/leads',
// 			'type' => 'POST'
// 		),
// 		'args' => array(
// 		),
// 		'returns' => array(
// 		)
// 	),
// );

?>
