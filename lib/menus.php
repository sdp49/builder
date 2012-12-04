<?php 

class PL_Menus {
  
  // init
  function init () {
    
  }

  function create( $menus, $theme_locations, $menu_overides = false ) {
    
    // get currently enabled "theme locations" (default is "primary" and "subsidiary")
    // $current_theme_locations = array_keys(get_theme_mod( 'nav_menu_locations' ));

    // Make plans for theme locations if they aren't standard
    if ($theme_locations != $current_theme_locations ) {
      // $current_theme_locations....
    }

    // Get All current menus
    $all_menus = wp_get_nav_menus();
    
    $conflicting_menus = array();
    $menus_to_create = array();
    
    // Check manifest menus against current menus
    foreach ($menus as $menu) {

      // check if $menu exists
      $menu_check = wp_get_nav_menu_object( $menu['name'] );

      if ( empty($menu_check) ) {
          // If menu doesn't exist, create it
          wp_update_nav_menu_object( 0, array(
            'menu-name' => $menu['name'])
          );
          
          self::add_pages_to_menu_by_name($menu);
          self::assign_menu_to_theme_location($menu, $theme_locations);
          
      } else {
          // if it already exists, add to array to ask user if we can delete them
          $conflicting_menus .= $menu;
          self::add_pages_to_menu_by_name($menu, true);
          // add override menu data
          if ($menu_overrides != false) {
            self::assign_menu_to_theme_location($menu, $theme_locations, $menu_overrides);
          }
      }
      
    }

    // if conflicting menus exist, resolve by asking user
    if ( !empty($conflicting_menus) ) {
      
    }
    
    // if user says Go, then add menus to $menus_to_create

    
    // once menu is create, add_pages_to_menu_by_name()
    
    
    
  }

  function add_pages_to_menu_by_name ($menu, $menu_exist = false, $menu_overrides = false ) {

    // get menu object
    $the_menu = wp_get_nav_menu_object($menu['name']);
    $menu_id = (int) $the_menu->term_id;
    
    if ($menu_exist == true) {
      
      $main_nav = wp_update_nav_menu_item($menu['name']);
      // var_dump($main_nav);
      
      
      
    } else {
    
        foreach ($menu['pages'] as $page) {

        
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

  function assign_menu_to_theme_location ($menu, $theme_locations, $menu_overrides = false ) {
    
    // end process if requested location doesn't exists
    $location_check = in_array($menu['location'], $theme_locations);
    if ($location_check == false) {
      return false;
    }
    
    // Theme Locations for menus that are in theme
    $locations = get_nav_menu_locations();
    $final_them_locations = array();
    foreach ($locations as $location => $location_id) {
      // check for locations having menus set to them
      $location_has_nav_menu = has_nav_menu($name);

      if ($location_has_nav_menu == false) {
        // if menu location is empty, set it from manifest
        if ($menu['location'] == $location) {
          
        }
      } else {
        
      }
      
    }
    // $menu_slug = 'top-menu';
    // $locations = get_nav_menu_locations();
    // 
    // if (isset($locations[$args->theme_location])) {
    //     $menu_id = $locations[$args->theme_location];
    // }
    var_dump($menu);
    // HERE! SO CLOSE!
    // set_theme_mod( 'nav_menu_locations', array_map( 'absint', $_POST['menu-locations'] ) );
    // set_theme_mod( 'nav_menu_locations', $final_theme_locations );
  }


}