// Stub for JS...

function call_CRM_AJAX (method, args, callback) {
	// Set response format variable...
	var format = (args && args.response_format) ? args.response_format : 'html';

	// Format data object to be passed...
	data = {};
	data.action = 'crm_ajax_controller';
	data.crm_method = method;
	
	// If a separate method is specified for what is returned, set property and remove from args...
	if (args && args.return_spec) { 
		data.return_spec = args.return_spec;
		delete args.return_spec;
	}

	data.crm_args = args;
	data.response_format = format;

	jQuery.post(ajaxurl, data, callback, format);
}

function fetch_api_key (crm_id) {
	var api_key = null;

	// Alphanumeric regex (API keys should pass this...)
	var alnum_regex = /^[a-z0-9]+$/i;

	// Try to fetch API key based on naming convention...
	var input_id = '#' + crm_id + '_api_key';
	var input_elem = jQuery(input_id);
	
	// There should only be one API key input entered for this CRM...
	if (input_elem.length == 1) {
		var input = input_elem.val();

		// If input passes validating regex, set API key to input, otherwise set to null...
		api_key = ( alnum_regex.test(input) ? input : null );
	}

	return api_key;
}

jQuery(document).ready(function($) {

	// Store ref to main container element for use in setting delegated events and altering the view...(see below)
	var view = $('#main-crm-container');

	// Get the main view initially...
	call_CRM_AJAX('mainView', {}, function (result) {
		view.html(result);
	});

	view.on('click', '.activate-button', function (event) {
		event.preventDefault();
		
		// Extract CRM id from clicked element's actual id...
		var id = $(this).attr('id')
		var CRMid = id.replace('activate_', '');

		// Specify call to return altered view that results from CRM activation...
		retSpec = {method: 'mainView'};		

		call_CRM_AJAX('setActiveCRM', {crm_id: CRMid, return_spec: retSpec}, function (result) {
			console.log(result);

			view.html(result);
		});
	});

	view.on('click', '.integrate-button', function (event) {
		event.preventDefault();
		
		// Extract CRM id from clicked element's actual id...
		var id = $(this).attr('id')
		var CRMid = id.replace('integrate_', '');
		var APIkey = fetch_api_key(CRMid);

		// If API key wasn't entered or is invalid, prompt the user and exit...
		if (APIkey == null) {
			// Prompt of invalid API key entry...
			console.log("Bad API key...");
			return;
		}

		// Specify call to return the "activate" CRM partial for display purposes...
		retSpec = {method: 'getPartial', args: {partial: 'activate', partial_args: {id: CRMid}}};

		call_CRM_AJAX('integrateCRM', {crm_id: CRMid, api_key: APIkey, return_spec: retSpec}, function (result) {
			console.log(result);

			// Replace the integration view with activate UI...
			var elem = $('#' + CRMid + '-box .action-box');
			elem.html(result);
		});
	});

});