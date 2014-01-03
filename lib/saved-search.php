<?php

// JS in js/public/saved-search.js

PL_Saved_Search::init();
class PL_Saved_Search {

	public static $save_extension = 'pl_ss_';

	public static function init () {
		// Basic AJAX endpoints
		add_action('wp_ajax_get_saved_search_filters', array(__CLASS__, 'ajax_get_saved_search_filters'));
		add_action('wp_ajax_nopriv_get_saved_search_filters', array(__CLASS__, 'ajax_get_saved_search_filters'));

		// AJAX endpoints for attaching saved searches to users (currently, ONLY exposed for authenticated users...)
		add_action('wp_ajax_is_search_saved', array(__CLASS__, 'ajax_is_search_saved'));
		add_action('wp_ajax_add_user_saved_search', array(__CLASS__,'ajax_add_user_saved_search'));
		add_action('wp_ajax_delete_user_saved_search', array(__CLASS__, 'ajax_delete_user_saved_search'));
		add_action('wp_ajax_toggle_search_notification', array(__CLASS__, 'ajax_toggle_search_notification'));
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
	 * Functionality to handle associating saved searches with site users...
	 */

	private static function user_saved_search_key () {
		global $blog_id;
		return self::$save_extension . 'list_' . $blog_id;
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

	/* 
	 * NOTE: This call assumes that "strip_empty_filters" has ALREADY been called on the filters being passed in...
	 */
	private static function generate_search_hash ($search_filters) {
		ksort($search_filters);
		return sha1(serialize($search_filters));
	}
	
	public static function get_user_saved_searches ($user_id = null) {
		// Default return value is an empty array (i.e., no saved searches)
		$saved_searches = array();

		// Fallback to current user if user_id is not set...
		if (empty($user_id)) {
			// If the current user isn't authenticated, no point in continuing...
			if (!is_user_logged_in()) {
				return $saved_searches;
			}

			$user_id = get_current_user_id();
		}
		
		// Fetch saved searches
		$result = get_user_meta($user_id, self::user_saved_search_key(), true);
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

	public static function ajax_is_search_saved () {
		$search_filters = $_POST['search_filters'];
		
		$is_saved = self::is_search_saved($search_filters);
		$response = array("saved" => $is_saved);

		echo json_encode($response);
    	die();
	}

	public static function is_search_saved ($search_filters) {
		$is_saved = false;

		// Remove empty filters...
		$filters = self::strip_empty_filters($search_filters);
		// error_log(var_export($filters, true));
		if (!empty($filters) && is_array($filters)) {
			$search_hash = self::generate_search_hash($filters);
			// error_log("Search hash: {$search_hash}");
			$saved_searches = self::get_user_saved_searches();
			// error_log(var_export($saved_searches, true));
			$is_saved = isset($saved_searches[$search_hash]) ? true : false;
		}

		return $is_saved;
	}

    public static function ajax_add_user_saved_search () {
    	$search_url_path = $_POST['search_url_path'];
    	$search_name = $_POST['search_name'];
    	$search_filters = $_POST['search_filters'];
		
		// error_log(var_export($_POST['search_filters'], true));
		// error_log(var_export($_POST['search_url_path'], true));

    	// Add meta to user for saved searches...
    	if (!empty($search_filters) && is_array($search_filters)) {
    		$response = self::add_user_saved_search($search_filters, $search_name, $search_url_path);
    	}
    	else {
    		$response = array("success" => false, "message" => "No search filters to save -- select some and try again");
    	}
    	
    	echo json_encode($response);
    	die();
    }

	public static function add_user_saved_search ($search_filters, $search_name, $search_url_path) {
		// Default result...
		$success = false;
		$message = "";

		// Remove empty filters...
		$filters = self::strip_empty_filters($search_filters);

		// Only works if request is coming from an authenticated user...
		$user_id = get_current_user_id();
		$saved_searches = self::get_user_saved_searches($user_id);

		// error_log(var_export($filters, true));
		// error_log(var_export($search_url_path, true));
		// error_log(var_export($user_id, true));

		if (!empty($filters) && is_array($filters) && !empty($user_id)) {			
			// Sort filter array by key so unique hash produced is consistent regardless of element order...
			$search_hash = self::generate_search_hash($filters);
			
			// Make sure an entry with the same unique search has does not already exist -- if it does, don't add...
			if (isset($saved_searches[$search_hash])) {
				$success = false;
				$message = "A search with the same filters has already been saved";
			}
			else {
				$saved_searches[$search_hash] = array(
					'filters' => $filters, 
					'name' => $search_name,
					'url' => $search_url_path,
					'notification' => false
				);
				
				$update_success = update_user_meta($user_id, self::user_saved_search_key(), $saved_searches);
				
				$success = empty($update_success) ? false : true;
				$message = ($success === false) ? "Could not save search -- please try again" : "";

				// error_log("Unique search hash: $search_hash");
				// error_log(var_export($saved_searches, true));
				// error_log("user_saved_search_key: " . self::user_saved_search_key());
			}
		}

		return array("success" => $success, "message" => $message);
	}

	public static function ajax_delete_user_saved_search () {
		// Default response...
		$response = array("success" => false, "message" => "");

		// Get authenticated user's Wordpress ID...
		$user_id = get_current_user_id();

		if (!empty($user_id)) {
			// Identify which search to delete...
			if (!empty($_POST['search_hash'])) {
				$search_hash = $_POST['search_hash'];
			}
			else if (!empty($_POST['search_filters'])) {
				$filters = self::strip_empty_filters($_POST['search_filters']);
				$search_hash = self::generate_search_hash($filters);
			}
			else {
				$response['message'] = "No search passed for deletion...";
			}

			$saved_searches = self::get_user_saved_searches();

			if (isset($saved_searches[$search_hash])) {
				unset($saved_searches[$search_hash]);
			
				// Save the altered searches array...
				$update_success = update_user_meta($user_id, self::user_saved_search_key(), $saved_searches);
				
				$response['success'] = empty($update_success) ? false : true;
				$response['message'] = ($response['success'] === false) ? "Could not delete search -- please try again" : "";
			}
			else {
				$response['message'] = "This search is not saved -- can't delete it...";
			}
		} 
		else {
			$response['message'] = "User is not logged in";
		}

		echo json_encode($response);
		die();
	}

	public static function ajax_toggle_search_notification () {
		$search_hash = $_POST['search_hash'];
		$toggle_flag = $_POST['toggle_flag'];

		$response = self::toggle_search_notification($search_hash, $toggle_flag);

		echo json_encode($response);
		die();
	}

	public static function toggle_search_notification ($search_hash, $toggle_flag) {
		// Translate flag...
		$enable = ($toggle_flag == 'false') ? false : true;
		
		// Get authenticated user's Wordpress ID...
		$user_id = get_current_user_id();

		// See if a saved search that matches the hash exists...
		$saved_searches = self::get_user_saved_searches($user_id);

		if (isset($saved_searches[$search_hash])) {
			$saved_searches[$search_hash]['notification'] = $enable;

			// Save the altered searches array...
			$update_success = update_user_meta($user_id, self::user_saved_search_key(), $saved_searches);
			
			$success = empty($update_success) ? false : true;
			$message = ($success === false) ? "Could not enable notification -- please try again" : "";
		}
		else {
			$success = false;
			$message = "This search is not saved -- can't delete it...";
		}

		return array("success" => $success, "message" => $message);
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