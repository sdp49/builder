<?php

PL_Lead_Capture_Helper::init();
class PL_Lead_Capture_Helper {

	private static $forward_address_options_key = 'pls_lead_forward_addresses';

	public static function init () {
		//register ajax endpoints
		add_action('wp_ajax_set_forwarding_addresses', array(__CLASS__, 'update_lead_forwarding_addresses')); 
	}

	public static function update_lead_forwarding_addresses () {
		$email_addresses = explode(',', $_POST['email_addresses']);

		PL_Options::set(self::$forward_address_options_key, $email_addresses);
		echo true;
		die();
	}

	public static function get_lead_forwarding_addresses () {
		return get_option(self::$forward_address_options_key);
	}

}