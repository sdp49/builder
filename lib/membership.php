<?php

/**
 * Class Designed to Handle the rigors of Membership, and membership options...
 */

PL_Membership::init();
class PL_Membership {

	public static function init () {
		add_action('wp_ajax_nopriv_pl_register_site_user', array(__CLASS__, 'ajax_register_site_user'));
		add_action('wp_ajax_nopriv_pl_login_site_user', array(__CLASS__, 'ajax_login_site_user'));
		// add_action( 'wp_ajax_nopriv_connect_wp_fb', array(__CLASS__, 'connect_fb_with_wp' ));
		// add_action( 'wp_ajax_nopriv_parse_signed_request', array(__CLASS__, 'fb_parse_signed_request' ));

		add_shortcode('lead_user_navigation', array(__CLASS__, 'placester_lead_control_panel'));
		add_shortcode('pl_login_block', array(__CLASS__, 'placester_lead_control_panel'));
		
		$capabilities = array(
			'add_roomates' => true,
			'read_roomates' => true,
			'delete_roomates' => true,
			'add_favorites' => true,
			'delete_roomates' => true,
			'level_0' => true,
			'read' => true
		);

		// Create the "Property lead" role
		add_role('placester_lead', 'Property Lead', $capabilities);
	}

	public static function get_client_area_url () {
		global $wpdb;
		$page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = 'client-profile'");
		
        return $page_id ? get_permalink($page_id) : '';
	}

	// Callback function for when the frontend lead register form is submitted
	//
    // NOTE: JavaScript in "js/theme/placester.membership.js"
	public static function ajax_register_site_user () {
		$errors = array();
		
        // Make sure it's from a form we created
		if ( !wp_verify_nonce($_POST['nonce'], 'placester_true_registration') ) {
			// Malicious...
			echo "Sorry, your nonce didn't verify -- try using the form on the site";
			die();
		}
		
		// All validation rules in a single place...
		$lead_object = self::validate_registration($_POST);
		
		// Check for lead errors
		if (!empty($lead_object['errors'])) {
			$errors = self::process_registration_errors($lead_object['errors']);
		} 
        else {
			// Try to create the lead...
			$errors = self::create_site_user($lead_object);
		}
		
        $result = empty($errors) ? array("success" => true) : array("success" => false, "errors" => $errors);
        
        echo json_encode($result);
        die();
	}

	public static function create_site_user ($lead_object) {
		$errors = array();

        // Create Wordpress user entity for lead...
        $userdata = array(
            'user_pass' => $lead_object['password'],
            'user_login' => $lead_object['username'],
            'user_email' => $lead_object['metadata']['email'],
            'role' => 'placester_lead'
        );

        $wordpress_user_id = wp_insert_user($userdata);

		if ( !is_wp_error($wordpress_user_id) ) {
			// Force blog to be set immediately or MU throws errors
			$blogs = get_blogs_of_user($wordpress_user_id);
			$first_blog = current($blogs);
			update_user_meta($wordpress_user_id, 'primary_blog', $first_blog->userblog_id);

            // Push the new WP user as a lead to the API...
			$response = PL_People_Helper::add_person($lead_object);
            
			if (isset($response['code'])) {
				$errors[] = $response['message'];
				foreach ($response['validations'] as $key => $validation) {
					$errors[] = $response['human_names'][$key] . implode($validation, ' and ');
				}
				$errors[] = 'placester_create_failed';
			}

			// If the API call was successful, inform the user that his/her password and set the password change
			if (empty($errors)) {
				update_user_meta($wordpress_user_id, 'placester_api_id', $response['id']);
				wp_new_user_notification($wordpress_user_id);
			}
			
			if (PL_Options::get('pls_send_client_option')) {
				wp_mail($lead_object['username'], 'Your new account on ' . site_url(), PL_Membership_Helper::parse_client_message($lead_object) );
			}

			// Login user if successfully signed-up...
			wp_set_auth_cookie($wordpress_user_id, true, is_ssl());
		} 
        else {
			// Failure...
			$errors[] = 'wp_user_create_failed';
		}

        return $errors;
	}

