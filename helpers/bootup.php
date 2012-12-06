<?php 

PL_Bootup::init();
class PL_Bootup {

  static $items_that_can_be_created = array(
      'pages' => array(),
      'menus' => array(),
      'posts' => array(),
      'agents' => array(),
      'testimonials' => array(),
      'settings' => array()
  );

	public function init () {
    add_action('switch_theme', array( __CLASS__, 'theme_switch' ));
    add_action('after_switch_theme', array( __CLASS__, 'theme_switch_user_prompt' ));
  }

  public function theme_switch () {
    
    $manifest = wp_parse_args( self::parse_manifest_to_array(), self::$items_that_can_be_created );
    extract($manifest);

    if ( !empty($pages) )  {
      self::create_pages( $pages );
    }

    if ( !empty($menus) ) {
      self::create_menus( $menus );
    }

    if ( !empty($posts) ) {
      self::create_posts( $posts, 'post', $settings );
    }

    if ( !empty($agents) ) {
      self::create_posts( $agents, 'agent', $settings );
    }

    if ( !empty($testimonials) ) {
      self::create_posts( $testimonials, 'testimonial', $settings );
    }

		return true;
	}

  private function create_pages ( $pages ) {
    PL_Pages::create_once( $pages, $force_template = false );
  }

  private function create_menus ( $menus ) {
    PL_Menus::create( $menus );
  }

  private function create_posts ( $posts, $post_type, $settings ) {
    PL_Posts::create( $posts, $post_type, $settings );
  }

  public function theme_switch_user_prompt () {
    PL_Js_Helper::theme_switch();
  }

	private function parse_manifest_to_array () {
		return json_decode( file_get_contents( self::get_current_theme_manifest_location() ), true );
	}

	private function get_current_theme_manifest_location () {	
		$template = trailingslashit( get_template_directory() );
		if (file_exists( $template . 'manifest.json' )) {
			return $template . 'manifest.json';
		}
		return trailingslashit( PL_PARENT_URL ) . 'config/default-manifest.json';
	}

	private function is_placester_theme () {
		global $i_am_a_placester_theme;
		if ($i_am_a_placester_theme) {
			return true;
		}
		return false;
	}

}