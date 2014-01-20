<?
global $PL_API_LEAD;
$PL_API_LEAD = array(
	'get' => array(
		'request' => array(
			'url' => '',
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
	),
	'create' => array(
		'request' => array(
			'url' => '',
			'type' => 'POST'
		),
		'args' => array(
			'id' => '',
			'email' => array('type' => 'text','group' => '', 'label' => 'Email'),
			'first_name' => array('type' => 'text','group' => '', 'label' => 'First Name'),
			'last_name' => array('type' => 'text','group' => '', 'label' => 'Last Name'),
			'phone' => array('type' => 'text','group' => '', 'label' => 'Phone'),
			'created' => '',
			'last_updated' => '',
			'saved_searches' => '',
			'favorited_listings' => ''
		)
	),
	'details' => array(
		'request' => array(
			'url' => '',
			'type' => 'POST'
		),
		'args' => array(
		),
		'returns' => array(
		)
	),
	'update' => array(
		'request' => array(
			'url' => '',
			'type' => 'POST'
		),
		'args' => array(
		),
		'returns' => array(
		)
	),
	'delete' => array(
		'request' => array(
			'url' => '',
			'type' => 'POST'
		),
		'args' => array(
		),
		'returns' => array(
		)
	)
);

?>
