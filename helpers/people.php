<?php 

PL_People_Helper::init();

class PL_People_Helper {

	public static function init () {
		add_action('wp_ajax_add_person', array(__CLASS__, 'add_person_ajax'));
		add_action('wp_ajax_get_favorites', array(__CLASS__, 'get_favorites_ajax'));

		add_action('wp_ajax_add_favorite_property', array(__CLASS__,'ajax_add_favorite_property'));
		add_action('wp_ajax_nopriv_add_favorite_property', array(__CLASS__,'ajax_add_favorite_property'));
		add_action('wp_ajax_remove_favorite_property', array(__CLASS__,'ajax_remove_favorite_property'));

		add_shortcode('favorite_link_toggle', array(__CLASS__,'placester_favorite_link_toggle'));

		add_action('wp_footer', array(__CLASS__, 'print_login_status'));
		add_action('wp_footer', array(__CLASS__, 'print_user_favorites'));
	}

	public static function add_person ($args = array()) {
		// Try to push lead to CRM (if one is linked/active)...
		self::add_person_to_CRM($_POST);	

		return PL_People::create($args);
	}	

	public static function add_person_ajax () {
		$api_response = PL_People::create($_POST);
		echo json_encode($api_response);

		// Try to push lead to CRM (if one is linked/active)...
		self::add_person_to_CRM($_POST);

		die();
	}

	public static function add_person_to_CRM ($args = array()) {
		// Check to see if site is actively linked to a CRM...
		$activeCRMKey = 'pl_active_CRM';
		$crm_id = PL_Options::get($activeCRMKey);
		
		if (!empty($crm_id)) {
			// Load CRM libs...
			$path_to_CRM = trailingslashit(PL_LIB_DIR) . 'CRM/controller.php';
			include_once($path_to_CRM);

			// Call necessary lib to add the contact to the active/registered CRM...
			if (class_exists('PL_CRM_Controller')) {
				PL_CRM_Controller::callCRMLib('createContact', $args);
			}
		}
	}

	public static function update_person_details ($person_details) {
		$placester_person = self::person_details();
		return PL_People::update(array_merge(array('id' => $placester_person['id']), $person_details));
	}

	// Fetch a site user's details based on his/her unique Placester ID (managed by Rails, stored in WP's usermeta table)
	public static function person_details () {
		$details = array();
		$wp_user = wp_get_current_user();

		if (!empty($wp_user->ID)) {
			$placester_id = get_user_meta($wp_user->ID, 'placester_api_id');
		
			if (is_array($placester_id)) { 
				$placester_id = implode($placester_id, ''); 
			}
			
			if (!empty($placester_id)) {
				$details = PL_People::details(array('id' => $placester_id));
			}
		}

		return $details;
	}

	public static function get_favorites_ajax () {
		$placester_person = self::person_details();
		$favs = array();

		if (isset($placester_person['fav_listings']) && is_array($placester_person['fav_listings'])) {
			$favs = $placester_person['fav_listings'];
		}

		echo json_encode($favs);	
		die();
	}

	public static function get_favorite_ids () {
		$person = self::person_details();
		$ids = array();
		if (isset($person['fav_listings'])) {
			foreach ( (array) $person['fav_listings'] as $fav_listings) {
				$ids[] = $fav_listings['id'];
			}
		}

		return $ids;
	}

	public static function ajax_add_favorite_property () {
		// Check to see if user is an admin (at this point, we know the user is logged in...)
		if (current_user_can('manage_options')) {
			$val = json_encode(array('is_admin' => true));
		}
		else if ($_POST['property_id']) {
			$api_response = self::associate_property($_POST['property_id']);
			$val = json_encode($api_response);
		} 
        else {
			$val = false;
		}

        echo $val;
		die();
	}

	public static function associate_property ($property_id) {
		$placester_person = self::person_details();
		$new_favorites = array($property_id);
		if (isset($placester_person['fav_listings']) && is_array($placester_person['fav_listings'])) {
			foreach ($placester_person['fav_listings'] as $fav_listings) {
				$new_favorites[] = $fav_listings['id'];
			}
		}

		return PL_People::update(array('id' => $placester_person['id'], 'fav_listing_ids' => $new_favorites ) );
	}

