<?php

// Js in js/public/saved-search.js

//TODO
//Methods for generating the saved_search_form
//Methods for adding the saved search link to subshort codes in widgets section.

PL_Saved_Search::init();
class PL_Saved_Search {

	public static $save_extension = 'pl_ss_';
	public static $user_saved_keys = 'pl_saved_searches';

	public static function init () {
		// Basic AJAX endpoints
		add_action('wp_ajax_get_saved_search_filters', array(__CLASS__, 'ajax_get_saved_search_filters'));
		add_action('wp_ajax_nopriv_get_saved_search_filters', array(__CLASS__, 'ajax_get_filters'));

		// AJAX endpoints for attaching saved searches to users
		add_action('wp_ajax_add_saved_search_to_user', array(__CLASS__,'ajax_add_saved_search_to_user'));
		add_action('wp_ajax_delete_user_saved_search', array(__CLASS__, 'delete_user_saved_search'));
	}

	public static function generate_key ($search_id) {
		$hash = sha1($search_id);
		$key = self::$save_extension . $hash;
		
		return $key;
	}

	public static function save ($search_id, $value) {
		$key = self::generate_key($search_id);

		// Setting 'no' ensures these option-entries are NOT autoloaded on every request...
		return PL_Options::set($key, $value, false);
	}

	// 
	public static function get_saved_search_filters ($search_id) {
		$key = self::generate_key($search_id);
		$result = PL_Options::get($key, false);

		// If the saved search doesn't exist, create it...
		if (!$result) {
			// The $_POST array for this request will contain the pertinent search filters set + their values
			self::save($search_id, $_POST);
			$result = false;
		}

		return $result;
	}

	public static function ajax_get_saved_search_filters () {
		$result = array();
		$search_id = $_POST['search_id'];

		if ($saved_search = self::get_saved_search_filters($search_id)) {
			foreach ($saved_search as $key => $value) {
				if (is_array($value)) {
					// this is how multidimensional arrays are stored in the name attribute in JS
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
	
    public static function ajax_add_saved_search_to_user () {
    	$link_to_search = $_POST['link_to_search'];
    	$saved_search_name = $_POST['name_of_saved_search'];

    	$clean_search_form_data = self::purge_unneeded_form_data($_POST['search_form_key_values']);
		
    	// add meta to user for searches
    	if ( !empty($clean_search_form_data) ) {
    		$response = self::add_saved_search_to_user($clean_search_form_data, $saved_search_name, $link_to_search);
    		echo json_encode($response);
    	}
    	else {
    		echo array('message' => 'No Form data to save!');
    	}

    	die();
    }

	public static function add_saved_search_to_user ($clean_search_form_data, $saved_search_name, $link_to_search) {
		// Only works if request is coming from an authenticated user...
		$user_id = get_current_user_id();
		$saved_searches = self::get_user_saved_searches();

		if ( !empty($clean_search_form_data) && !empty($user_id) ) {			
			// 
			$search_value = json_encode($clean_search_form_data);

			$search_hash = self::generate_key($search_value);
				
			$saved_searches[$search_hash] = $search_value;
			$saved_searches[$search_hash] = array('search_value' => $search_value, 'search_name' => $saved_search_name, 'link_to_search' => $link_to_search);

			$update_success = self::assoc_saved_searches_to_user($user_id, $saved_searches);

			return $update_success;
		} 
		else {
			return false;
		}
	}

	public static function get_user_saved_searches ($user_id = null) {
		// Fallback to current user if user_id is not set...
		if (empty($user_id)) {
			if (!is_user_logged_in()) {
				return array();
			}

			$user_id = get_current_user_id();
		}
		
		// Fetch saved searches
		$saved_searches = get_user_meta($user_id, self::$user_saved_keys );
		if (empty( $saved_searches ) && ! is_array($saved_searches)) {
			$response = array();
		} 
		else {
			$response = $saved_searches[0];
		}
		// error_log(var_export($saved_searches, true));
		return $response;
	}

	public static function delete_user_saved_search () {
		// Get authenticated user's Wordpress ID...
		$user_id = get_current_user_id();

		if (!empty($user_id)) {
			$saved_search_hash_to_be_deleted = $_POST['saved_search_option_key'];

			$saved_searches = self::get_user_saved_searches();

			if ( isset($saved_searches[$saved_search_hash_to_be_deleted]) ) {
				unset( $saved_searches[$saved_search_hash_to_be_deleted] );
			}

			$response = self::assoc_saved_searches_to_user($user_id, $saved_searches);
		} 
		else {
			$response = json_encode(array('message' => 'User is not logged in'));
		}

		echo $response;
		die();
	}

	private static function assoc_saved_searches_to_user ($user_id, $saved_searches) {
		// 
		if (!empty($saved_searches) && is_array($saved_searches)) {
			return update_user_meta($user_id, self::$user_saved_keys, $saved_searches);
		} 
		else {
			return array('message' => "You didn't pass any saved searches");
		}
	}

	private static function purge_unneeded_form_data ($form_data) {
    	// Irrelevant data to the search form filters
    	$internal_params = array('action', 'submit');
    	foreach ($internal_params as $internal) {
    		if (isset( $form_data[$internal])) { unset($form_data[$internal]); }
    	}

    	return $form_data;
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
            include(trailingslashit(PL_FRONTEND_DIR) . 'saved-search-unauthenticated.php');
        }

        return ob_get_clean();
    }

    public static function get_saved_search_button () {
    	ob_start();
            include(trailingslashit(PL_FRONTEND_DIR) . 'saved-search-button.php');
        return ob_get_clean();	
    }

    public static function get_save_search_link () {
		return '<a href="#" class="pls_save_search">Save Search</a>';
	}
}