	//  AJAX endpoint for authenticating a site user from the frontend
	public static function ajax_login_site_user () {
        extract($_POST);

		$sanitized_username = sanitize_user($username);
        $errors = array();

		if (empty($sanitized_username)) {
			$errors['user_login'] = "An email address is required";
		} 
        elseif (empty($password)) {
			$errors['user_pass'] = "A password is required";
		} 
        else {
			$userdata = get_user_by('login', $sanitized_username);

			if (empty($userdata)) {
                $errors['user_login'] = "There is no user for this email address";
            }
            else if ($userdata && !wp_check_password($password, $userdata->user_pass, $userdata->ID)) {
                $errors['user_pass'] = "The password isn't correct";
			}
		}

		if (!empty($errors)) {
			$result = array("success" => false, "errors" => $errors);
		} 
        else {
			$rememberme = ($remember == "forever") ? true : false;

			// Manually login user
			$creds['user_login'] = $sanitized_username;
			$creds['user_password'] = $password;
			$creds['remember'] = $rememberme;

			$user = wp_signon($creds, true);

			wp_set_current_user($user->ID);

            $result = array("success" => true);
		}

        echo json_encode($result);
		die();
	}

	// Validates all registration data
	private static function validate_registration ($post_vars) {
		if (is_array($post_vars)) {
			$lead_object['username'] = '';
			$lead_object['metadata']['email'] = '';
			$lead_object['password'] = '';
			$lead_object['name'] = '';
			$lead_object['phone'] = '';
			$lead_object['lead_type'] = get_bloginfo('url');
			$lead_object['errors'] = array();

			foreach ($post_vars as $key => $value) {
				switch ($key) {
					case 'username':
						$username['errors'] = array();
						$username['unvalidated'] = $value;
						$username['validated'] = '';

						//handles all random edge cases
						$username_validation = self::validate_username($username, $lead_object);

						//split verification array
						$username = $username_validation['username'];
						$lead_object = $username_validation['lead_object'];

						// if no errors, set username
						if( empty($username['errors']) ){
							$lead_object['username'] = $username['validated'];
						}

						break;

					case 'email':
						$email['errors'] = array();
						$email['unvalidated'] = $value;
						$email['validated'] = '';

						$email_validation = self::validate_email($email, $lead_object);

						//split verification array
						$email = $email_validation['email'];
						$lead_object = $email_validation['lead_object'];

						if ( empty($email['errors']) ) {
							$lead_object['metadata']['email'] = $email['validated'];
						}

						break;

					case 'password':
						$password['errors'] = array();
						$password['unvalidated'] = $value;
						$confirm_password = $post_vars['confirm'];
						$password['validated'] = '';

						$password_validation = self::validate_password($password, $confirm_password, $lead_object);

						//split verification array
						$password = $password_validation['password'];
						$lead_object = $password_validation['lead_object'];

						if ( empty($password['errors']) ) {
							$lead_object['password'] = $password['validated'];
						}
						break;

					case 'name':
						// we'll be fancy later.
						if ( !empty($value) ) {
							$lead_object['name'] = $value;
						}
						break;

					case 'phone':
						// we'll be fancy later.
						if ( !empty($value) ) {
							$lead_object['phone'] = $value;
						};
				}
			}
		}

		return $lead_object;
	}

	// Rules for validating passwords
	private static function validate_password ($password, $confirm_password, $lead_object) {
		// Make sure we have password and confirm.
		if ( !empty($password['unvalidated']) && !empty($confirm_password) ) {
			// Make sure they are the same
			if ($password['unvalidated'] == $confirm_password ) {
				$password['validated'] = $password['unvalidated'];
			} 
            else {
				// They aren't the same
				$lead_object['errors'][] = 'password_mismatch';
				$password['errors'] = true;
			}
		} 
        else {
			// Missing one...
			if (empty($password['unvalidated'])) {
				$lead_object['errors'][] = 'password_empty';
				$password['errors'] = true;
			}

			if (empty($confirm_password)) {
				$lead_object['errors'][] = 'confirm_empty';
				$password['errors'] = true;
			}
		}

		return array('password' => $password, 'lead_object' => $lead_object);
	}

