<?php 

class PL_Menus {
  
  // init
  function init () {
    
  }

  function create( $manifest_menus ) {
    
    // get currently enabled "theme locations" (default is "primary" and "subsidiary")
    // $current_theme_locations = array_keys(get_theme_mod( 'nav_menu_locations' ));

    // Make plans for theme locations if they aren't standard
    // if ($theme_locations != $current_theme_locations ) {
      // $current_theme_locations....
    // }

    // Get All current menus
    // $all_menus = wp_get_nav_menus();
    
    // $conflicting_menus = array();
    // $menus_to_create = array();
    
    // Check manifest menus against current menus
    foreach ($manifest_menus as $manifest_menu) {
        // create menus
        self::create_new_menu($manifest_menu['name']);
        // add pages
        self::add_pages_to_menu($manifest_menu);
    }

    self::assign_menu_to_theme_location( $manifest_menus );


    // if conflicting menus exist, resolve by asking user
    // if ( !empty($conflicting_menus) ) {}
    // if user says Go, then add menus to $menus_to_create
    // once menu is create, add_pages_to_menu_by_name()
  }




  function assign_menu_to_theme_location ( $manifest_menus ) {
    
    // Get Menu Locations
    $menu_theme_locations = get_nav_menu_locations();
    // Get Current Menus
    $all_current_menus = wp_get_nav_menus();
    
    // Try to respect each manifest menu
    foreach ($manifest_menus as $manifest_menu) {
      // this manifest menu's real ID if it already exists
      $menu_id = get_term_by( 'name', $manifest_menu['name'], 'nav_menu' );
      // pls_dump("menu id:",$menu_id);
      // loop through theme's menu locations to find matching $manifest_menu's location
      foreach ($menu_theme_locations as $menu_theme_location => $value) {
        // pls_dump("menu theme location:",$menu_theme_location);
        // when matching location is found in manifest, set existing menu's term_id to the theme location
        if ($manifest_menu['location'] == $menu_theme_location) {
          $menu_theme_locations[$menu_theme_location] = $menu_id->term_id;
        }
      }
    }
    
    $new_locations = array();
    
    foreach ($menu_theme_locations as $location => $value) {
      if (is_numeric($value)) {
        $new_locations[$location] = $value;
      } else {
        $new_locations[$location] = 0;
      }
    }
    // pls_dump($new_locations);
    
    // Set menus to theme locations
    set_theme_mod( 'nav_menu_locations', $new_locations );

  }


  function create_new_menu ( $menu_name ) {
    // check if $menu exists
    $menu_check = wp_get_nav_menu_object( $menu_name );
    // pls_dump($menu_check);
    // if menu doesn't already exist, make it
    if ( !empty($menu_check) ) {
      return false;
    }
    
    // Create the menu
    wp_update_nav_menu_object( 0, array( 'menu-name' => $menu_name) );
  }


  function add_pages_to_menu ( $manifest_menu ) {
    
    // get the menu object
    $the_menu = wp_get_nav_menu_object($manifest_menu['name']);
    $menu_id = (int) $the_menu->term_id;
    $the_menu_pages = wp_get_nav_menu_items($menu_id);
    $existing_menu_pages = array();
    foreach ($the_menu_pages as $page) {
      $existing_menu_pages[] = $page->post_title;
    }
    
    // add manifest pages to menu obejct
    foreach ($manifest_menu['pages'] as $page) {
        // don't allow duplicates
        if (in_array($page, $existing_menu_pages)) {
          continue;
        }
        
        $the_page = get_page_by_title($page);
        
        $args =  array(
            'menu-item-object-id' => $the_page->ID,
            // 'menu-item-parent-id' => 0,
            // 'menu-item-position'  => 2,
            // 'menu-item-object'    => 'page',
            // 'menu-item-type'      => 'post_type',
            'menu-item-title'     => $the_page->post_title,
            'menu-item-classes'   => $the_page->post_title,
            'menu-item-url'       => $the_page->guid,
            'menu-item-status'    => 'publish'
          );
        wp_update_nav_menu_item( $menu_id, 0, $args );
    }
    
  }

}