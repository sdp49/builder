jQuery(document).ready(function($) {

	// Beat Chrome's HTML5 tooltips for form validation
	$('form.pl_lead_register_form').on('mousedown', 'input[type="submit"]', function() {
		validate_register_form(this);
	});
	$('form#pl_login_form').on('mousedown', 'input[type="submit"]', function() {
		validate_login_form(this);
	});
	$('.pl_lead_register_form').bind('keypress', function(e) {
		var code = e.keyCode || e.which;
		if (code == 13) {
			validate_register_form(this);
		}
	});
	$('#pl_login_form').bind('keypress', function(e) {
		var code = e.keyCode || e.which;
		if (code == 13) {
			validate_login_form(this);
		}
	});

	// Actual form submission - validate and submit
	$('.pl_lead_register_form').bind('submit', function (event) {	 
		// Prevent default form submission logic
		event.preventDefault();
		if (validate_register_form(this)) {
			register_user(this);
		}
	});
	$('form#pl_login_form').bind('submit', function (event) {
		event.preventDefault();
		if (validate_login_form(this)) {
			login_user(this);
		}
	});

	// Bind link in registration form that allows user to switch to the login form..
	$('form.pl_lead_register_form').on('click', '#switch_to_login', function (event) {
		event.preventDefault();

		// Simulate login link click to switch to login form...
		$('.pl_login_link').trigger('click');
	});

	if (typeof $.fancybox == "function") {
		// If reg form available or logged in then show add to favorites 
		if ($('.pl_lead_register_form').length || $('.pl_add_remove_lead_favorites #pl_add_favorite').length) {
			$('div#pl_add_remove_lead_favorites,.pl_add_remove_lead_favorites').show();		
		}
		// Register Form Fancybox
		$('.pl_register_lead_link').fancybox({
			"hideOnContentClick": false,
			"scrolling": true,
			wrapCSS: 'pl_fancybox_register_lead_link',
			onCleanup: function () {
				reset_form();
			}
		});

		// Login Form Fancybox
		$('.pl_login_link').fancybox({
			"hideOnContentClick": false,
			"scrolling": true,
			wrapCSS: 'pl_fancybox_login_form',
			onCleanup: function () {
				reset_form();
			}
		});

		$(document).ajaxStop(function() {
			favorites_link_signup();
		});
	}

	favorites_link_signup();

	function favorites_link_signup () {
		if (typeof $.fancybox == 'function') {
			$('.pl_register_lead_favorites_link').fancybox({
				"hideOnContentClick": false,
				"scrolling": true,
				onCleanup: function () {
					reset_form();
				}
			}); 
		}
	}

	// Called with form data after validation 
	function register_user (form_el) {
		var $form = $(form_el).closest('form');

		data = {
				action: "pl_register_site_user",
				username: $form.find('#reg_user_email').val(),
				email: $form.find('#reg_user_email').val(),
				nonce: $form.find('#register_nonce_field').val(),
				password: $form.find('#reg_user_password').val(),
				confirm: $form.find('#reg_user_confirm').val()
		};
		
		$.post(info.ajaxurl, data, function (response) {
			if (response && response.success) {
				// Remove error messages
				$('.register-form-validator-error').remove();

				// Remove form
				$("#pl_lead_register_form_inner_wrapper").slideUp();

				// Show success message
				$("#pl_lead_register_form .success").show('fast');

				// Reload window so it shows new login status
				setTimeout(function () { window.location.reload(true); }, 1000);
			}
			else {
				// Error Handling
				var errors = (response && response.errors) ? response.errors : {};

				// jQuery Tools Validator error handling
				$form.validator();

				// Take possible errors and create new object with correct ones to pass to validator
				error_keys = new Array("user_email", "user_password", "user_confirm");
				error_obj = new Object();

				for (key in errors) {
					if (error_keys.indexOf(key) != -1) {
						error_obj[key] = errors[key];
					}
				}

				$form.find('input').data("validator").invalidate(error_obj);
			}
		}, 'json');
	}

	// Called with form data after validation 
	function login_user (form_el) {
		var $form = $(form_el).closest('form');

		data = {
				action: "pl_login_site_user",
				username: $form.find('#user_login').val(),
				password: $form.find('#user_pass').val(),
				remember: $form.find('#rememberme').val()
		};

		$.post(info.ajaxurl, data, function (response) {
			// If request successfull empty the form...
			if (response && response.success) {
				// Remove error messages...
				$('.login-form-validator-error').remove();

				// Hide form...
				// $("#pl_login_form_inner_wrapper").slideUp();
				$.fancybox.close();

				// Show success message
				// setTimeout(function() { $('#pl_login_form .success').show('fast'); }, 500);

				// Reload window so it shows new login status
				window.location.reload(true);
			} 
			else {
				// Error Handling
				var errors = (response && response.errors) ? response.errors : {};

				// jQuery Tools Validator error handling
				$form.validator();

				// Take possible errors and create new object with correct ones to pass to validator
				error_keys = new Array("user_login", "user_pass");
				error_obj = new Object();

				for (key in errors) {
					if (error_keys.indexOf(key) != -1) {
						error_obj[key] = errors[key];
					}
				}

				$form.find('input').data("validator").invalidate(error_obj);
			}
		}, 'json');
	}

	function validate_register_form (form_el) {
		var $form = $(form_el).closest('form');
		
		if(jQuery().validator) {
			// get fields that are required from form and execute validator()
			var inputs = $form.find("input[required]").validator({
				messageClass: "register-form-validator-error", 
				offset: [10,0],
				message: "<div><span></span></div>",
				position: "top center"
			});

			return inputs.data("validator").checkValidity();
		} else {
			return true;
		}
	}

	function validate_login_form (form_el) {
		var $form = $(form_el).closest('form');

		if(jQuery().validator) {
		// get fields that are required from form and execute validator()
		var inputs = $form.find("input[required]").validator({
			messageClass: "login-form-validator-error", 
			offset: [10,0],
			message: "<div><span></span></div>",
			position: "top center"
		});

			return inputs.data("validator").checkValidity();
		} else {
			return true;
		}
	}
	
	function reset_form () {
		$('#fancybox-content').find('form').each(function() {
			this.reset()
		});
	}

	/*
	 * Property/Listing "favorites" functionality...
	 */

	// Don't ajaxify the add to favorites link for guests
	$('#pl_add_favorite:not(.guest)').live('click', function (event) {
		event.preventDefault();

		var spinner = $(this).parent().find(".pl_spinner");
		spinner.show();

		property_id = $(this).attr('href');

		data = {
				action: 'add_favorite_property',
				property_id: property_id.substr(1)
		};

		var that = this;
		$.post(info.ajaxurl, data, function (response) {
			spinner.hide();

			// This property will only be set if WP determines user is of admin status...
			if ( response.is_admin) {
				alert('Sorry, admins currently aren\'t able to maintain a list of "favorite" listings');
			}

			if ( response.id ) {
				$(that).parent().find('#pl_add_favorite').hide();
				$(that).parent().find('#pl_remove_favorite').show();

				if (typeof window.plsUserFavs !== 'undefined') {
					plsUserFavs.push(parseInt(data.property_id));
				}
			}
		}, 'json');
	});

	$('#pl_remove_favorite').live('click',function (event) {
		event.preventDefault();
		var that = this;
		$spinner = $(this).parent().find(".pl_spinner");
		$spinner.show();

		property_id = $(this).attr('href');
		data = {
				action: 'remove_favorite_property',
				property_id: property_id.substr(1)
		};

		$.post(info.ajaxurl, data, function (response) {
			$spinner.hide();
			// If request successfull
			if ( response != 'errors' ) {
				$(that).parent().find('#pl_remove_favorite').hide();
				$(that).parent().find('#pl_add_favorite').show();

				if (typeof window.plsUserFavs !== 'undefined') {
					for (var i in plsUserFavs) {
						if (plsUserFavs[i] === parseInt(property_id.substr(1))) {
							delete plsUserFavs[i];
						}
					}
				}
			}
		}, 'json');
	}); 

/* TODO: Get FB login working...

	//
	// Facebook Login
	//

	// Additional JS functions here
	window.fbAsyncInit = function() {
		fb_init();

		// check FB login status
		FB.getLoginStatus(function(response) {

		// Is user logged into FB?
		if (response.status === 'connected') {
			// var accessToken = response.authResponse.accessToken;
			console.log(response);
			var user_id = response.authResponse.userID;


			// get user info
			var u_info = '';

			FB.api('/me', function(user) {
				console.log(user);
				u_info = user;
			});

			// console.log(u_info);

			// verified_response = parse_signed_request(signed_request);
			// if (verified_response) {
			//   connect_wp_fb(user_id);
			// } else {
			//   console.log('sorry, something went wrong');
			// }

			// log in user if user_id exists in our user list via ajax

			// else prompt them to register
			} 
			else if (response.status === 'not_authorized') {
				// not_authorized
				console.log("not authorized");
				// login();
			} 
			else {
				// not_logged_in
				console.log("not logged in");
				// add login button
				// login_to_fb();
			}
		});
	};

	function fb_init () {
		FB.init({
			appId: "263914027073402", // App ID
			channelUrl: "<?php echo get_template_directory_uri(); ?>/fb_channel.html", // Channel File
			status: true, // check login status
			cookie: true, // enable cookies to allow the server to access the session
			xfbml: true  // parse XFBML
		});
	}

	function connect_wp_fb (user_id) {
		data = {
			action: 'connect_wp_fb',
			user_id: user_id//,
			// user_nickname: user_nickname
		};

		$.ajax({
			url: info.ajaxurl,
			data: data, 
			async: false,
			type: "POST",
			success: function (response) { 
				// console.log(response); 
			}
		});
	}

	function parse_signed_request (signed_request) {
		data = {
			action: 'parse_signed_request',
			signed_request: signed_request
		};

		success = false;

		$.ajax({
			url: info.ajaxurl,
			data: data, 
			async: false,
			type: "POST",
			success: function (response) {
				success = true;
			}
		});

		return success;
	}
*/

});
