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

jQuery(document).ready(function($) {

	// Get main view...
	call_CRM_AJAX('mainView', {}, function (result) {
		console.log(result);
		$('#main-crm-container').html(result);
	});

});