<?php

PL_Demo_Data::init();
class PL_Demo_Data {

	public function init() {
		add_action('wp_ajax_demo_data_on', array(__CLASS__, 'toggle_on' ) );
		add_action('wp_ajax_demo_data_off', array(__CLASS__, 'toggle_off' ) );
	}

	public function toggle_on() {
		PL_Option_Helper::set_demo_data_flag(true);
		
		return json_encode(array());
		die();
	}

	public function toggle_off() {
		PL_Option_Helper::set_demo_data_flag(false);

		return json_encode(array());
		die();
	}
}

?>