	// Rules for validating email addresses
	private static function validate_email ($email, $lead_object) {
		if (empty($email['unvalidated'])) {
			$lead_object['errors'][] = 'email_required';
			$email['errors'] = true;
		} 
        else {
			// Something in email, is it valid?
			if ( is_email($email['unvalidated'] ) ) {
				if ( email_exists($email['unvalidated']) ) {
					$lead_object['errors'][] = 'email_taken';
					$email['errors'] = true;
				} 
                else {
					$email['validated'] = $email['unvalidated'];
				}

			} 
            else {
				$lead_object['errors'][] = 'email_invalid';
				$email['errors'] = true;
			}
		}

		return array('email' => $email, 'lead_object' => $lead_object);
	}

	// Rules for validating the username
	private static function validate_username ($username, $lead_object) {
		// Check for empty..
		if ( !empty($username['unvalidated']) ) {
			// Check to see if it's valid
			$username['unvalidated'] = sanitize_user($username['unvalidated']);

		} 
        else {
			// Generate one from the email, because wordpress requries it
			$lead_object['errors'][] = 'username_empty';
			$username['errors'] = true;

		}

		// Check if username exists...
		if ( username_exists($username['unvalidated']) ) {
			$lead_object['errors'][] = 'username_exists';
			$username['errors'] = true;
		} 
        else {
			$username['validated'] = $username['unvalidated'];
		}

		return array('username' => $username, 'lead_object' => $lead_object);

	}

	// Used for processing errors for the various forms.
	private static function process_registration_errors ($errors) {
        // Default value...
		$error_messages = '';

		foreach ($errors as $error => $type) {

			switch ($type) {
				case 'username_exists':
					// $error_messages['username'][] .= 'That username already exists';
					$error_messages['user_email'] = 'That email is already taken';
					break;

				case 'username_empty':
					// $error_messages['username'][] .= 'Username is required.';
					$error_messages['user_email'] = 'Email is required';
					break;

				case 'email_required':
					$error_messages['user_email'] = 'Email is required';
					break;

				case 'email_invalid':
					$error_messages['user_email'] = 'Your email is invalid';
					break;

				case 'email_taken':
					$error_messages['user_email'] = 'That email is already taken';
					break;

				case 'password_empty':
					$error_messages['user_password'] = 'Password is required';
					break;

				case 'password_mismatch':
					$error_messages['user_confirm'] = 'Your passwords don\'t match';
					break;

				case 'confirm_empty':
					$error_messages['user_confirm'] = 'Confirm password is empty';
					break;

				default:
					$error_messages['user_email'] = 'There was an error, try again soon';
					break;
			}
		}
		
		return $error_messages;
	}

	/**
	* Creates a registration form
	*
	* The paramater will be used as an action for the registration form and it
	* will be used in the ajax callback at submission
	*
	* @param string $role The Wordpress role
	*
	*/
	public static function generate_lead_reg_form ($role = 'placester_lead')
	{
		if ( !is_user_logged_in() ) {
			ob_start();
			?>
			<div style="display:none;">
				<form method="post" action="#<?php echo $role; ?>" id="pl_lead_register_form" name="pl_lead_register_form" class="pl_login_reg_form pl_lead_register_form" autocomplete="off">

					<div style="display:none" class="success">You have been successfully signed up. This page will refresh momentarily.</div>

					<div id="pl_lead_register_form_inner_wrapper">

						<?php pls_do_atomic( 'register_form_before_title' ); ?>

						<h2>Sign Up</h2>

						<?php pls_do_atomic( 'register_form_before_email' ); ?>

						<p class="reg_form_email">
							<label for="user_email">Email</label>
							<input type="text" tabindex="25" size="20" required="required" class="input" id="reg_user_email" name="user_email" data-message="A valid email is needed." placeholder="Email">
						</p>

						<?php pls_do_atomic( 'register_form_before_password' ); ?>

						<p class="reg_form_pass">
							<label for="user_password">Password</label>
							<input type="password" tabindex="26" size="20" required="required" class="input" id="reg_user_password" name="user_password" data-message="Please enter a password." placeholder="Password">
						</p>

						<?php pls_do_atomic( 'register_form_before_confirm_password' ); ?>

						<p class="reg_form_confirm_pass">
							<label for="user_confirm">Confirm Password</label>
							<input type="password" tabindex="27" size="20" required="required" class="input" id="reg_user_confirm" name="user_confirm" data-message="Please confirm your password." placeholder="Confirm Password">
						</p>

						<?php pls_do_atomic( 'register_form_before_submit' ); ?>

						<p class="reg_form_submit">
							<input type="submit" tabindex="28" class="submit button" value="Register" id="pl_register" name="pl_register">
						</p>
						<?php echo wp_nonce_field( 'placester_true_registration', 'register_nonce_field' ); ?>
						<input type="hidden" tabindex="29" id="register_form_submit_button" name="_wp_http_referer" value="/listings/">

						<?php pls_do_atomic( 'register_form_after_submit' ); ?>

					</div>

				</form>
			</div>
			<?php
			$result = ob_get_clean();
		} 
        else {
			ob_start();
			?>
				<div style="display:none">
					<div class="pl_error error" id="pl_lead_register_form">
					You cannot register a user if you are logged in. You shouldn't even see a "Register" link.
					</div>
				</div>
			<?php
			$result = ob_get_clean();
		}

		return $result;
	}

