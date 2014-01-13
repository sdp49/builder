<?php 

PL_Lead_Helper::init();

class PL_Lead_Helper {

	const PL_LEAD_ID_KEY = 'pl_lead_id';

	private static $lead_details_default = array(
		'id' => '',
		'email' => '(Not Provided)',
		'first_name' => '(Not Provided)',
		'last_name' => '(Not Provided)',
		'phone' => '(Not Provided)',
		'created' => '(Not Provided)',
		'updated' => '(Not Provided)',
		'saved_searches' => 0
	);

	public static function init () {
		// Basic AJAX endpoints
		add_action('wp_ajax_datatable_my_leads', array(__CLASS__, 'ajax_get_leads'));
		add_action('wp_ajax_datatable_favorites_ajax', array(__CLASS__, 'ajax_get_favorites_by_id'));
		add_action('wp_ajax_update_lead', array(__CLASS__, 'ajax_update_lead'));
		add_action('wp_ajax_delete_lead', array(__CLASS__, 'ajax_delete_lead'));

		add_action('wp_ajax_delete_lead_search', array(__CLASS__, 'ajax_delete_lead_search'));		
	}

	public static function ajax_delete_lead () {
		echo json_encode(array('result' => 1, 'data_received' => json_encode($_POST)));
		die();
	}

	public static function ajax_delete_lead_search () {
		echo json_encode(array('result' => 1, 'data_received' => json_encode($_POST)));
		die();
	}

	public static function ajax_update_lead () {
		echo json_encode(array('result' => 1, 'data_received' => json_encode($_POST)));
		die();
	}

	public static function add_lead ($args = array()) {
		// Try to push lead to CRM (if one is linked/active)...
		self::add_lead_to_CRM($args);	

		return PL_Lead::create($args);
	}	

	public static function add_lead_ajax () {
		$api_response = self::add_lead($_POST);
		echo json_encode($api_response);
		die();
	}

	public static function add_lead_to_CRM ($args = array()) {
		// Check to see if site is actively linked to a CRM...
		$activeCRMKey = 'pl_active_CRM';
		$crm_id = PL_Options::get($activeCRMKey);
		
		if (!empty($crm_id)) {
			// Load CRM libs...
			$path_to_CRM = trailingslashit(PL_LIB_DIR) . 'CRM/controller.php';
			include_once($path_to_CRM);

			// Call necessary lib to add the contact to the active/registered CRM...
			if (class_exists('PL_CRM_Controller')) {
				PL_CRM_Controller::callCRMLib('createContact', $args);
			}
		}
	}

	public static function get_lead_id ($wp_user_id = null) {
		// Default this to null (indicates failure to callers...)
		$lead_id = null;

		// Get currentauthenticated user's Wordpress ID if no invalid one is passed in...
		$user_id = empty($wp_user_id) ? get_current_user_id() : $wp_user_id;

		if (!empty($user_id)) {
			$lead_id = get_user_meta($user_id, self::PL_LEAD_ID_KEY);
		}

		return $lead_id;
	}

	// Fetch a site user's details by his/her unique lead ID (managed externally, stored in WP's usermeta table)
	public static function lead_details ($args = array(), $wp_user_id = null) {
		// $details = array();

		$details = array(
			'id' => '2',
			'email' => 'john@smith.com',
			'first_name' => 'Jane',
			'last_name' => 'Johnson',
			'phone' => '123 123 1234',
			'created' => 'Today',
			'updated' => 'Yesterday',
			'saved_searches' => 5,
			'favorited_listings' => 3
		);

		$lead_id = null;

		// See if the lead id was passed -- if not, try to fetch it based on a WP user id...
		$lead_id = empty($args['id']) ? self::get_lead_id($wp_user_id) : $args['id'];

		if (!empty($lead_id)) {	
			// Fetch details from the API...
			$details = PL_Lead::details($lead_id, $args);

			// Format response...
			$details['full_name'] = $details['first_name'] . ' ' . $details['last_name'];
			$details = wp_parse_args($details, self::$lead_details_default);
		}

		return $details;
	}

	public static function get_lead_details_by_id ($lead_id) {
		
	}

	public static function update_lead_details ($lead_details) {
		$pl_lead = self::lead_details();
		return PL_People::update(array_merge(array('id' => $pl_lead['id']), $lead_details));
	}

