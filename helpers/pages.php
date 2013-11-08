<?php 

PL_Page_Helper::init();
class PL_Page_Helper {

	public static function init () {
	}

	/**
	 * Create a pretty link for property details page
	 */
	public static function get_url ($placester_id, $listing = array()) {
		$listing = wp_parse_args($listing, array('location' => array(
				'region' => 'region',
				'locality' => 'locality',
				'postal' => 'postal',
				'neighborhood' => 'neighborhood',
				'address' => 'address',
		)));
		// not using get_permalink because it's a virtual page
		$permalink_struct = get_option('permalink_structure');
		if (empty($permalink_struct)) {
			// non pretty format
			$link = '?pls_page=property&property='.$placester_id;
		}
		else {
			$link = "/property/{$listing['location']['region']}/{$listing['location']['locality']}/{$listing['location']['postal']}/{$listing['location']['neighborhood']}/{$listing['location']['address']}/$placester_id";
			$link = preg_replace('/[^a-z0-9\-\/]+/', '-', strtolower($link));
		}
		return home_url($link);
	}
}