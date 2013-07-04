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

	public function constructURL ($endpoint) {
		return "{self::$apiURL}/{self::$version}/{$endpoint}.json";
	}

	public function callAPI ($endpoint, $method, $args = array()) {
		// init cURL handle...
		$handle = curl_init();
		$api_key = $this->getAPIKey();

		// Attach the API as the first query arg for authentication purposes...
		if (is_array($args["query_params"])) {
			$args["query_params"]["api_key"] = $this->getAPIKey();
		}
		else {
			$args["query_params"] = array("api_key" => $this->getAPIKey());
		}

		// Construct URL...
		$query_str = isset($args["query_params"]) ? $this->constructQueryString($args["query_params"]) : "";
		$url = $this->constructURL($endpoint) . $query_str;

		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

		// Use a local cert to make sure we have a valid one
		curl_setopt($handle, CURLOPT_CAINFO, trailingslashit(PL_PARENT_DIR) . "config/cacert.pem");

		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		
		// Set payload if it exists...
		if (!empty($args["body"])) {
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($args["body"]));
		}

		// make API call
		$response = curl_exec($handle);
		if ($response === false) {
		    exit("cURL error: " . curl_error($handle) . "\n");
		}
	}
}

?>