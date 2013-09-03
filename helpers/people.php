<?php 

PL_People_Helper::init();

class PL_People_Helper {

	public static function init () {
		add_action('wp_ajax_add_person', array(__CLASS__, 'add_person_ajax' ) );
		add_action('wp_ajax_get_favorites', array(__CLASS__, 'get_favorites_ajax' ) );
	}

	public static function get_user () {
		$wp_user = wp_get_current_user();

		return empty($wp_user->ID) ? false : $wp_user;
	}	

	public static function add_person ($args = array()) {
		return PL_People::create($args);
	}	

	public static function add_person_ajax () {
		$api_response = PL_People::create($_POST);
		echo json_encode($api_response);
		die();
	}

	public static function get_favorites_ajax () {
		$placester_person = self::person_details();
		$favs = array();

		if (isset($placester_person['fav_listings']) && is_array($placester_person['fav_listings'])) {
			$favs = $placester_person['fav_listings'];
		}

		echo json_encode($favs);	
		die();
	}

	public static function update_person_details ($person_details) {
		$placester_person = self::person_details();
		return PL_People::update(array_merge(array('id' => $placester_person['id']), $person_details));
	}

	public static function person_details () {
		$wp_user = self::get_user();
		$placester_id = get_user_meta($wp_user->ID, 'placester_api_id');
		if (is_array($placester_id)) { $placester_id = implode($placester_id, ''); }
		if (empty($placester_id)) {
			return array();
		}
		return PL_People::details(array('id' => $placester_id));
	}

	public static function associate_property ($property_id) {
		$placester_person = self::person_details();
		$new_favorites = array($property_id);
		if (isset($placester_person['fav_listings']) && is_array($placester_person['fav_listings'])) {
			foreach ($placester_person['fav_listings'] as $fav_listings) {
				$new_favorites[] = $fav_listings['id'];
			}
		}
		return PL_People::update(array('id' => $placester_person['id'], 'fav_listing_ids' => $new_favorites ) );
	}	

	public static function unassociate_property ($property_id) {
		$placester_person = self::person_details();
		$new_favorites = array();
		if (is_array($placester_person['fav_listings'])) {
			foreach ($placester_person['fav_listings'] as $fav_listings) {
				if ($fav_listings['id'] != $property_id) {
					$new_favorites[] = $fav_listings['id'];
				}
			}
		}
		return PL_People::update(array('id' => $placester_person['id'], 'fav_listing_ids' => $new_favorites ) );
	}

	/**
	 * Helper function for a user's unique Placester ID (managed by Rails, stored in WP's usermeta table)
	 * @return User's Placester ID
	 */
	public static function get_placester_user_id () {
		$wp_user = self::get_user();
		$placester_id = get_user_meta($wp_user->ID, 'placester_api_id');
		if (is_array($placester_id)) { $placester_id = implode($placester_id, ''); }
		
		return $placester_id;
	}
		
}