jQuery(document).ready(function($) {

	var new_buttons = {
        1 : {
            text: "Don't activate yet.",
            class: "linkify-button",
            click: function() {
                $(this).dialog("close");
            }
        },  
        2 : {
            text: "I already have an account.",
            class: "linkify-button",
            click: function() {
             	construct_modal(existing_acct_args);
            }
        },
        3 : {
            text: "Confirm Email",
            class: "green-btn right-btn",
            click: function() {
                new_sign_up(function () { 
                	construct_modal(idx_args); 
                	$(this).dialog("close");
                });
            }
        }
    };

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
				 construct_modal(new_acct_args);
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
	};

	var idx_buttons = {
        1 : {
            text: "No thanks.",
            class: "linkify-button no-thanks-idx-btn",
            click: function() {
				// Remove current dialog area, add "Add Listings Manually" dialog area.
				$('#idx-add-inner').addClass('hide');
				$('#idx-none-inner').removeClass('hide');
				// Hide buttons, show new buttons
				$('.yes-idx-btn, .no-thanks-idx-btn').addClass('hide');
				$('.add-listings-manually-btn').removeClass('hide');

				// Change title
				$('.ui-dialog-title h2').html("Add Listings to your Website Manually");
            }
        },
        2 : {
            text: "Yes",
            class: "green-btn yes-idx-btn right-btn",
            click: function() {
				// Remove current dialog area, add phone # dialog area.
				$('#idx-add-inner').addClass('hide');
				$('#idx-contact-inner').removeClass('hide');
				
				// Hide buttons, show new buttons
				$('.yes-idx-btn, .no-thanks-idx-btn').addClass('hide');
				$('.i-prefer-email-btn, .call-me-btn').removeClass('hide');
				
				// Start free trial...
				$.post(ajaxurl, {action: "start_subscription_trial"}, function (result) {}, "json");
            }            
        },  
        3 : {
            text: "All set!",
            class: "linkify-button hide add-listings-manually-btn right-btn",
            click: function() {
              // Direct to Add Listings page
              $(this).dialog("close");
            }
        }, 
        4 : {
            text: "I prefer email.",
            class: "linkify-button hide i-prefer-email-btn",
            click: function() {
				// remove current dialog
				$('#idx-contact-inner').addClass('hide');
				// Show email dialog
				$('#idx-success-inner span#action').text("email");
				$('#idx-success-inner').removeClass('hide');

				// Hide buttons, show new buttons
				$('.i-prefer-email-btn, .call-me-btn').addClass('hide');
				$('.request-done-btn').removeClass('hide');
            }
        },
        5 : {
            text: "Please Call Me",
            class: "green-btn hide call-me-btn right-btn",
            click: function() {
				// Check if number entered is valid...
				var valid = validate_phone_number($("#callme-idx-phone").val());

				if (valid) {
					// Valid Phone Number
					$('#idx-contact-inner').prepend("YEP!");

					// remove current dialog
					$('#idx-contact-inner').addClass('hide');
					
					// Show email dialog
					$('#idx-success-inner span#action').text("call");
					$('#idx-success-inner').removeClass('hide');

					// Hide buttons, show new buttons
					$('.i-prefer-email-btn, .call-me-btn').addClass('hide');
					$('.request-done-btn').removeClass('hide');
					$("#phone-validation-message").html('');
				} 
				else {
					// Invalid Phone Number
					$("#callme-idx-phone").addClass('red');
					$("#phone-validation-message").html("Phone number is not valid");
				}
            }            
        },
        6 : {
            text: "All set!",
            class: "linkify-button hide request-done-btn right-btn",
            click: function() {
				// Point to phone number modal
				$(this).dialog("close");
            }            
		}
    };

	// Modal config args...
	var new_acct_args = { ajax: 'new_api_key_view', title: 'Activate Your Plugin', buttons: new_buttons, width: 500 };
	var existing_acct_args = { ajax: 'existing_api_key_view', title: 'Use an Existing Placester API Key', buttons: existing_buttons, width: 700 };
	var idx_args = { ajax: 'idx_prompt_view', title: 'Add IDX / MLS To My Website', buttons: idx_buttons, width: 500 };

	function construct_modal(args) {
		$.post(ajaxurl, {action: args.ajax}, function (result) {
			if (result) {
				$('#signup_wizard').html(result);
				$("#signup_wizard").dialog({
					autoOpen: true,
					draggable: false,
					modal: true,
					title: '<h3>' + args.title + '</h3>',
					width: args.width,
					buttons: args.buttons
				});
			};
		});
	}

	// Execute "Add IDX To My Website" modal
    // $("#add-idx-dialog").dialog({
    //     close: function(event, ui) {
    //       // Hide dialogs again
    //       $('#add-listings-manually-inner, #call-me-for-idx-inner, #email-me-for-idx-inner, #phone-me-for-idx-inner').addClass('hide');
    //       // Hide buttons again
    //       $('.request-done-btn, .call-me-btn, .i-prefer-email-btn').addClass('hide');
    //       // Show only the initial dialog
    //       $('#add-idx-inner').removeClass('hide');
	// 		 // Reload page to reflect the addition of an API key...
    //       setTimeout(function () { window.location.href = window.location.href; }, 2000);
    //     }
    // });

	// Create the sign-up wizard dialog container on initial page load...
	$('body').append('<div id="signup_wizard"></div>');
	construct_modal(new_acct_args);

	// Prevent any clicks...
	$('.wrapper').on('click', function() {
		$("#signup_wizard").dialog("open");
	});
	
});