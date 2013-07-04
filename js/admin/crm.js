// Stub for JS...

function call_CRM_AJAX (method, args, callback) {
	// Set response format variable...
	var format = (args && args.response_format) ? args.response_format : 'html';

	// Format data object to be passed...
	data = {};
	data.action = 'crm_ajax_controller';
	data.crm_method = method;
	data.crm_args = args;
	data.response_format = format;

	jQuery.post(ajaxurl, data, callback, format);
}

function fetch_api_key ($crm_id) {
	var api_key = null;

	// Alphanumeric regex (API keys should pass this...)
	var alnum_regex = /^[a-z0-9]+$/i;

	// Try to fetch API key based on naming convention...
	var input_id = '#' + id + '_api_key';
	var input_elem = $(api_key_input_id);
	
	// There should only be one API key input entered for this CRM...
	if (input_elem.length == 1) {
		var input = input_elem.val();

		// If input passes validating regex, set API key to input, otherwise set to null...
		api_key = ( alnum_regex.test(input) ? input : null );
	}

	return api_key;
}

jQuery(document).ready(function($) {

	// Get main view...
	call_CRM_AJAX('mainView', {}, function (result) {
		$('#main-crm-container').html(result);
	});

	$('.activate-button').on('click', function (event) {
		event.preventDefault();
		
		// Extract CRM id from clicked element's actual id...
		var id = $(this).attr('id')
		var CRMid = id.replace('activate_', '');

		call_CRM_AJAX('setActiveCRM', {crm_id: CRMid}, function (result) {
			console.log(result);
		});
	});

	$('.integrate-button').on('click', function (event) {
		event.preventDefault();
		
		// Extract CRM id from clicked element's actual id...
		var id = $(this).attr('id')
		var CRMid = id.replace('integrate_', '');
		var api_key = fetch_api_key(CRMid);

		// If API key wasn't entered or is invalid, prompt the user and exit...
		if (api_key == null) {
			// Prompt of invalid API key entry...
		}

		call_CRM_AJAX('integrateCRM', {crm_id: CRMid}, function (result) {
			console.log(result);

			// Alter view to include an activate button...
		});
	});

});