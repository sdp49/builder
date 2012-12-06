<?php

PL_Posts::init();

class PL_Posts {
  
  function init () {
    self::register_dummy_data_post_status();
  }

  public function create ( $manifest, $settings ) {
    
    // get default-settings min-posts value.
    $min_posts = $settings['min_posts'];
    
    // get existing posts... get_existing_posts() function
    $posts = get_posts();
    $existing_post_count = count($posts);

    $use_manifest = '';

    // if # of existing posts > min-posts use dummy data from manifest
    if ($existing_post_count <= $min_posts) {
      $posts = $manifest['posts'];
      $use_manifest = true;
      // append special mark of posts to show their custom
        // function => register_dummy_data_post_status();
    }
    
    foreach ($posts as $post) {
      # code...
    }
    var_dump($posts);

  }







  private function register_dummy_data_post_status() {
    // http://codex.wordpress.org/Function_Reference/register_post_status
    // register_post_status("Dummy Data", array(
    //   'exclude_from_search' => true,
    //   'show_in_admin_all_list' => true,
    //   'show_in_admin_all' => true,
    //   'single_view_cap' => true,
    //   'label' => "Dummy Data",
    //   'public' => true
    //   )
    // );
  }
}