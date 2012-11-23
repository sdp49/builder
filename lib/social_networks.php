<?php

// PL_Social_Networks::init();

define( 'SOCIAL_DEBUGGER', false );

function debug_nasty_socials( $arg, $color = 'black' ) {
	if( SOCIAL_DEBUGGER ) {
		echo "<pre style='color: $color'>";
		var_dump( $arg );
		echo "</pre>";
	}
}

PL_Social_Networks_Twitter::init();

class PL_Social_Networks_Twitter {
	public static $plugin_dir;
	public static $plugin_url;
	
	// Twitter related variables
	public static $user_token = NULL;
	public static $user_token_secret = NULL;
	public static $user_meta_key_token = 'pl_twitter_token';
	public static $user_meta_key_token_secret = 'pl_twitter_token_secret';
	public static $logged_user = NULL;
	public static $admin_redirect_uri = NULL;
	
	// Facebook related variables
	public static $fb_user_meta_key_token = 'fb_token';
	public static $fb_token_name = 'FBLoginToken';
	public static $logged_in = FALSE;
	public static $fb = NULL;
	public static $fb_token = NULL;
	public static $fb_profile = NULL;
	public static $fb_default_proxy_url = 'http://plsbridge.dxdemos.eu/';
	
	public static function init() {
		// init for Twitter
		add_action( 'admin_init', array( __CLASS__, 'verify_user_logged'), 3 );
		add_action( 'admin_init', array( __CLASS__, 'prevent_headers_already_sent_options' ), 1 );
		
		self::$plugin_dir = plugin_dir_path( __FILE__ );
		self::$plugin_url = plugin_dir_url( __FILE__ );
		add_action( 'admin_init', array( __CLASS__, 'init_admin_redirect_uri' ), 1 );
		add_action( 'admin_menu', array( __CLASS__, 'add_social_settings_page' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_post_metaboxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post_social_messages' ) );
		
		// Facebook init
		add_action( 'init', array( __CLASS__, 'fb_login_callback' ) );
		add_action( 'pl_twitter_display', array( __CLASS__, 'twitter_handler' ) );
		add_action( 'pl_facebook_display', array( __CLASS__, 'facebook_handler' ) );
	}
	
	public static function verify_user_logged() {
		if( is_user_logged_in() ) {
			self::$logged_user = wp_get_current_user();
			debug_nasty_socials( 'in verify user logged' );
			$current_user_id = self::$logged_user->ID;
		
			if( ! empty( $current_user_id ) ) {
				$user_token = get_user_meta( $current_user_id, self::$user_meta_key_token, true );
				if( ! empty( $user_token ) ) {
					self::$user_token = $user_token;
				}
				$user_token_secret = get_user_meta( $current_user_id, self::$user_meta_key_token_secret, true );
				if( ! empty( $user_token_secret ) ) {
					self::$user_token_secret = $user_token_secret;
				}
				return true;
			}
		}
		return false; 
	}
	
	/**
	 * Init the twitter and facebook redirect URI, unique for a site domain
	 */
	public static function init_admin_redirect_uri() {
		$admin_url = admin_url( 'options-general.php?page=placester-social' );
		self::$admin_redirect_uri = $admin_url;
 		define('OAUTH_CALLBACK', $admin_url );
	}

	/**
	 * Add a page for social settings
	 */
	public static function add_social_settings_page() {
		add_options_page('Social Networks', 'Social Networks', 'manage_options', 
						'placester-social', array( __CLASS__, 'add_social_settings_cb' ) );
	}
	
	/**
	 * Call settings page callback content for socials
	 */
	public static function add_social_settings_cb() {
		if( is_user_logged_in() && ! empty( self::$logged_user ) ) {
			$current_user_id = self::$logged_user;
			
			// Clear database variables based on a GET request
			if( isset( $_GET['logout_clear'] ) && $_GET['logout_clear'] == 'twitter' ) {
 				delete_user_meta( $current_user_id, self::$user_meta_key_token );
 				delete_user_meta( $current_user_id, self::$user_meta_key_token_secret );
 				wp_redirect( self::$admin_redirect_uri );
 				exit;
			}
			if( isset( $_GET['logout_clear'] ) && $_GET['logout_clear'] == 'facebook' ) {
				delete_user_meta( $current_user_id, self::$fb_user_meta_key_token );
				wp_redirect( self::$admin_redirect_uri );
				exit;
			}
			
			// Call the template that loads Twitter and Facebook hooks
			include_once( PL_VIEWS_ADMIN_DIR . 'social/social.php');
		}
	}
	
	/**
	 * Manage all Facebook related work
	 */
	public static function facebook_handler() {
		self::facebook_callback();
	}
	
	/**
	 * Manage steps 1-5 for Twitter authorization
	 */
	public static function twitter_handler() {
		// Mandatory configs
		include_once PL_LIB_DIR . 'twitteroauth/config.php';
		include_once PL_LIB_DIR . 'twitteroauth/twitteroauth/twitteroauth.php';
			
		// Start a session if there hasn't been started yet
		if( session_id() == '' ) {
			session_start();
		}
			
		debug_nasty_socials('User token: ');
		debug_nasty_socials(self::$user_token, 'red');
			
		debug_nasty_socials($_REQUEST, 'red');
		debug_nasty_socials($_SESSION, 'yellow');
		
		// Step 5 - we already know the user, he's authorized, we have the data in DB
		if( ! empty( self::$user_token ) && ! empty( self::$user_token_secret ) ) {
			$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET, self::$user_token, self::$user_token_secret );
		
			if( isset( $_GET['postme'] ) ) {
				$post_msg = urldecode( $_GET['postme'] );
				$connection->post('statuses/update', array('status' => $post_msg));
			}
				
			$user = $connection->get('account/verify_credentials');
			if( is_object( $user ) ) {
				echo "<p>Hello, " . $user->screen_name . "</p>";
			}
			
			echo '<p><a href="' . self::$admin_redirect_uri .'&logout_clear=twitter">Logout from Twitter</a></p>';
			debug_nasty_socials( 'Authorized:', 'brown');
			debug_nasty_socials($content, 'brown');
		} else {
			// Steps 1 through 4 for authentication
			if( isset( $_GET['oauth_token'] ) && isset( $_GET['oauth_verifier'] ) ) {
				if( ! isset( $_SESSION['first_token'] ) ) {
					$_SESSION['first_token'] = $_GET['oauth_token'];
					self::step3_login();
				} else {
					// Update session due to step 3
					session_destroy();
					session_start();
					self::step4_login();
				}
			}
			else if( isset( $_GET['social_action'] ) && $_GET['social_action'] === 'twitter-redirect' ) {
				self::step2_login();
			} else {
				self::step1_login();
			}
		}
	}
	
	/**
	 * Step 1 - initial 'Sign in' button
	 */
	public static function step1_login() {
		if (CONSUMER_KEY === '' || CONSUMER_SECRET === '') {
			echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://twitter.com/apps">https://twitter.com/apps</a>';
			exit;
		}
		
		/* Build an image link to start the redirect process. */
		$content = '<a href="' . self::$admin_redirect_uri . '&social_action=twitter-redirect"><img src="' . PL_LIB_URL . 'twitteroauth/images/lighter.png" alt="Sign in with Twitter"/></a>';
			
		echo $content;
		/* Include HTML to display on the page. */
		// include PL_LIB_DIR . 'twitteroauth/html.inc';
	}
	
	/**
	 * Step 2 - the Redirect to Twitter for auth
	 */
	public static function step2_login() {
		include_once PL_LIB_DIR . 'twitteroauth/redirect.php';
	}
	
	/**
	 * Step 3 - do the twist (i.e. verify that token of yours)
	 */
	public static function step3_login() {
		$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET );
		$url = $connection->getAuthorizeURL( $_GET['oauth_token'], FALSE );
		debug_nasty_socials('URL to redrect to!', '#FF00AA');
		debug_nasty_socials( $url, 'yellow' );
// 		die();
		wp_redirect( $url );
		exit;
	}
	/**
	 * Step 4 - already authorized, use the correct tokens now!
	 */
	public static function step4_login() {
		$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET );
// 		$token_consumer = $connection->getCorrectAccessToken( $_GET['oauth_token'], $_GET['oauth_verifier'], 'POST' );
		$token_consumer = $connection->getAccessToken( $_GET['oauth_verifier'] );

