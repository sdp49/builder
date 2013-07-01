<?php

PL_CRM_Contactually::init();

class PL_CRM_Contactually extends PL_CRM_Base {
	
	const apiOptionKey = "pl_contactually_api_key";
	const apiURL = "https://www.contactually.com/api";
	const version = "v1";

	public static function init () {
		if (class_exists("PL_CRM_Controller")) {
			$crm_info = array(
				"id" => "contactually", 
				"class" => "PL_CRM_Contactually",
				"display_name" => "Contactually",
				"logo" => ""
			);

			PL_CRM_Controller::registerCRM($crm_info);
		}
	}

	protected function getAPIOptionKey () {
		return self::apiOptionKey;
	}

	public function constructURL ($endpoint) {
		return "{self::apiURL}/{self::version}/{$endpoint}.json";
	}

	public function callAPI ($endpoint, $method, $args) {
		
	}
}

?>