<?php 

// This global wrapper eventually needs to be removed -- need time to alter its consumers...
function placester_get_api_key() { return PL_Option_Helper::api_key(); }

class PL_Option_Helper {
	
	public static function api_key () {
	    $api_key = PL_Options::get('placester_api_key');
		if (strlen($api_key) <= 0) {
			$api_key = false;
		}
		
	    return $api_key;	    
	}

	public static function set_api_key ($new_api_key) {
		// Default values...
		$result = false;
		$message = "That's not a valid Placester API Key";

		if (empty($new_api_key) ) {
			$message = "The API key cannot be empty";
		}
		elseif (self::api_key() == $new_api_key) { 
			$message = "You're already using that Placester API Key";
		}
		else {
			$response = PL_Helper_User::whoami(array(), $new_api_key);
			if ($response && isset($response['user'])) {
				$option_result = PL_Options::set('placester_api_key', $new_api_key);
				if ($option_result) {
					// Set result to reflect a successful update/change...
					$result = true;
					$message = "You've successfully changed your Placester API Key -- this page will reload momentarily";

					// Nuke the cache...
					PL_Cache::invalidate();
				} 
				else {
					$message = "There was an error -- are you sure that's a valid Placester API key?";
				}
			}
		}

		return array('result' => $result, 'message' => $message);
	}

	public static function set_google_places_key ($new_places_key) {
		if (self::get_google_places_key() == $new_places_key) {
			$result = false;
			$message = "You're already using that Google Places API Key";
		} 
		else {
			$result = PL_Options::set('placester_places_api_key', $new_places_key);
			$message = ($result ? "You've successfully updated your Google Places API Key" : "There was an error -- please try again");
		}

		return array('result' => $result, 'message' => $message);
	}

	public static function get_google_places_key () {
		$places_api_key = PL_Options::get('placester_places_api_key', '');
		return $places_api_key;
	}

	public static function set_polygons ($add = false, $remove_id = false) {
		$polygons = PL_Options::get('pls_polygons', array());
		if ($add) {
			$polygons[] = $add;
		}
		if ($remove_id !== false) {
			if (isset($polygons[$remove_id])) {
				unset($polygons[$remove_id]);

			}
		}
		$result = PL_Options::set('pls_polygons', $polygons);
		return $result;
	}

	public static function get_polygons () {
		return PL_Options::get('pls_polygons', array());	
	}

	public static function set_global_filters ($args) {
		extract(wp_parse_args($args, array('filters' => array())));
		return PL_Options::set('pls_global_search_filters', $filters);		
	}

	public static function get_global_filters () {
		return PL_Options::get('pls_global_search_filters');		
	}

	public static function set_community_pages ($enable_pages = false) {
		$result = PL_Options::set('pls_enable_community_pages', $enable_pages);
		return $result;
	}

	public static function get_community_pages () {
		$result = get_option('pls_enable_community_pages', false);
		return $result;
	}

	public static function set_log_errors ($report_errors = true) {
		$result = PL_Options::set('pls_log_errors', $report_errors);
		return $result;
	}

	public static function get_log_errors () {
		$result = PL_Options::get('pls_log_errors', true);
		return $result;
	}

	public static function set_block_address ($block_address = false) {
		$result = PL_Options::set('pls_block_address', $block_address);
		return $result;
	}

	public static function get_block_address () {
		$result = PL_Options::get('pls_block_address', false);
		return $result;
	}

	public static function set_default_country ($default_country) {
		return PL_Options::set('pls_default_country', $default_country);
	}

	public static function get_default_country () {
		$result = PL_Options::get('pls_default_country');
		return $result;
	}

	public static function set_default_location ($lat, $lng) {
		return PL_Options::set('pls_default_location', array('lat' => $lat, 'lng' => $lng));
	}

	public static function get_default_location () {
		$result = PL_Options::get('pls_default_location');
		if ($result) { return $result; }

		$response = PL_Helper_User::whoami();
		if ($response) {

			// user info
			if (isset($response['user']) && isset($response['user']['location'])) {
				$loc = $response['user']['location'];
				$lat = $loc['latitude'];
				$lng = $loc['longitude'];
				if ($lat && $lng) {
					return array('lat' => $lat, 'lng' => $lng);
				}

				$address = $loc['address'] . " " . $loc['locality'] . ", " . $loc['region'];
				$geo = PLS_Format::get_lat_lng_of_address($address);
				if ($geo && $geo['lat'] && $geo['lng']) {
					return $geo;
				}
			}

			// company info
			if (isset($response['location'])) {
				$loc = $response['location'];
				$lat = $loc['latitude'];
				$lng = $loc['longitude'];
				if ($lat && $lng) {
					return array('lat' => $lat, 'lng' => $lng);
				}

				$address = $loc['address'] . " " . $loc['locality'] . ", " . $loc['region'];
				$geo = PLS_Format::get_lat_lng_of_address($address);
				if ($geo && $geo['lat'] && $geo['lng']) {
					return $geo;
				}
			}
		}

		// default
		return array('lat' => 42.3596681, 'lng' => -71.0599325);
	}

	public static function set_translations ($dictionary) {
		return PL_Options::set('pls_amenity_dictionary', $dictionary);
	}

	public static function get_translations () {
		$result = PL_Options::get('pls_amenity_dictionary');	
		return $result;
	}

	public static function set_demo_data_flag ($desired_state = false) {
		// If the desired state is true, demo data is turned on (opposite for false)
		PL_Options::set('pls_demo_data_flag', $desired_state);
	}

	public static function get_demo_data_flag () {
		$result = PL_Options::get('pls_demo_data_flag');
		return $result;
	}

//end of class
}