		// check is not processed before that
		if( self::verify_user_logged() ) {
			if( isset( $token_consumer['oauth_token'] ) && isset( $token_consumer['oauth_token_secret'] ) ) {
				update_user_meta( self::$logged_user->ID, self::$user_meta_key_token, $token_consumer['oauth_token']);
				update_user_meta( self::$logged_user->ID, self::$user_meta_key_token_secret, $token_consumer['oauth_token_secret']);
				
				wp_redirect( self::$admin_redirect_uri );
				exit;
			}
			
			debug_nasty_socials(' token and verifier - okay ', 'blue');
			debug_nasty_socials( $token_consumer, 'red' );
		} else {
			debug_nasty_socials(' No user verified ');
		}
	}
 
	/**
	 * Add post metaboxes for posts and pages
	 */
	public static function add_post_metaboxes() {
		add_meta_box(
			'pl_social_box',
			__( 'Placester Social', 'pls' ),
			array( __CLASS__, 'add_post_metaboxes_callback' ),
			'page', // leave empty quotes as '' if you want it on all custom post add/edit screens
			'side',
			'high'
		);
		add_meta_box(
			'pl_social_box',
			__( 'Placester Social', 'pls' ),
			array( __CLASS__, 'add_post_metaboxes_callback' ),
			'post', // leave empty quotes as '' if you want it on all custom post add/edit screens
			'side',
			'high'
		);
	}
	
	/**
	 * Content for metaboxes
	 */
	public static function add_post_metaboxes_callback() {
	?>
		<h3>Facebook</h3>
		<p><input type="text" name="pl_facebook_message" /></p>
		<h3>Twitter</h3>
		<p><input type="text" name="pl_twitter_message" /></p>
	<?php 
	}

	/**
	 * Save hook for post social messages
	 */
	public static function save_post_social_messages( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		// Verify nonces for ineffective calls
// 		if( !isset( $_POST['pl_social_nonce'] ) || !wp_verify_nonce( $_POST['pl_social_nonce'], 'pl_social_nonce' ) ) return;
		
		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_post' ) ) return;
		
		// Handle Facebook and Twitter messaging
		if ( !wp_is_post_revision( $post_id ) ) {
			$slug = get_permalink( $post_id );
			
			
			if( ! empty( $_POST['pl_facebook_message'] ) ) {
				$facebook = self::get_facebook_object();

				if( $facebook ) {
					$message = $_POST['pl_facebook_message'];
					$facebook->api("/me/feed", "post", array(
									    message => $message,
									    link => $slug,
									));
				}
			}
			
			if( ! empty( $_POST['pl_twitter_message'] ) ) {
				$twitter = self::get_twitter_object();
				if( $twitter ) {
					$message = $_POST['pl_twitter_message'];
					$twitter->post('statuses/update', array('status' => $message . ': ' . $slug ) );
				}	
			}
		}
	}
	
	/**
	 * Stop automatic header sent when loading the admin template (this breaks the redirect to twitter part)
	 */
	public static function prevent_headers_already_sent_options( ) {
		$request_uri = $_SERVER['REQUEST_URI'];
		
		if( false !== strpos( $request_uri, 'page=placester-social&social_action=twitter-redirect' )
		|| ( false !== strpos( $request_uri, 'page=placester-social' ) && false !== strpos( $request_uri, 'oauth_verifier=' ) )
		|| ( false !== strpos( $request_uri, 'page=placester-social' ) && false !== strpos( $request_uri, 'logout_clear=' ) ) ) {
		
			ob_start();
		}
	}
	
	/**
	 * Facebook functions
	 */
	
	/**
	 * Get the login callback for communication with the proxy
	 */
	public static function fb_login_callback() {
		include_once PL_LIB_DIR . 'facebook-php-sdk/src/facebook.php';

		if ( isset( $_GET[ self::$fb_token_name ] ) ) {
			update_user_meta( get_current_user_id(), self::$fb_user_meta_key_token, $_GET[ self::$fb_token_name ] );
			$redirect = self::fb_get_clean_url( get_bloginfo('url') . $_SERVER['REQUEST_URI'] );
	
 			die( '<script type="text/javascript">top.location.href = "' .  $redirect . '";</script>' );
		}
	
		self::$logged_in = FALSE;
	
		if( ! is_user_logged_in() )
			return;
	
		self::$fb_token = get_user_meta( get_current_user_id(), self::$fb_user_meta_key_token, true );
	
		self::$fb = new Facebook( array( 'appId' => NULL, 'secret' => NULL ) );
		self::$fb->setAccessToken( self::$fb_token );
	
		try {
			self::$fb_profile = self::$fb->api( '/me' );
		}
		catch( FacebookApiException $e ) {
			error_log($e->getMessage());
			return;
		}
	
		self::$logged_in = TRUE;
	}
	
	public static function save_settings() {
		if( !current_user_can('manage_options') )
			return;
	
		$fb_proxy_url = isset( $_POST['fb_proxy_url'] ) ? $_POST['fb_proxy_url'] : '';
		if(!$fb_proxy_url || !preg_match( "#^https?://(www\.)?[^\.]+\..+#", $fb_proxy_url ) )
			return;
	
		$fb_proxy_url = str_replace( array('"', '\''), '', $fb_proxy_url );
	
		update_option( 'fb_proxy_url', $fb_proxy_url );
	}
	
	/**
	 * Print the login URL for Facebook
	 * 
	 * Part of a function to be reused in other sections if needed
	 * 
	 */
	public static function fb_print_login_url() {
		$proxy_url = get_option( 'fb_proxy_url', self::$fb_default_proxy_url );
		
		echo '<a href="' . trailingslashit( $proxy_url ) . 'login.php">Log In with Facebook</a>' . "<br />\n\n"; 
		// . print_r($this->_profile, true) . "<br />\n\n";
	}
	
	/**
	 * Manage Facebook arguments and add to temporary array for building proper query to the proxy
	 * @return string url
	 */
	private static function fb_get_clean_url( $url ) {
		$tmp = parse_url( $url );
	
		if ( !is_array( $tmp ) )
			return $url;
	
		$tmp_query = array();
		parse_str( @$tmp['query'], $tmp_query );
	
		unset($tmp_query[self::$fb_token_name]);
	
		$tmp['query'] = http_build_query( $tmp_query );
	
		if ( strlen( $tmp['query'] ) )
			$tmp['query'] = '?' . $tmp['query'];
	
		return $tmp['scheme'] . '://' . $tmp['host'] . $tmp['path'] . $tmp['query'];
	}
	
	/**
	 * Menu page call
	 */
	
	public static function facebook_callback() {
		include_once( PL_VIEWS_ADMIN_DIR . 'social/facebook.php');
	}
	
	/**
	 * Property-alike helper functions for templates
	 */
	public static function get_user_meta_key_name() {
		return self::$fb_user_meta_key_token;
	}
	
	public static function get_token_key_name() {
		return self::$fb_token_name;
	}
	
	public static function is_logged_in() {
		return self::$logged_in;
	}
	
	public static function get_profile() {
		return self::$fb_profile;
	}
	
	/**
	 * Helpers for checking whether a user is logged in or not
	 */
	
	public static function is_facebook_authenticated() {
		if( ! is_user_logged_in() ) {
			return false;
		}
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		
		$user_facebook_token = get_user_meta( $current_user_id, self::$fb_user_meta_key_token, true );
		if( empty( $user_facebook_token ) ) {
			return false;
		}
		
		self::$fb_token = $user_facebook_token;
		
		return true;
	}
	
	public static function is_twitter_authenticated() {
		if( ! is_user_logged_in() ) {
			return false;
		}
		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;
		
		$user_twitter_token = get_user_meta( $current_user_id, self::$user_meta_key_token, true );
		$user_twitter_token_secret = get_user_meta( $current_user_id, self::$user_meta_key_token_secret, true );
		if( empty( $user_twitter_token ) || empty( $user_twitter_token_secret ) ) {
			return false;
		}
		
		self::$user_token = $user_twitter_token;
		self::$user_token_secret = $user_twitter_token_secret;
		
		return true;
	}
	
	/**
	 * Helper functions for getting the objects for Twitter/Facebook management
	 */
	
	/**
	 * Get Twitter object
	 * @return TwitterOAuth or false
	 */
	public static function get_twitter_object() {
		if( self::is_twitter_authenticated() ) {
			include_once PL_LIB_DIR . 'twitteroauth/config.php';
			include_once PL_LIB_DIR . 'twitteroauth/twitteroauth/twitteroauth.php';
			
			$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET, self::$user_token, self::$user_token_secret );
			
			return $connection;
		}
		
		return false;
	}
	
	/**
	 * Get Facebook object
	 * @return Facebook object or false
	 */
	public static function get_facebook_object() {
		if( self::is_facebook_authenticated() ) {
			include_once PL_LIB_DIR . 'facebook-php-sdk/src/facebook.php';

			self::$fb = new Facebook( array( 'appId' => NULL, 'secret' => NULL ) );
			self::$fb->setAccessToken( self::$fb_token );
			
			try {
				self::$fb_profile = self::$fb->api( '/me' );
				
				
				return self::$fb;
			}
			catch( FacebookApiException $e ) {
				error_log($e->getMessage());
				return false;
			}
		}
		
		return false;
	}

}