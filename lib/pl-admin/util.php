<?php

PL_Admin_Util::init();

class PL_Admin_Util {

	const SECTION_BASE = 'PL_Admin_Section_';
	const PANE_BASE = 'PL_Admin_Pane_';
	const ESCAPE_ARG = 'content';

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

	public static function constructNav ( $id ) {
		global $PL_ADMIN_NAVS;
		// Make sure nav config exists...
		$config = $PL_ADMIN_NAVS[$id];
		if ( empty($config) ) { return null; }

		// Constuct an empty Nav...
		$nav = new PL_Admin_Nav($id);

		foreach ( $config as $section => $args ) {
			// Check for custom entity, otherwise use generic class...
			$entity = self::SECTION_BASE . $section;
			$new_section = ( class_exists($entity) ? new $entity($args) : new PL_Admin_Section($args) );
			$nav->add_section($new_section);
		}

		return $nav;
	}

	public static function getBreadcrumbs ( $enabled = true ) {
		$host = $_SERVER['HTTP_HOST'];
		$uri_parts = explode('/', $_SERVER['REQUEST_URI']);

		ob_start();
		?>
		  <ul id="pls-breadcrumbs" class="<?php echo ( $enabled ? 'enabled' : 'disabled' ); ?>">
	      	  <li><a href="#"><?php echo $host; ?><span class="a-down"></span></a></li>
	        <?php foreach ( $uri_parts as $part ): ?>
	          <?php if ( empty($part) ) { continue; } ?>
	          <li>/</li>           
	       	  <li><a href="#"><?php echo esc_html( ucfirst($part) ); ?></a></li>
	       	<?php endforeach; ?>                                    
	      </ul>
		<?php
		$breadcrumbs = ob_get_clean();

		return $breadcrumbs;
	}

	public static function constuctButtons () {
		
	}

}

?>