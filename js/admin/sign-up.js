jQuery(document).ready(function($) {

	var new_buttons = {
		1 : {
				text: "Close Setup Wizard",
				class: "gray-btn",
				click: function() {
					 $(this).dialog( "close" );
			}
		},
		2 : {
				text: "Use Existing Placester Account",
				class: "gray-btn",
				classes: "left",
				click: function() {
					 construct_modal(existing_acct_args);
			}
		},
		3 : {
				text: "Confirm Email",
				id: 'confirm_email_button',
				class: "green-btn right-btn",
				click: function() {
					// new_sign_up(modal_state.integration_launch);
					// Open IDX dialog...
			}
		}
	}

	var existing_buttons = {
		1 : {
				text: "Close Setup Wizard",
				class: "gray-btn",
				click: function() {
					 $(this).dialog("close");
			}
		},
		2 : {
				text: "Use a new Email address",
				class: "gray-btn",
				classes: "left",
				click: function() {
					 construct_modal('new_api_key_view', '');
			}
		},
		3 : {
			text: "Confirm",
			class: "green-btn right-btn",
			click: function() {
				var api_key = $('#existing_placester_modal_api_key').val();
				check_api_key(api_key);
			}
		}
	}

	// Modal config args...
	var new_acct_args = { ajax: 'new_api_key_view', title: 'Activate Your Plugin', buttons: new_buttons };
	var existing_acct_args = { ajax: 'existing_api_key_view', title: 'Use an Existing Placester API Key', buttons: existing_buttons };
	var idx_args = { ajax: '', title: '' };

	function construct_modal(args) {
		$.post(ajaxurl, {action: args.ajax}, function (result) {
			if (result) {
				$('#signup_wizard').html(result);
				$("#signup_wizard").dialog({
					autoOpen: true,
					draggable: false,
					modal: true,
					title: '<h3>' + args.title + '</h3>',
					width: 700,
					buttons: args.buttons
				});
			};
		});
	}

	// Create the sign-up wizard dialog container on initial page load...
	$('body').append('<div id="signup_wizard"></div>');
	construct_modal(new_acct_args);

	// 
	$('.wrapper, #settings_get_started_signup').on('click', function() {
		$("#signup_wizard").dialog("open");
	});
	
});