<?php

PL_Demo_Data::init();
class PL_Demo_Data {

	public function init() {
		add_action('wp_ajax_demo_data_on', array(__CLASS__, 'toggle_on' ) );
		add_action('wp_ajax_demo_data_off', array(__CLASS__, 'toggle_off' ) );
		add_action('wp_ajax_upload_demo_data', array(__CLASS__, 'upload_data') );
	}

	public function toggle_on() {
		PL_Options_Helper::set_demo_data_flag(true);
	}

	public function toggle_off() {
		PL_Options_Helper::set_demo_data_flag(false);
	}

	public function upload_data() {
		
	}
}

?>