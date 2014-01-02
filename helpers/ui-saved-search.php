<?php 

PL_UI_Saved_Search::init();
class PL_UI_Saved_Search {

	public static $save_extension = 'pl_ss_';

	public static function init () {
		// Basic AJAX endpoints
		add_action('wp_ajax_datatable_my_leads_ajax', array(__CLASS__, 'ajax_get_leads'));
		// add_action('wp_ajax_nopriv_get_saved_search_filters', array(__CLASS__, 'ajax_get_saved_search_filters'));

		
	}

	public static function ajax_get_leads () {

		$response = array();

		// Get leads from model -- no global filters applied...
		// $api_response = PL_Lead::get($args);
		$api_response = array(
			'total' => 2,
			'leads' => array(
				array(
					'id' => '1',
					'email' => 'john@smith.com',
					'first_name' => 'john',
					'last_name' => 'smith',
					'phone' => '123 123 1234',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'saved_searches' => 5
				),
				array(
					'id' => '2',
					'email' => 'john@smith.com',
					'first_name' => 'Jane',
					'last_name' => 'Johnson',
					'phone' => '123 123 1234',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'saved_searches' => 5
				)
			)
		);
		
		// build response for datatables.js
		$leads = array();
		foreach ($api_response['leads'] as $key => $lead) {
			// $images = $listing['images'];
			$leads[$key][] = $lead['created'];
			$lead['full_name'] = $lead['first_name'] . ' ' . $lead['last_name'];
			// $leads[$key][] = ((is_array($images) && isset($images[0])) ? '<img width=50 height=50 src="' . $images[0]['url'] . '" />' : 'empty');
			$leads[$key][] = '<a class="address" href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '">' . $lead['full_name'] . '</a><div class="row_actions"><a href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '" >Edit</a><span>|</span><a href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '">View</a><span>|</span><a class="red" id="pls_delete_listing" href="#" ref="'.$lead['id'].'">Delete</a></div>';
			// $leads[$key][] = $listing["location"]["postal"];
			
			$leads[$key][] = $lead['email'];
			$leads[$key][] = $lead['phone'];
			$leads[$key][] = $lead['updated'];
			$leads[$key][] = $lead['saved_searches'];
		}

		// Required for datatables.js to function properly.
		$response['sEcho'] = $_POST['sEcho'];
		$response['aaData'] = $leads;
		$response['iTotalRecords'] = $api_response['total'];
		$response['iTotalDisplayRecords'] = $api_response['total'];
		echo json_encode($response);
		die();
	}

	public static function ajax_get_leads_saved_searches () {

		$response = array();

		// Get leads from model -- no global filters applied...
		// $api_response = PL_Lead::get($args);
		$api_response = array(
			'total' => 1,
			'leads' => array(
				array(
					'id' => '1',
					'email' => 'john@smith.com',
					'first_name' => 'john',
					'last_name' => 'smith',
					'phone' => '123 123 1234',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'saved_searches' => 5
				),
				array(
					'id' => '2',
					'email' => 'john@smith.com',
					'first_name' => 'Jane',
					'last_name' => 'Johnson',
					'phone' => '123 123 1234',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'saved_searches' => 5
				)
			)
		);
		
		// build response for datatables.js
		$leads = array();
		foreach ($api_response['leads'] as $key => $lead) {
			// $images = $listing['images'];
			$leads[$key][] = $lead['created'];
			$lead['full_name'] = $lead['first_name'] . ' ' . $lead['last_name'];
			// $leads[$key][] = ((is_array($images) && isset($images[0])) ? '<img width=50 height=50 src="' . $images[0]['url'] . '" />' : 'empty');
			$leads[$key][] = '<a class="address" href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '">' . $lead['full_name'] . '</a><div class="row_actions"><a href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '" >Edit</a><span>|</span><a href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '">View</a><span>|</span><a class="red" id="pls_delete_listing" href="#" ref="'.$lead['id'].'">Delete</a></div>';
			// $leads[$key][] = $listing["location"]["postal"];
			
			$leads[$key][] = $lead['email'];
			$leads[$key][] = $lead['phone'];
			$leads[$key][] = $lead['updated'];
			$leads[$key][] = $lead['saved_searches'];
		}

		// Required for datatables.js to function properly.
		$response['sEcho'] = $_POST['sEcho'];
		$response['aaData'] = $leads;
		$response['iTotalRecords'] = $api_response['total'];
		$response['iTotalDisplayRecords'] = $api_response['total'];
		echo json_encode($response);
		die();
	}


	public static function get_lead_details_by_id ($lead_id) {
		return false;
	}


}