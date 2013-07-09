<?php

PL_CRM_Contactually::init();

class PL_CRM_Contactually extends PL_CRM_Base {
	
	private static $apiOptionKey = "pl_contactually_api_key";
	private static $apiURL = "https://www.contactually.com/api";
	private static $version = "v1";

	public static function init () {
		// Register this CRM implementation with the controller...
		if (class_exists("PL_CRM_Controller")) {
			$crm_info = array(
				"id" => "contactually",
				"class" => "PL_CRM_Contactually",
				"display_name" => "Contactually",
				"referral_url" => "https://www.contactually.com/invite/placester",
				"cred_lookup_url" => "https://www.contactually.com/settings/integrations",
				"logo_img" => "contactually-logo.png"
			);

			PL_CRM_Controller::registerCRM($crm_info);
		}
	}

	public function __construct () {
		// Nothing yet...
	}

	protected function getAPIOptionKey () {
		return self::$apiOptionKey;
	}

	protected function setCredentials (&$handle) {
		// Attach the API as the first query arg for authentication purposes...
		if (is_array($args["query_params"])) {
			$args["query_params"]["api_key"] = $this->getAPIKey();
		}
		else {
			$args["query_params"] = array("api_key" => $this->getAPIKey());
		}
	}

	protected function constructURL ($endpoint) {
		$url = self::$apiURL;
		$version = self::$version;

		return "{$url}/{$version}/{$endpoint}.json";
	}

	/*
	 * Contacts
	 */

	public function getContacts ($filters = array()) {
		$endpoint = "contacts";
		$method = "GET";

		// Arg processing?

		// Make API Call...
		$response = $this->callAPI($endpoint, $method, $args);
	}

	public function createContact ($args) {
		//
	}

}

?>