	/**
	 * Adds "Login | Register" if not logged in
	 * or "Logout | My account" if logged in
	 *
	 * TODO If logged in and not lead display something informing them
	 * of what they need to do to register a lead account
	 */
	public static function placester_lead_control_panel ($args) {
    	$fb_registered = false;
    	// Capture users that just logged on w/ FB registration
    	// if (isset($_POST['signed_request'])) {
    	//   $fb_registered = true;
    	//   $signed_request = self::fb_parse_signed_request($_POST['signed_request'], false);
    	// }

    	$defaults = array(
    		'loginout' => true,
    		'profile' => true,
    		'register' => true,
    		'container_tag' => false,
    		'container_class' => false,
    		'anchor_tag' => false,
    		'anchor_class' => false,
    		'separator' => ' | ',
    		'inside_pre_tag' => false,
    		'inside_post_tag' => false,
    		'no_forms' => false // use this to return just the login/logout forms, no links. Do this for all calls to this function after the first on a page.
    	);
    	$args = wp_parse_args( $args, $defaults );
    	extract( $args, EXTR_SKIP );

    	// Register WP user w/ FB creds when FB registration has been triggered
    	// if ($fb_registered) {
    	//   self::connect_fb_with_wp($signed_request);
    	// }

    	$is_lead = current_user_can( 'placester_lead' );

    	/** The login or logout link. */

    	// user isn't logged into WP nor FB
    	if ( !is_user_logged_in() && !$fb_registered ) {
    		$loginout_link = '<a class="pl_login_link" href="#pl_login_form">Log in</a>';
    	} 
        else {
    		$loginout_link = '<a href="' . esc_url( wp_logout_url(site_url()) ) . '" id="pl_logout_link" class="pl_logout_link">Log out</a>';
    	}
    	
        if ($anchor_tag) {
    		$loginout_link = "<{$anchor_tag} class={$anchor_class}>" . $inside_pre_tag . $loginout_link . $inside_post_tag . "</{$anchor_tag}>";
    	}

    	/** The register link. */
    	$register_link = '<a class="pl_register_lead_link" href="#pl_lead_register_form">Register</a>';
    	if ($anchor_tag) {
    		$register_link = "<{$anchor_tag} class={$anchor_class}>" . $inside_pre_tag . $register_link . $inside_post_tag . "</{$anchor_tag}>";
    	}

    	/** The profile link. */
    	$profile_url = self::get_client_area_url();
    	$profile = $profile && $profile_url!=='';
    	$profile_link = '<a id="pl_lead_profile_link" target="_blank" href="' . $profile_url . '">My Account</a>';
    	if ($anchor_tag) {
    		$profile_link = "<{$anchor_tag} class={$anchor_class}>" . $inside_pre_tag . $profile_link . $inside_post_tag . "</{$anchor_tag}>";
    	}
    	// var_dump($profile_link);

    	$loginout_link = $loginout ? $loginout_link : '';
    	$register_link = $register ? ( empty($loginout_link) ? $register_link : $separator . $register_link ) : '';
    	$profile_link = $profile ? ( empty($loginout_link) ? $profile_link : $separator . $profile_link ) : '';

    	if ( !is_user_logged_in() && $no_forms == false ) {
            // Set the URL
            $url = is_home() ? home_url() : get_permalink();

        	ob_start();
        	?>
        		<form name="pl_login_form" id="pl_login_form" action="<?php echo home_url(); ?>/wp-login.php" method="post" class="pl_login_reg_form">

        			<?php pls_do_atomic( 'login_form_before_title' ); ?>

        			<div id="pl_login_form_inner_wrapper">
        				<h2>Login</h2>
        				<!-- redirect-uri="<?php //echo $_SERVER["HTTP_REFERER"]; ?>" -->
        				<!-- <fb:registration fields="name,location,email" width="260"></fb:registration> -->

        				<?php pls_do_atomic( 'login_form_before_email' ); ?>

        				<p class="login-username">
        					<label for="user_login">Email</label>
        					<input type="text" name="user_login" id="user_login" class="input" required="required" value="" tabindex="20" data-message="A valid email is needed" placeholder="Email" />
        				</p>

        				<?php pls_do_atomic( 'login_form_before_password' ); ?>

        				<p class="login-password">
        					<label for="user_pass">Password</label>
        					<input type="password" name="user_pass" id="user_pass" class="input" required="required" value="" tabindex="21" data-message="A password is needed" placeholder="Password" />
        				</p>

        				<?php pls_do_atomic( 'login_form_before_remember' ); ?>

        				<p class="login-remember">
        					<label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="22" /> Remember Me</label>
        				</p>

        				<?php pls_do_atomic( 'login_form_before_submit' ); ?>

        				<p class="login-submit">
        					<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Log In" tabindex="23" />
        					<input type="hidden" name="redirect_to" value="<?php echo $url; ?>" />
        				</p>

        				<?php pls_do_atomic( 'before_login_title' ); ?>

        			</div>

        		</form>
    		<?php

            // Store form HTML
    		$login_form = ob_get_clean();

    		// Base link value...
            $link = $loginout_link . $register_link;

            // Enclose in container tag if set...
            if ($container_tag) {
                $link = "<{$container_tag} class={$container_class}>" . $link . "</{$container_tag}>";
            }

            // Append the form HTML...
            $link .= self::generate_lead_reg_form() . "<div style='display:none;'>{$login_form}</div>";
        } 
        else {
            // Remove the link to the profile if the current user is not a lead...
            $link = $is_lead ? ($loginout_link . $profile_link) : $loginout_link;
            
            if ($container_tag) {
                $link = "<{$container_tag} class={$container_class}>" . $link . "</{$container_tag}>";
            } 
        }

        return $link;
	}

