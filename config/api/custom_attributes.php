<?php 
global $PL_API_CUST_ATTR, $PL_API_URLS;

$PL_API_CUST_ATTR = array(
	'get' => array(
		'request' => array(
			'url' => 'http://'.$PL_API_URLS['API_V2_URL'].'custom/attributes',
			'type' => 'GET'
		),
		'args' => array(
			'cat' => array('type' => 'text'),
			'name' => array('type' => 'text'),
			'attr_type' => array(
				'type' => 'select',
				'options' => array(
					'0' => 'int',
					'1' => 'text',
					'2' => 'text',
					'3' => 'textarea',
					'4' => 'date',
					'5' => 'date',
					'6' => 'checkbox',
					'7' => 'text'
				)
			),
			'attr_class' => array(
				'type' => 'select',
				'options' => array(
					'0' => 'deal',
					'1' => 'people',
					'2' => 'listing',
					'3' => 'building'
				)
			),
		),
		'returns' => array(
			'id' => false,
			'cat' => false,
			'name' => false,
			'attr_type' => false,
			'attr_class' => false,
			'always_show' => false
		)
	)
);