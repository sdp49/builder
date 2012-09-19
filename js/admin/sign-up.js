$(document).ready(function($) {

	var main_buttons = {
		1 : {
			text: "Close Setup Wizard",
			click: function() {
				 $( this ).dialog( "close" );
			}
		},
		2 : {
			text: "Use Existing Placester API Key",
			classes: "left",
			click: function() {
				 existing_api_key();
			}
		},
		3 : {
			text: "Confirm",
			id: 'confirm_email_button',
			click: function() {
				// var success = new_sign_up();
				
			// FOR TESTING PURPOSES	(remove!!!)
				var success = false;
				$.post(ajaxurl, {action: 'set_placester_api_key', api_key: 'a15ffb49672aeacffdcb6c1f983e3663c1bea767'}, function(response, textStatus, xhr) {
					console.log("here...");
					console.log(response);
					if (response['result']) {
						console.log("here #2...");
						success = true;
						console.log(success);
					}
				},'json');
			// =========================
				console.log(success);
				if (success) {
					console.log("made it here?");
					$('#signup_wizard').dialog("close");
					prompt_integration();
				}				 
			}
		}
	}

	var existing_api_key_buttons = {
		1 : {
			text: "Close Setup Wizard",
			click: function() {
				 $( this ).dialog( "close" );
			}
		},
		2 : {
			text: "Use a new Email address",
			classes: "left",
			click: function() {
				 new_api_key();
			}
		},
		3 : {
			text: "Confirm",
			click: function() {
				var api_key = $('#existing_placester_modal_api_key').val();
				check_api_key(api_key);
			}
		}
	}

	function existing_api_key() {
		$ = jQuery; //we're in no conflict land. 
		$.post(ajaxurl, {action:"existing_api_key_view"}, function (result) {
			if (result) {
				$('#signup_wizard').html(result);
				$('#existing_placester_dialog').show();
				$('#signup_wizard').dialog('option', 'title', '	<h3>Use an Existing Placester API Key</h3>');
				$('#signup_wizard').dialog('option', 'buttons', existing_api_key_buttons);
			};
		});
	}

	function new_api_key() {
		$ = jQuery; //we're in no conflict land. 
		$.post(ajaxurl, {action:"new_api_key_view"}, function (result) {
			if (result) {
				$('#signup_wizard').html(result);
				$('#signup_wizard').show();
				$('#signup_wizard').dialog('option', 'title', '	<h3>Welcome to the RE Website Builder Set Up Wizard</h3>');
				$('#signup_wizard').dialog('option', 'buttons', main_buttons);
			};
		});
	}

	$( "#signup_wizard" ).dialog({
		autoOpen: true,
		draggable: false,
		modal: true,
		title: '<h3>Welcome to the RE Website Builder Set Up Wizard</h3>',
		width: 700,
		buttons: main_buttons
	});

	$('.wrapper, #settings_get_started_signup').live('click', function() {
		$( "#signup_wizard" ).dialog( "open" );
	});
	
});