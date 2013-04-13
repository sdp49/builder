jQuery(document).ready(function($) {

	var new_api_key_buttons = {
		1 : {
			text: "Close Setup Wizard",
			class: "gray-btn",
			click: function() {
				 $( this ).dialog( "close" );
			}
		},
		2 : {
			text: "Use Existing Placester Account",
			class: "gray-btn",
			classes: "left",
			click: function() {
				 existing_api_key();
			}
		},
		3 : {
			text: "Confirm",
			id: 'confirm_email_button',
			class: "green-btn right-btn",
			click: function() {
				new_sign_up(modal_state.integration_launch);
			}
		}
	}

	var existing_api_key_buttons = {
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
				 new_api_key();
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

	function existing_api_key() {
		$.post(ajaxurl, {action:"existing_api_key_view"}, function (result) {
			if (result) {
				$('#signup_wizard').html(result);
				$("#signup_wizard").dialog({
					autoOpen: true,
					draggable: false,
					modal: true,
					title: '<h3>Use an Existing Placester API Key</h3>',
					width: 700,
					buttons: existing_api_key_buttons
				});
			};
		});
	}

	function new_api_key() {
		$.post(ajaxurl, {action:"new_api_key_view"}, function (result) {
			if (result) {
				$('#signup_wizard').html(result);
				$("#signup_wizard").dialog({
					autoOpen: true,
					draggable: false,
					modal: true,
					title: '<h3>Activate Your Plugin</h3>',
					width: 700,
					buttons: new_api_key_buttons
				});
			};
		});
	}

	// Create the sign-up wizard dialog container on initial page load...
	$('body').append('<div id="signup_wizard"></div>');
	new_api_key();

	// 
	$('.wrapper, #settings_get_started_signup').on('click', function() {
		$("#signup_wizard").dialog("open");
	});
	
});