<?php

class PL_CRM_Controller {

	const activeCRMKey = "pl_active_CRM";
	const integratedCRMListKey = "pl_integrated_CRMs";

	public static supportedCRMList = array();

	public static function init () {
		// Load CRM libs...
		include_once("models/base.php");
		include_once("models/contactually.php");
		include_once("models/followupboss.php");

		// Load any necessary non-CRM libs...
		include_once("../../models/options.php");

		// Register AJAX endpoints...
		add_action("wp_ajax_integrate_crm", array(__CLASS__, "integrateCRM_ajax"));
		add_action("wp_ajax_get_active_crm", array(__CLASS__, "getActiveCRM_ajax"));
		add_action("wp_ajax_integrate_crm", array(__CLASS__, "setActiveCRM_ajax"));
	}

	public static function registerCRM ($crm_info) {
		extract($crm_info);

		// We need an id...
		if (!isset($id)) { return; }

		$info = array();
		$info['class'] = isset($class) ? $class : null;
		$info['display_name'] = isset($display_name) ? $display_name : null;

		self::supportedCRMList[$id] = $info;
	}

	public static function integrateCRM () {
		
	}

	public static function integrateCRM_ajax () {

	}

	public static function getActiveCRM () {
		return PL_Options::get(self::activeCRMkey, null);
	}

	public static function getActiveCRM_ajax () {
		
	}

	public static function setActiveCRM ($crm_id) {
		return PL_Options::set(self::activeCRMKey, $crm_id);
	}

	public static function setActiveCRM_ajax () {
		
	}


}

?>