	/*
	 * Facebook user integration functionality
	 *
	 * NOTE: Unfinished/untested/not in use...
	 */
/*
	public static function connect_fb_with_wp ($signed_request) {
		// json_decode signed_request into array
		$signed_request = json_decode($signed_request, true);

		$user_id = $signed_request['user_id'];
		$user_email = $signed_request['registration']['email'];
		$user_name = $signed_request['registration']['name'];
		$userdata = get_user_by( 'login', $user_id );

		if ($userdata) {
			wp_set_current_user($user_id);
			wp_set_auth_cookie($user_id, true);
		} 
        else {
			// Create random password
			$random_pass = self::random_password();

			// User doesn't exist, create user
			$userdata = array(
				'user_pass' => $random_pass,
				'user_login' => $user_id,
				'user_url' => $_SERVER["SERVER_NAME"],
				'user_email' => $user_email,
				'user_nicename' => $user_name,
				'role' => 'placester_lead'
			);

			// Add user to WP user table
			wp_insert_user( $userdata );

			$user = get_user_by('login', $user_id);

			// Send user email w/ login and password
			wp_mail($user_email,
				'Your password for ' . $_SERVER["SERVER_NAME"],
				"to log into " . $_SERVER["SERVER_NAME"] . " your username is '" . $user_email . "', and your password is '" . $random_pass . "'. However, as long as you are signed into Facebook, you won't need to manually sign in."
			);

		}
	}

	// Parse Facebook Signed Request
	public static function fb_parse_signed_request ($signed_request = '', $return = 'ajax') {
		if (empty($signed_request)) {
			extract($_POST);
		}

		list($encoded_sig, $payload) = explode('.', $signed_request, 2);

		// decode the data
		$sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
		$data = base64_decode(strtr($payload, '-_', '+/'));

		if ($return == 'ajax') {
			echo $data;
		} 
        else {
			return $data;
		}

	}

	private static function random_password () {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		
        for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}

		return implode($pass); //turn the array into a string
	}
*/
}