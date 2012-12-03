<?php 

class PL_Menus {
  
  // init
  function init () {
    
  }
  
  // get current nav menus
  function get_current () {
    global $wpdb;
    $sql = $wpdb->prepare('SELECT * ' . 'FROM ' . $wpdb->prefix . 'posts ' . "WHERE post_type = 'nav_menu_item'");
    $rows = $wpdb->get_results($sql, ARRAY_A);
    return $rows;
  }

  function dynamic ($menus) {

    $current_menus = self::get_current();
    // wp_create_nav_menu('vinter');
    foreach ($menus as $menu) {
      // self::create_menu( $menu['name'] );
      var_dump( "IN" );
      if (function_exists('wp_get_nav_menu_object')) {
        var_dump( "yep - it exists" );
      }
      wp_get_nav_menu_object('hellow', false);
      



    //   wp_update_nav_menu_item($menu['name'], 0, array(
    //           'menu-item-title' =>  __($menu['name']),
    //           'menu-item-classes' => $menu['name'],
    //           'menu-item-url' => home_url( '/' ), 
    //           'menu-item-status' => 'publish'));
    //   
    }

  }

  // $defaults = array('name' => '', 'location' => '', 'pages' => array() );
  // extract(wp_parse_args($menus, $defaults));


  // create
  // function create_menu ( $menu_name ) {
  //   var_dump($menu_name);
  //   $menu = wp_get_nav_menu_object( $menu_name );
  //   var_dump($menu);
  // }
  
  // update

// delete


}

// Returns boolean Whether a registered nav menu location has a menu assigned(true) or not(false).
// if ( has_nav_menu( $location ) ) {
     //Do something
// }