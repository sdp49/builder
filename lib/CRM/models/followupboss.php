<?php

PL_CRM_Followupboss::init();

class PL_CRM_Followupboss extends PL_CRM_Base {
	
	private static $apiOptionKey = "pl_followupboss_api_key";
	private static $apiURL = "https://api.followupboss.com";
	private static $version = "v1";

	private static $contactFieldMeta = array();

	public static function init () {
		// Register this CRM implementation with the controller...
		if (class_exists("PL_CRM_Controller")) {
			$crm_info = array(
				"id" => "followupboss",
				"class" => "PL_CRM_Followupboss",
				"display_name" => "Follow Up Boss",
				"referral_url" => "app.followupboss.com/signup?p=placester",
				"cred_lookup_url" => "https://app.followupboss.com/settings/user",
				"logo_img" => "follow-up-boss-color.png"
			);

			PL_CRM_Controller::registerCRM($crm_info);
		}

		// Initialize contact field -- NOTE: Specific to this CRM's API!!!
		self::$contactFieldMeta = array(
			"id" => array(
				"label" => "ID",
				"data_format" => "integer",
				"searchable" => false,
				"group" => "Search",
				"type" => "text"
			),
			"firstName" => array(
				"label" => "First Name",
				"data_format" => "string",
				"searchable" => true,
				"group" => "Search",
				"type" => "text"
			),
			"lastName" => array(
				"label" => "Last Name",
				"data_format" => "string",
				"searchable" => true,
				"group" => "Search",
				"type" => "text"
			),
			"emails" => array(
				"label" => "E-mail(s)",
				"data_format" => "object",
				"searchable" => false,
				"group" => "Search",
				"type" => "text"
			),
			"phones" => array(
				"label" => "Phone(s)",
				"data_format" => "object",
				"searchable" => false,
				"group" => "Search",
				"type" => "text"
			),
			"stage" => array(
				"label" => "Stage",
				"data_format" => "string",
				"searchable" => true,
				"group" => "Search",
				"type" => "text"
			),
			"source" => array(
				"label" => "Source",
				"data_format" => "string",
				"searchable" => true,
				"group" => "Search",
				"type" => "text"
			),
			"lastActivity" => array(
				"label" => "Last Activity",
				"data_format" => "datetime",
				"searchable" => false,
				"group" => "Search",
				"type" => "text"
			),
			"contacted" => array(
				"label" => "Contacted",
				"data_format" => "boolean",
				"searchable" => true,
				"group" => "Search",
				"type" => "checkbox"
			)
		);
	}

	public function __construct () {
		// Nothing yet...
	}

	protected function getAPIOptionKey () {
		return self::$apiOptionKey;
	}

	protected function setCredentials (&$handle, &$args) {
		$api_key = $this->getAPIKey();

		// HTTP authentication using the API key as user name with no password...
		curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($handle, CURLOPT_USERPWD, $api_key . ":");
	}

	public function constructURL ($endpoint) {
		$url = self::$apiURL;
		$version = self::$version;

		return "{$url}/{$version}/{$endpoint}";
	}

	/*
	 * Contacts
	 */

	public function contactFieldMeta () {
		return self::$contactFieldMeta;
	}  

	public function contactFieldLabels () {
		$labels = array();

		foreach (self::$contactFieldMeta as $field => $meta) {
			$labels[] = $meta["label"]; 
		}

		return $labels;
	}

	public function generateContactSearchForm () {
		// Get all "searchable" contact fields...
		$search_fields = array();
		foreach ($this->contactFieldMeta() as $field => $meta) {
			if (isset($meta["searchable"]) && $meta["searchable"] === true) {
				$search_fields[$field] = $meta;
			}
		}

		$form_args = array(
			"method" => "POST", 
			"title" => true, 
			"include_submit" => false, 
			"echo_form" => false, 
			"id" => "contacts_grid_search"
		);

		return PL_Form::generate_form($search_fields, $form_args);
	}

	public function getContacts ($filters = array()) {
		// This is a GET request, so mark all filters as query string params...
		$args = array("query_params" => $filters);

		// Make API Call...
		$response = $this->callAPI("people", "GET", $args);

		// error_log(var_export($response, true));

		// Translate API specific response into standard contacts collection...
		$data = array();
		$data["total"] = empty($response["_metadata"]["total"]) ? 0 : $response["_metadata"]["total"];
		$data["contacts"] = (empty($response["people"]) || !is_array($response["people"])) ? array() : $response["people"];

		return $data;
	}

	public function getContact ($id) {
		// Make API Call...
		$response = $this->callAPI("people/{$id}", "GET");
		error_log(var_export($response, true));
	}

	public function createContact ($args) {
		// NOTE: Use events endpoint for this!!!
	}
}

?>