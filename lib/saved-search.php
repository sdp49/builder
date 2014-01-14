<?php

// JS in js/public/saved-search.js

PL_Saved_Search::init();

class PL_Saved_Search {

	/*
	 * Search Permalink functionality...
	 */

	public static $save_extension = 'pl_ss_';

	public static function init () {
		// Basic AJAX endpoints
		add_action('wp_ajax_get_saved_search_filters', array(__CLASS__, 'ajax_get_saved_search_filters'));
		add_action('wp_ajax_nopriv_get_saved_search_filters', array(__CLASS__, 'ajax_get_saved_search_filters'));

		// AJAX endpoints for attaching saved searches to users (currently, ONLY exposed for authenticated users...)
		add_action('wp_ajax_is_search_saved', array(__CLASS__, 'ajax_is_search_saved'));
		add_action('wp_ajax_get_saved_searches', array(__CLASS__, 'ajax_get_saved_searches'));
		add_action('wp_ajax_add_saved_search', array(__CLASS__,'ajax_add_saved_search'));
		add_action('wp_ajax_delete_saved_search', array(__CLASS__, 'ajax_delete_saved_search'));
		add_action('wp_ajax_update_search_notification', array(__CLASS__, 'ajax_update_search_notification'));
	}

	public static function generate_key ($search_id) {
		$hash = sha1($search_id);
		$key = self::$save_extension . $hash;
		
		return $key;
	}

	public static function save_search ($search_id, $search_filters) {
		$key = self::generate_key($search_id);

		// error_log("Search ID: $search_id");
		// error_log("Option key: $key");
		// error_log(var_export($search_filters, true));

		// Ensure these option-entries are NOT autoloaded on every request...
		return PL_Options::set($key, $search_filters, false);
	}

	public static function get_saved_search_filters ($search_id) {
		$key = self::generate_key($search_id);
		$result = PL_Options::get($key, false);

		return $result;
	}

	public static function ajax_get_saved_search_filters () {
		$result = array();
		$search_id = $_POST['search_id'];

		// Retrieve search filters associated with the given saved search ID...
		$filters = self::get_saved_search_filters($search_id);

		if (is_array($filters)) {
			foreach ($filters as $key => $value) {
				if (is_array($value)) {
					// This is how multidimensional arrays are stored in the name attribute in JS
					foreach ($value as $k => $v) {
						$result["{$key}[{$k}]"] = $v;
					}
				}
				else {
					// Otherwise, just store it regularly
					$result[$key] = $value;
				}
			}
		}

		echo json_encode($result);
		die();
	}

	// Clear all saved searches stored in the DB...
	public static function clear () {
		$saved_searches = $wpdb->get_results('SELECT option_name FROM ' . $wpdb->prefix . 'options ' ."WHERE option_name LIKE 'pl_ss_%'");
	    foreach ($saved_searches as $option) {
	        PL_Options::delete($option->option_name);
	    }
	}

	/*
	 * Saved Search for site users functionality...
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
		// $result = PL_Lead_Helper::lead_details($wp_user_id, $args);

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
			$is_saved = PL_Lead_Helper::lead_details($args);
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

			$response = PL_Lead_Helper::update_lead($args);
			
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
			$response = PL_Lead_Helper::update_lead($args);
			
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
		$response = PL_Lead_Helper::update_lead($search_id, $enable);
		
		$success = empty($response) ? false : true;
		$message = ($success === false) ? "Could not enable notification -- please try again" : "";

		return array("success" => $success, "message" => $message);
	}

	/*
	 * AJAX Endpoints for saved searches associated with site users + wrappers...
	 */

	public static function ajax_is_search_saved () {
		$search_filters = $_POST['search_filters'];
		
		$is_saved = PL_Lead_Helper::is_search_saved($search_filters);
		$response = array("saved" => $is_saved);

		echo json_encode($response);
    	die();
	}

