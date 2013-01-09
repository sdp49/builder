<?php

PL_Admin_Util::init();

class PL_Admin_Util {

	const ESCAPE_ARG = 'content';

	// Stores nav objects -- basically the object representation of the functionality that will be displayed.
	// NOTE: Populated by calls to constructNav(), utilized in many places.
	public static $navs = array();

	public static function init () {
		add_action( 'template_redirect', array( __CLASS__, 'load_framework') );
	}

	public static function load_framework () {
	    // Try to retrieve this object to ensure that the request is NOT coming from the customizer...
	    global $wp_customize;
	    	    
	    if ( current_user_can('manage_options') && empty($wp_customize) && empty($_GET[self::ESCAPE_ARG]) ) {
	        // Load PL admin panel...
	        PL_Router::load_builder_view('main.php', trailingslashit(PL_VIEWS_DIR) . 'pl-admin/');

			// ob_start();
			// 	var_dump($_SERVER);
			// error_log(ob_get_clean());

	        // Make sure WP doesn't load any other templates...
	        exit;
	    }    
	} 

	public static function getContentURI () {
		$escape_admin = ( '?' . self::ESCAPE_ARG . '=true' );
		$iframe_url = ( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $escape_admin );
		return $iframe_url;
	}

	public static function getAdminURI () {
		return ( 'http://' . trailingslashit($_SERVER['HTTP_HOST']) . 'wp-admin/' ); 
	}

	public static function getBreadcrumbs ( $enabled = true ) {
		$host = $_SERVER['HTTP_HOST'];
		$uri_parts = explode('/', $_SERVER['REQUEST_URI']);

		// Re-build the full URL as breadcrumbs are printed...
		$runningURL = 'http://' . trailingslashit($host);

		ob_start();
		?>
		  <ul id="pls-breadcrumbs" class="<?php echo ( $enabled ? 'enabled' : 'disabled' ); ?>">
	      	  <li><a href="<?php echo $runningURL; ?>"><?php echo $host; ?><span class="a-down"></span></a></li>
	        <?php foreach ( $uri_parts as $part ): ?>
	          <?php if ( empty($part) ) { continue; } 
	          		else { $runningURL .=  trailingslashit($part); }
	          ?>
	          <li>/</li>           
	       	  <li><a href="<?php echo esc_url( $runningURL ); ?>"><?php echo esc_html( ucfirst($part) ); ?></a></li>
	       	<?php endforeach; ?>                                    
	      </ul>
		<?php
		$breadcrumbs = ob_get_clean();

		return $breadcrumbs;
	}

	public static function getAnchorList ( $type = 'admin' ) {
		global $PL_ADMIN_HEADER;
		// Make sure anchor list exists...
		$id = "{$type}-links";
		$links = $PL_ADMIN_HEADER[$id];
		if ( empty($links) ) { return null; }

		ob_start();
		?>
		  <ul>
            <?php foreach ( $links as $text => $url ): ?>	      
	       	  <li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( ucfirst($text) ); ?></a></li>
	       	<?php endforeach; ?> 	
          </ul>
		<?php
		$anchorList = ob_get_clean();

		return $anchorList;
	}

	public static function constructButtons () {
		global $PL_ADMIN_HEADER;
		// Make sure button config exists...
		$id = 'buttons';
		$config = $PL_ADMIN_HEADER[$id];
		if ( empty($config) ) { return null; }

		ob_start();
		?>
		  <?php foreach ( $config as $key => $buttonObj ): extract($buttonObj) ?>
		    <a class="button <?php echo esc_attr( $class ); ?>" href="<?php echo esc_attr( $action ); ?>"><?php echo esc_html( ucfirst($text) ); ?></a>
		  <?php endforeach; ?>
		<?php
		$buttons = ob_get_clean();

		return $buttons;	
	}

	/*
	 * Functions related to the "Nav" groups on the left sidebar
	 */

	public static function constructNav ( $id ) {
		global $PL_ADMIN_NAVS;
		// Make sure nav config exists...
		$config = $PL_ADMIN_NAVS[$id];
		if ( empty($config) ) { return null; }

		// Constuct an empty Nav...
		$nav = new PL_Admin_Nav($id);

		foreach ( $config as $sectionID => $args ) {
			// Check for custom entity, otherwise use generic class... (NOTE: Class names are NOT case-sensitive in PHP)
			$entity = "PL_Admin_Section_{$sectionID}";
			$new_section = ( class_exists($entity) ? new $entity($sectionID, $args) : new PL_Admin_Section($sectionID, $args) );
			
			// Construct and append the new section's associated cards...
			self::constructCards($new_section);

			// Add new section to the nav...
			$nav->add_section($new_section);
		}

		// Store for use later on by other functions...
		self::$navs[$id] = $nav;

		return $nav;
	}

	public static function renderNavs ( $navList = array() ) {
	  ob_start();	
		foreach ( $navList as $navID ) {
			$nav = self::constructNav($navID);
			$nav->render();
		}
	  return ob_get_clean();	
	}

	/* 
	 * Given a section object, construct and append the corresponding card objects. 
	 */
	private static function constructCards ( $section_obj ) {
		global $PL_ADMIN_CARDS;
		// Make sure a card group definition exists for the give section in the config...
		$config = $PL_ADMIN_CARDS[$section_obj->id];
		if ( empty($config) ) { return null; }

		foreach ( $config as $cardID => $args ) {
			// Check for custom entity, otherwise use generic class... (NOTE: Class names are NOT case-sensitive in PHP)
			$entity = "PL_Admin_Card_{$cardID}";
			error_log ("{$entity} exists: " . class_exists($entity));
			$new_card = ( class_exists($entity) ? new $entity($cardID, $args) : new PL_Admin_Card($cardID, $args) );

			// Add new card to the section...
			$section_obj->add_card($new_card);
		}
	}

	/*
	 * Render the cards of any previously constructed (and stored) Sections objects (stored in Navs)
	 */
	public static function renderPane () {
		foreach ( self::$navs as $nav) {
			$nav->render_content();
		}
	}
}

?>