	public static function get_leads ($filters = array()) {
		// Get leads from model...
		// $api_response = PL_Lead::get($filters);
		$api_response = array(
			'total' => 2,
			'leads' => array(
				array(
					'id' => '1',
					'email' => 'john@smith.com',
					'first_name' => 'john',
					'last_name' => 'smith',
					'phone' => '123 123 1234',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'saved_searches' => 5
				),
				array(
					'id' => '2',
					'email' => 'john@smith.com',
					'first_name' => 'Jane',
					'last_name' => 'Johnson',
					'phone' => '123 123 1234',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'saved_searches' => 5
				)
			)
		);

		return $api_response;
	}

	public static function ajax_get_leads () {
		// Get all leads associated with this site...
		$api_response = self::get_leads();
		
		// build response for datatables.js
		$leads = array();
		foreach ($api_response['leads'] as $key => $lead) {
			// $images = $listing['images'];
			$leads[$key][] = $lead['created'];
			$lead['full_name'] = $lead['first_name'] . ' ' . $lead['last_name'];
			// $leads[$key][] = ((is_array($images) && isset($images[0])) ? '<img width=50 height=50 src="' . $images[0]['url'] . '" />' : 'empty');
			$leads[$key][] = '<a class="address" href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '">' .
			 					$lead['full_name'] . 
			 				'</a>
			 				<div class="row_actions">
			 				<a href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id='. $lead['id'] .'&edit=1" >
			 					Edit
			 				</a>
			 				<span>|</span>
			 				<a href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '">
			 					View
			 				</a>
			 				<span>|</span>
			 				<a class="red" id="pls_delete_listing" href="#" ref="'.$lead['id'].'">
			 					Delete
			 				</a>
			 				</div>';
			// $leads[$key][] = $listing["location"]["postal"];
			
			$leads[$key][] = $lead['email'];
			$leads[$key][] = $lead['phone'];
			$leads[$key][] = $lead['updated'];
			$leads[$key][] = $lead['saved_searches'];
		}

		// Required for datatables.js to function properly
		$response = array();
		$response['sEcho'] = $_POST['sEcho'];
		$response['aaData'] = $leads;
		$response['iTotalRecords'] = $api_response['total'];
		$response['iTotalDisplayRecords'] = $api_response['total'];
		
		echo json_encode($response);
		die();
	}

	public static function ajax_get_favorites_by_id () {
		$lead_id = $_POST['lead_id'];

		// Get leads from model
		// $api_response = PL_Lead::get($lead_id);
		$api_response = array(
			'total' => 40,
			'searches' => array(
				array(
					'id' => '1',
					'image' => '',
					'full_address' => '38 W Cedar Street',
					'beds' => '1',
					'baths' => '2',
					'price' => '500k',
					'sqft' => '3454',
					'mls_id' => '123123'
				),
				array(
					'id' => '2',
					'image' => '',
					'full_address' => '38 W Cedar Street',
					'beds' => '1',
					'baths' => '2',
					'price' => '500k',
					'sqft' => '3454',
					'mls_id' => '123123'
				),
			)
		);
		
		// build response for datatables.js
		$searches = array();
		foreach ($api_response['searches'] as $key => $search) {
			
			$searches[$key][] = '<img src="' . $search['image'] . '" />';
			$searches[$key][] = '<a class="address" href="' . ADMIN_MENU_URL . $search['id'] . '">' . 
									$search['full_address'] . 
								'</a>
								<div class="row_actions">
									<a href="' . ADMIN_MENU_URL . '?page=placester_my_searches&id=' . $search['id'] . '">
										View
									</a>
									<span>|</span>
									<a class="red" id="pls_delete_listing" href="#" ref="'.$search['id'].'">
										Delete
									</a>
								</div>';
			
			$searches[$key][] = $search['beds'];
			$searches[$key][] = $search['baths'];
			$searches[$key][] = $search['price'];
			$searches[$key][] = $search['sqft'];
			$searches[$key][] = $search['mls_id'];
		}

		// Required for datatables.js to function properly.
		$response = array();
		$response['sEcho'] = $_POST['sEcho'];
		$response['aaData'] = $searches;
		$response['iTotalRecords'] = $api_response['total'];
		$response['iTotalDisplayRecords'] = $api_response['total'];
		echo json_encode($response);
		die();
	}

