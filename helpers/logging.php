<?php 

PL_Logging::init();
class PL_Logging {

	private static $hook;
	private static $pages = array('placester_page_placester_properties', 
								  'placester_page_placester_property_add',
								  'placester_page_placester_settings', 
								  'placester_page_placester_support', 
								  'placester_page_placester_theme_gallery',
								  'placester_page_placester_settings_client',
								  'placester_page_placester_settings_filtering',
								  'placester_page_placester_settings_polygons',
								  'placester_page_placester_settings_property_pages',
								  'placester_page_placester_settings_international',
								  'placester_page_placester_social',
								  'placester_page_placester_integrations'
								  );

	public static function init() {
	 	$logging_option = PL_Option_Helper::get_log_errors();
	 	if ($logging_option) {
			add_action('admin_head', array(__CLASS__, 'start'));
			add_action('admin_footer', array(__CLASS__, 'events'));
			add_action('admin_enqueue_scripts', array(__CLASS__, 'record_page'));
			register_activation_hook( PL_PARENT_DIR, 'activation' );
		}
	}

	public static function record_page ($hook) {
	 	self::$hook = $hook;
	}

	public static function start () {
		if (!in_array(self::$hook, self::$pages)) { 
			return; 
		} else {
			echo self::mixpanel_inline_js();	
		}
	}

	public static function events () {
		if (!in_array(self::$hook, self::$pages)) { return; }

	 	ob_start();

	 	if (!PL_Option_Helper::api_key()) {
	 		?>
		 		<script type="text/javascript">
		 			jQuery('#signup_wizard').live('dialogopen', function () {
		 				mixpanel.track("SignUp: Overlay Opened");			
		 			});
		 			jQuery('#signup_wizard').live('dialogclose', function () {
		 				mixpanel.track("SignUp: Overlay Closed");			
		 			});
		 			jQuery('#pls_search_form input#email').live('focus', function() {
		 				mixpanel.track("SignUp: Edit Sign Up Email");			
		 			});
		 			jQuery('#confirm_email_button').live('click', function() {
		 				mixpanel.track("SignUp: Confirm Email Click");			
		 			});
		 		</script>	
		 	<?php	
	 	} else {
	 		$page = 'unknown';
	 		switch (self::$hook) {
	 			case 'placester_page_placester_properties':
	 				$page = 'View - Property Index';
	 				break;

	 			case 'placester_page_placester_property_add':
	 				$page = 'View - Property Add';
	 				break;

	 			case 'placester_page_placester_support':
	 				$page = 'View - Property Support';
	 				break;

	 			case 'placester_page_placester_theme_gallery':
	 				$page = 'View - Property Theme Gallery';
	 				break;

	 			case 'placester_page_placester_settings':
	 				$page = 'View - Settings - General';
	 				break;

	 			case 'placester_page_placester_settings_client':
	 				$page = 'View = Settings - Client';
	 				break;

	 			case 'placester_page_placester_settings_filtering':
	 				$page = 'View = Settings - Global Filtering';
	 				break;

	 			case 'placester_page_placester_settings_polygons':
	 				$page = 'View = Settings - Polygons';
	 				break;

	 			case 'placester_page_placester_settings_property_pages':
	 				$page = 'View = Settings - Property Pages Index';
	 				break;

	 			case 'placester_page_placester_settings_international':
	 				$page = 'View = Settings - International Settings';
	 				break;

	 			case 'placester_page_placester_social':
	 				$page = 'View = Settings - Social';
	 				break;

	 			case 'placester_page_placester_integrations':
	 				$page = 'View = Settings - MLS / IDX';
	 				break;
	 		}
	 		?>
	 		<script type="text/javascript">
	 			//Log page views since wordpress always appears as admin.php :(. 
	 			mixpanel.track("<?php echo $page ?>");		
	 		</script>
	 		<?php
	 	}
	 	echo ob_get_clean();
	}

	public static function activation() {

	}

	public static function mixpanel_inline_js() {

		$whoami = PLS_Plugin_API::get_user_details();


		ob_start();
	 	?>
	 		<script type="text/javascript">
			    (function(c,a){window.mixpanel=a;var b,d,h,e;b=c.createElement("script");
			    b.type="text/javascript";b.async=!0;b.src=("https:"===c.location.protocol?"https:":"http:")+
			    '//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';d=c.getElementsByTagName("script")[0];
			    d.parentNode.insertBefore(b,d);a._i=[];a.init=function(b,c,f){function d(a,b){
			    var c=b.split(".");2==c.length&&(a=a[c[0]],b=c[1]);a[b]=function(){a.push([b].concat(
			    Array.prototype.slice.call(arguments,0)))}}var g=a;"undefined"!==typeof f?g=a[f]=[]:
			    f="mixpanel";g.people=g.people||[];h=['disable','track','track_pageview','track_links',
			    'track_forms','register','register_once','unregister','identify','alias','name_tag',
			    'set_config','people.set','people.increment','people.track_charge','people.append'];
			    for(e=0;e<h.length;e++)d(g,h[e]);a._i.push([b,c,f])};a.__SV=1.2;})(document,window.mixpanel||[]);
			    mixpanel.init("9186cdb540264089399036dd672afb10");

				//things that we want to track for every request.
				var core_properties = {
					"first seen": new Date(),
					"$initial referrer": document.referrer,
					"wordpress_location": "<?php echo site_url(); ?>",
					"wordpress_version": "<?php echo get_bloginfo('version'); ?>",
					"wordpress_language": "<?php echo get_bloginfo('language'); ?>",
				};
				//append them to every request.
				mixpanel.register_once(core_properties);

				//conditionally identify if we actually know who this person is.
				<?php if ( is_array($whoami) ): ?>
					mixpanel.identify("<?php echo $whoami['user']['email'] ?>");
					mixpanel.name_tag("Registered - <?php echo $whoami['user']['email']; ?>");				
					var user_data = core_properties;
					user_data['$email'] = "<?php echo $whoami['user']['email'] ?>";
					user_data['$first_name'] = "<?php echo $whoami['user']['first_name'] ?>";
					user_data['$last_name'] = "<?php echo $whoami['user']['last_name'] ?>";
					user_data['wordpress_email'] = "<?php echo get_option('admin_email'); ?>";
					mixpanel.people.set(user_data);
				<?php else : ?>
					mixpanel.name_tag("Unregistered - <?php echo get_option('admin_email'); ?>");
				<?php endif ?>
			</script>
	 	<?php
	 	
	 	return ob_get_clean();
	}
}
