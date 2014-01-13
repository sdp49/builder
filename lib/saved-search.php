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
	 * AJAX Endpoints for saved searches associated with site users + wrappers...
	 */

	public static function ajax_is_search_saved () {
		$search_filters = $_POST['search_filters'];
		
		$is_saved = PL_Lead_Helper::is_search_saved($search_filters);
		$response = array("saved" => $is_saved);

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

	// This wrapper exists to prevent having to alter Blueprint significantly up-front...
	public static function get_user_saved_searches () {
		return PL_Lead_Helper::get_saved_searches();
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