	public static function ajax_remove_favorite_property () {
		if ($_POST['property_id']) {
			$api_response = self::unassociate_property($_POST['property_id']);
			echo json_encode($api_response);
		}

        die();
	}

	public static function unassociate_property ($property_id) {
		$placester_person = self::person_details();
		$new_favorites = array();
		if (is_array($placester_person['fav_listings'])) {
			foreach ($placester_person['fav_listings'] as $fav_listings) {
				if ($fav_listings['id'] != $property_id) {
					$new_favorites[] = $fav_listings['id'];
				}
			}
		}

		return PL_People::update(array('id' => $placester_person['id'], 'fav_listing_ids' => $new_favorites ) );
	}

	public static function is_favorite_property ($property_id) {
        $person = self::person_details();
        $is_fav = false;

        if ( isset($person['fav_listings']) && is_array($person['fav_listings']) ) {
            foreach ($person['fav_listings'] as $fav_listing) {
                if ($fav_listing['id'] == $property_id) {
                    $is_fav = true;
                }
            }
        }

        return $is_fav;
    }

	/**
     * Adds a "Add property to favorites" link if the user is not logged in, or if the property is not in the favorite list,
     * and a "Remove property from favorites" otherwise
     *
     * TODO: If logged in and not a lead, register the user as such...
     */
    public static function placester_favorite_link_toggle ($atts) {
        $defaults = array(
            'add_text' => 'Add property to favorites',
            'remove_text' => 'Remove property from favorites',
            'spinner' => admin_url( 'images/wpspin_light.gif' ),
            'property_id' => false,
            'wrapping_div' => true
        );

        $args = wp_parse_args( $atts, $defaults );
        extract( $args, EXTR_SKIP );

        $is_lead = current_user_can('placester_lead');
        $is_favorite = is_user_logged_in() ? self::is_favorite_property($property_id) : "";

        ob_start();
        ?>
            <?php pls_do_atomic( 'before_add_to_fav' ); ?>

            <?php if (is_user_logged_in()): ?>
                <?php pls_do_atomic( 'before_add_to_fav_registered' ); ?>
                <a href="<?php echo "#" . $property_id ?>" id="pl_add_favorite" class="pl_prop_fav_link pl_add_favorite" <?php echo $is_favorite ? 'style="display:none;"' : '' ?>><?php echo $add_text ?></a>
            <?php else: ?>
                <?php pls_do_atomic( 'before_add_to_fav_unregistered' ); ?>
                <a class="pl_register_lead_favorites_link" href="#pl_lead_register_form"><?php echo $add_text ?> </a>
            <?php endif ?>

            <a href="<?php echo "#" . $property_id ?>" id="pl_remove_favorite" class="pl_prop_fav_link pl_remove_favorite" <?php echo !$is_favorite ? 'style="display:none;"' : '' ?>><?php echo $remove_text ?></a>
            <img class="pl_spinner" src="<?php echo $spinner ?>" alt="ajax-spinner" style="display:none; margin-left:5px;">

            <?php pls_do_atomic( 'after_add_to_fav' ); ?>

        <?php
        $contents = ob_get_clean();

        if ($wrapping_div == true) {
        	$contents = '<div id="pl_add_remove_lead_favorites" class="pl_add_remove_lead_favorites" style="display:none;">' . $contents . '</div>';
        }

        return $contents;
    }

    /**
     * Lets us determine whether user is logged in via js. Not to be used for anything sensitive.
     */
    public static function print_login_status() {
    	?>
    	<script type="text/javascript">
    		window.plsUserLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;
    	</script>
    	<?php
    }

    /**
     * Defines favorite listing IDs for use in js.
     */
    public static function print_user_favorites() {
    	$fav_listings = is_user_logged_in() ? self::get_favorite_ids() : null;
    	?>
    	<script type="text/javascript">
    		window.plsUserFavs = <?php echo json_encode($fav_listings); ?>;
    	</script>
    	<?php
    }

}