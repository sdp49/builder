<?php

PL_Posts::init();

class PL_Posts {
  
  function init () {
    self::register_dummy_data_post_status();
  }

  public function create ( $manifest_posts, $post_type, $settings ) {

    // get existing posts... get_existing_posts() function
    $posts = get_posts( array(
        'showposts' => 50,
        'post_type' => $post_type,
        ));
    
    $existing_post_count = count($posts);

    $use_manifest = '';

    // if # of existing posts > min-posts use dummy data from manifest
    if ($existing_post_count <= $settings['min_posts']) {
      $posts = $manifest_posts;
      
      $use_manifest = true;
      // append special mark of posts to show their custom
        // function => register_dummy_data_post_status();
        
    }

    // Add new posts if they don't already exist
    foreach ($posts as $post) {
      $post_array = (array) $post;
      
      $found_post = get_page_by_title($post['post_title'], ARRAY_A, $post_type);
      if (empty($found_post)) {
        wp_insert_post($post_array);
      }
    }
    // var_dump($posts);
    
    // NEED TO ADD POST ATTRIBUTES (IE. CATEGORIES, TAGS, ETC)
    
    // NEED TO ADD META DATA (IE. AGENT EMAIL/PHONE, TESTIMONIAL LOCATION)
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