	/*
	 * Saved Search Functionality...
	 */

	public static function get_saved_searches ($wp_user_id = null, $lead_id = null) {
		// Default return value is an empty array (i.e., no saved searches)
		$saved_searches = array();

		$saved_searches = array(
			'total' => 40,
			'searches' => array(
				array(
					'id' => '1',
					'name' => 'Boston Properties',
					'saved_fields' => '1 Beds, City Boston, $500k+',
					'link_to_search' => '/listings/something',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'notification_schedule' => 'Once per week'
				),
				array(
					'id' => '2',
					'name' => 'Cambridge Properties',
					'saved_fields' => '1 Beds, City Boston, $500k+',
					'link_to_search' => '/listings/something',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'notification_schedule' => 'Once per week'
				),
			)
		);

		// Setup details call args to only pull saved searches...
		// $args = array('lead_id' => $lead_id, meta_keys' => array('saved_search'));
		
		// Fetch saved searches
		// $result = self::lead_details($wp_user_id, $args);

		// Prep searches...
		if (!empty($result) && is_array($result)) {
			foreach ($result as $hash => &$search) {
				// Construct full search URL based on current site's URL...
				if (!empty($search['url'])) {
					$search['url'] = site_url($search['url']);
				}
			}
			unset($search); // break the reference with the last element...

			$saved_searches = $result;
		}

		return $saved_searches;
	}

	private static function strip_empty_filters ($search_filters) {
		$filters = array();
		
		if (!empty($search_filters) && is_array($search_filters)) {
			foreach ($search_filters as $key => $filter) {
				if (trim($filter) != '') {
					$filters[$key] = trim($filter);
				}
			}
		}
		
		return $filters;
	}

	public static function is_search_saved ($search_filters) {
		$is_saved = false;

		// Remove empty filters...
		$filters = self::strip_empty_filters($search_filters);
		// error_log(var_export($filters, true));
		
		if (!empty($filters) && is_array($filters)) {
			// Setup details call args to check whether or not search is saved...
			$args = array('meta_keys' => array('saved_search'), 'val_match' => array($filters));

			// Call API to check for existence of saved search...
			$is_saved = self::lead_details($args);
		}

		return $is_saved;
	}

	public static function add_saved_search ($search_filters, $search_name, $search_url_path) {
		// Default result...
		$success = false;
		$message = "";

		// Remove empty filters...
		$filters = self::strip_empty_filters($search_filters);

		if (!empty($filters) && is_array($filters) && !empty($user_id)) {			
			// Args for saving search...
			$saved_search = array(
				'filters' => $filters, 
				'name' => $search_name,
				'url' => $search_url_path,
				'notification' => false
			);
			
			// Setup details call args to check whether or not search is saved...
			$args = array('add_meta', 'meta_key' => 'saved_search', 'meta_value' => $saved_search);

			$response = self::update_lead($args);
			
			$success = empty($response) ? false : true;
			$message = ($success === false) ? "Could not save search -- please try again" : "";

			// error_log("Unique search hash: $search_hash");
			// error_log(var_export($saved_searches, true));
		}

		return array("success" => $success, "message" => $message);
	}

    public static function delete_saved_search ($search_id) {
		// Default result...
		$success = false;
		$message = "";

		if (!empty($search_id)) {
			// Setup details call args to check whether or not search is saved...
			$args = array('delete_meta', 'meta_key' => 'saved_search', 'meta_id' => $search_id);

			// TODO: Actually delete...
			$response = self::update_lead($args);
			
			$success = empty($response) ? false : true;
			$message = ($success === false) ? "Could not delete search -- please try again" : "";
		}
		else {
			$message = "No search ID was passed -- cannot delete...";
		}
			
		return array("success" => $success, "message" => $message);
	}

	public static function update_search_notification ($search_id, $schedule_id) {
		// Setup details call args to check whether or not search is saved...
		$args = array('update_notification', 'type' => 'listing', 'meta_id' => $search_id, 'schedule' => $schedule_id);

		// TODO: Update the corresponding saved search...
		$response = self::update_lead($search_id, $enable);
		
		$success = empty($response) ? false : true;
		$message = ($success === false) ? "Could not enable notification -- please try again" : "";

		return array("success" => $success, "message" => $message);
	}

}