	public static function ajax_get_saved_searches () {
		$lead_id = $_POST['lead_id'];

		$saved_searches = self::get_saved_searches($lead_id);
		
		// build response for datatables.js
		$searches = array();
		foreach ($saved_searches['searches'] as $key => $search) {
			// $images = $listing['images'];
			$searches[$key][] = $search['created'];
			// $searches[$key][] = ((is_array($images) && isset($images[0])) ? '<img width=50 height=50 src="' . $images[0]['url'] . '" />' : 'empty');
			$searches[$key][] = '<a class="address" href="' . ADMIN_MENU_URL . $search['link_to_search'] . '">' . 
									$search['name'] . 
								'</a>
								<div class="row_actions">
									<a href="' . ADMIN_MENU_URL . '?page=placester_my_searches&id=' . $search['id'] . '">
										View
									</a>
									<span>|</span>
									<a class="red" id="pls_delete_search" href="#" ref="'.$search['id'].'">
										Delete
									</a>
								</div>';
		
			// <a href="' . ADMIN_MENU_URL . '?page=placester_my_searches&id=' . $search['id'] . '" >
			// 							Edit
			// 						</a>
			
			$searches[$key][] = $search['saved_fields'];
			$searches[$key][] = $search['updated'];
			$searches[$key][] = $search['notification_schedule'];
		}

		// Required for datatables.js to function properly.
		$response = array();
		$response['sEcho'] = $_POST['sEcho'];
		$response['aaData'] = $searches;
		$response['iTotalRecords'] = $saved_searches['total'];
		$response['iTotalDisplayRecords'] = $saved_searches['total'];
		
		echo json_encode($response);
		die();
	}

	public static function ajax_add_saved_search () {
    	$search_url_path = $_POST['search_url_path'];
    	$search_name = $_POST['search_name'];
    	$search_filters = $_POST['search_filters'];
		
		// error_log(var_export($_POST['search_filters'], true));
		// error_log(var_export($_POST['search_url_path'], true));

    	// Add meta to user for saved searches...
    	if (!empty($search_filters) && is_array($search_filters)) {
    		$response = PL_Lead_Helper::add_saved_search($search_filters, $search_name, $search_url_path);
    	}
    	else {
    		$response = array("success" => false, "message" => "No search filters to save -- select some and try again");
    	}
    	
    	echo json_encode($response);
    	die();
    }

    public static function ajax_delete_saved_search () {
    	// Get search id...
    	$search_id = empty($_POST['search_id']) ? null : $_POST['search_id'];

    	echo json_encode(PL_Lead_Helper::delete_saved_search($search_id));
    	die();
    }

	public static function ajax_update_search_notification () {
		$search_id = $_POST['search_id'];
		$schedule_id = $_POST['schedule_id'];

		$response = PL_Lead_Helper::update_search_notification($search_id, $schedule_id);

		echo json_encode($response);
		die();
	}

	/*
	 * UI + Views
	 */

	// Renders the saved search form overlay...
	public static function get_saved_search_registration_form () {
        ob_start();
        if (is_user_logged_in()) {
            include(trailingslashit(PL_FRONTEND_DIR) . 'saved-search-authenticated.php');
        }
        else {
			// include(trailingslashit(PL_FRONTEND_DIR) . 'saved-search-unauthenticated.php');
        }

        return ob_get_clean();
    }

    public static function get_saved_search_button () {
    	ob_start();
            include(trailingslashit(PL_FRONTEND_DIR) . 'saved-search-button.php');
        return ob_get_clean();	
    }

    public static function translate_key ($key) {
		static $translations = array(
			// Listing API V3 fields
			'min_sqft' => 'Min Sqft',
			'min_beds' => 'Min Beds',
			'min_baths' => 'Min Baths',
			'max_price' => 'Max Price',
			'min_price' => 'Min Price',
			'prop_type' => 'Property Type',
			// Listing API V2.1 fields
			'location[locality]' => 'City',
            'location[postal]' => 'Zip Code',
            'location[neighborhood]' => 'Neighborhood',
            'metadata[min_sqft]' => 'Min Sqft',
            'purchase_types[]' => 'Purchase Type',
            'price_off' => 'Min Price',
            'metadata[min_beds]' => 'Min Beds',
            'metadata[min_baths]' => 'Min Baths',
            'metadata[min_price]' => 'Min Price'
		);

		$val = ( isset($translations[$key]) ? $translations[$key] : $key );
		return $val;
	}
}