<?php

PL_CRM_Controller::init();

class PL_CRM_Controller {

	private static $activeCRMKey = "pl_active_CRM";

	public static $registeredCRMList = array();

	public static function init () {
		// Load CRM libs...
		include_once("models/base.php");
		include_once("models/contactually.php");
		include_once("models/followupboss.php");

		// Load any necessary non-CRM plugin libs...
		$curr_dir = trailingslashit(dirname(__FILE__));
		include_once("{$curr_dir}../../models/options.php");

		// Register main AJAX endpoint for all CRM calls...
		add_action("wp_ajax_crm_ajax_controller", array(__CLASS__, "ajaxController"));
	}

	public static function ajaxController () {
		error_log("In ajaxController...");
		error_log(var_export($_POST, true));

		// CRM-related AJAX calls (i.e., to the single endpoint defined in init) MUST specify a
		// field called "crm_method" that corresponds to the class function it wants to execute,
		// along with the properly labeled fields as subsequent arguments...
		if (is_null($_POST["crm_method"])) { return; }

		$method = $_POST["crm_method"];
		$callback = array(__CLASS__, $method);

		// Set args array if it exists...
		$args = ( !empty($_POST["crm_args"]) && is_array($_POST["crm_args"]) ? $_POST["crm_args"] : array() );

		// Execute correct function...
		$response = call_user_func_array($callback, $args);

		// Handle formatting response if set to JSON...
		if (isset($_POST["response_format"]) && $_POST["response_format"] == "JSON") {
	 		$response = json_encode($response);
 		}

		// Write payload to response...
		echo $response;

		die();
	}

	public static function registerCRM ($crm_info) {
		// We need an id...
		if (empty($crm_info["id"])) { return; }

		$id = $crm_info["id"];
		unset($crm_info["id"]);

		self::$registeredCRMList[$id] = $crm_info;
	}

	public static function getActiveCRM () {
		return PL_Options::get(self::$activeCRMKey, null);
	}

	public static function setActiveCRM ($crm_id) {
		return PL_Options::set(self::$activeCRMKey, $crm_id);
	}

	/*
	 * Serve up view(s)...
	 */

	public static function mainView () {
		// Check if a CRM is active...
		$active_crm = self::getActiveCRM();

		ob_start();
			if (is_null($active_crm)) {
				include("views/login.php");
			}
			else {
				// TODO...
			}
		$html = ob_get_clean();

		return $html;
	}
}

?>