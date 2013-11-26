<?php

class PL_Page_Helper {

	public static function get_link_template () {
		$permalink_struct = get_option('permalink_structure');
		if (empty($permalink_struct)) {
			// non pretty format
			$link = '?pls_page=property&property=%id%';
		}
		else {
			$link = "/property/%region%/%locality%/%postal%/%neighborhood%/%address%/%id%/";
		}
		return home_url($link);
	}

	/**
	 * Create a pretty link for property details page
	 */
	public static function get_url ($placester_id, $listing = array()) {
		$default = array(
				'region' => 'region',
				'locality' => 'locality',
				'postal' => 'postal',
				'neighborhood' => 'neighborhood',
				'address' => 'address',
				'id' => ''
		);
		$listing = wp_parse_args($listing, array('location' => $default));
		$listing = $listing['location'];
		$listing['id'] = $placester_id;
		// not using get_permalink because it's a virtual page
		$url = self::get_link_template();

		$tmpl_replace = $tmpl_keys = array();
		foreach ($default as $key=>$val) {
			$tmpl_replace[] = empty($listing[$key]) ? '-' : preg_replace('/[^a-z0-9\-]+/', '-', strtolower($listing[$key]));
			$tmpl_keys[] = '%'.$key.'%';
		}
		$url = str_replace($tmpl_keys, $tmpl_replace, $url);

		return $url;
	}
}