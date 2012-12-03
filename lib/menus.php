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
    
    foreach ($menus as $menu) {

      // check if $menu exists
      $menu_check = wp_get_nav_menu_object( $menu['name'] );

      // If menu doesn't exist, create it
      if ( empty($menu_check) ) {
        wp_update_nav_menu_object( 0, array(
          'menu-name' => $menu['name'])
        );
      }
      
      // if it already exists, ask user if we can delete
      
        // if user says Go, then Go
      
      
      
      // once menu is create, add_pages_to_menu_by_name()
      
      // assign_menu_to_theme_location ()
      
    }

  }

  function add_pages_to_menu_by_name ($menu) {
    
    // if page already exists, skip
    
  }

  function assign_menu_to_theme_location ($menu) {
    
    // Returns boolean Whether a registered nav menu location has a menu assigned(true) or not(false).
    // if ( has_nav_menu( $location ) ) {
         //Do something
    // }
    
  }


}