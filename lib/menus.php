<?php 

class PL_Menus {
  
  // init
  function init () {
    
  }

  function create( $menus, $theme_locations ) {
    
    // get currently enabled "theme locations" (default is "primary" and "subsidiary")
    $current_theme_locations = array_keys(get_theme_mod( 'nav_menu_locations' ));

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
          
          
      } else {
          // if it already exists, add to array to ask user if we can delete them
          $conflicting_menus .= $menu;
      }
      
    }

    // if conflicting menus exist, resolve by asking user
    if ( !empty($conflicting_menus) ) {
      
    }
    
    // if user says Go, then add menus to $menus_to_create

    
    // once menu is create, add_pages_to_menu_by_name()
    
    // assign_menu_to_theme_location ()
    
  }

  function add_pages_to_menu_by_name ($menu) {
    
    foreach ($menu['pages'] as $page) {
    
    var_dump("add_pages_to_menu is firing");

      wp_update_nav_menu_item($menu_id, 0, array(
              'menu-item-title' =>  __($menu['pages']),
              // 'menu-item-classes' => 'forums',
              // 'menu-item-url' => home_url( '/forums/' ), 
              'menu-item-status' => 'publish'));

    }
    
    // Insert new page 
    // $page = wp_insert_post(array(
    //   'post_title' => 'Blog', 
    //   'post_content' => '', 
    //   'post_status' => 'publish', 
    //   'post_type' => 'page')); 

    // Insert new nav_menu_item 
    // $nav_item = wp_insert_post(array(
    //   'post_title' => 'News', 
    //   'post_content' => '', 
    //   'post_status' => 'publish', 
    //   'post_type' => 'nav_menu_item'));
  }

  function assign_menu_to_theme_location ($menu) {
    
    // Returns boolean Whether a registered nav menu location has a menu assigned(true) or not(false).
    // if ( has_nav_menu( $location ) ) {
         //Do something
    // }
    
  }


}