<?php

PL_Pages::init();
/**
 * @todo expose list of taxonomies in helpers/taxonomy.php and get list from there instead
 */
class PL_Pages {

	public static $property_post_type = 'property';
	private static $all_taxonomies = array(
		'state',
		'zip',
		'city',
		'neighborhood',
		'street',
		'beds',
		'baths',
		'half-baths',
		'mlsid'
	);
	private static $listing_details = null;
	private static $taxonomy_object = null;

	public static function init () {
		// NOTE: The three filters/hooks below are hit on each request in the order they are listed -- 
		// these are the crux of how virtual property CPTs and tax pages are rendered...
		add_action('init', array(__CLASS__, 'setup_rewrite'));
		add_filter('pre_get_posts', array(__CLASS__, 'detect_virtual_pages'));
		add_filter('the_posts', array(__CLASS__, 'the_posts'));

		add_filter('post_type_link', array(__CLASS__, 'get_property_permalink'), 10, 3);

		add_action('wp_footer', array(__CLASS__,'force_rewrite_update'));
		add_action('admin_footer', array(__CLASS__,'force_rewrite_update'));
		add_action('404_template', array(__CLASS__, 'dump_permalinks'));
	}

	public static function create_once ($pages_to_create, $force_template = true) {
		foreach ($pages_to_create as $page_info) {
			$page = get_page_by_title($page_info['title']);
			if (!isset($page->ID)) {
				$page_details = array();
				$page_details['title'] = $page_info['title'];
				if (isset($page_info['template'])) {
          			$page_details['post_meta'] = array('_wp_page_template' => $page_info['template']);
				}
				if (isset($page_info['content'])) {
          			$page_details['content'] = $page_info['content'];
				}

        		self::manage($page_details);
			}
			elseif ($force_template) {
		        if (isset($page_info['template'])) {
		        	delete_post_meta($page->ID, '_wp_page_template');
		        	add_post_meta($page->ID, '_wp_page_template', get_template_directory_uri().'/'.$page_info['template']);
		        }
			}
		}
	}

	//create page
	public static function manage ($args = array()) {
		$defaults = array('post_id' => false, 'type' => 'page', 'title' => '', 'name' => false, 'content' => ' ', 'status' => 'publish', 'post_meta' => array(), 'taxonomies' => array());
		extract(wp_parse_args($args, $defaults));

		$post = array(
			'post_type'   => $type,
			'post_title'  => $title,
			'post_name'   => $name,
			'post_status' => $status,
			'post_author' => 1,
			'post_content'=> $content,
			'filter'      => 'db',
			'guid'        => @$guid
		);

		if ($post_id <= 0) {
			$post_id = wp_insert_post($post);

			if (!empty($post_meta)) {
				foreach ($post_meta as $key => $value) {
					add_post_meta($post_id, $key, $value, TRUE);
				}
			}

			if (!empty($taxonomies)) {
				foreach ($taxonomies as $taxonomy => $term) {
					wp_set_object_terms($post_id, $term, $taxonomy);
				}
			}
		}
		else {
			$post['ID'] = $post_id;
			$post_id = wp_update_post($post);
		}

        return $post_id;
	}

	/**
	 * Load rules
	 */
	public static function setup_rewrite(){
		// do not make public or Yoast will create sitemaps - we are making our own elsewhere
		$cpt_args = array(
			'labels'=> array('name'=>__('Properties'), 'singular_name'=>__('property')),
			'public'=> false,
			'has_archive' => true,
			'rewrite' => true,
			'query_var' => true,
			'taxonomies' => array(),
			'exclude_from_search' => true,
			'publicly_queryable'=> false
		);

		register_post_type(self::$property_post_type, $cpt_args);

		// Traditional property page URL structure...
		add_rewrite_rule('property/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]+)/?$', 'index.php?property=$matches[6]', 'top');
		
		// Alternative property page URL format...
		add_rewrite_rule('property/([^/]+)/?$', 'index.php?property=$matches[1]', 'top');
	}

	/**
	 * Fetch listing details if this is a details page
	 */
	public static function detect_virtual_pages($query) {
		// If the URL matched either of the rewrite rules listed in "setup_rewrite", then a "property" query variable will be set...
		if (!empty($query->query_vars['property'])) {
			$args = array('listing_ids' => array($query->query_vars['property']));

			// Respect address_mode type -- exact, or block adresses (i.e., 'polygon')
			$args['address_mode'] = ( PL_Option_Helper::get_block_address() ? 'polygon' : 'exact' );

			$response = PL_Listing::get($args);
			if (!empty($response['listings'][0])) {
				$query->set('post_type', self::$property_post_type);
				self::$listing_details = $response['listings'][0];
			}
			else {
				$query->set_404();
			}
		}
	}

	/**
	 * When we get here should have some object to display, so create something if necessary
	 */
	public function the_posts($posts) {
		global $wp, $wp_query;

		if (!empty($wp_query->query_vars['property'])) {
			// If details page and have a listing, make a dummy post
			if (self::$listing_details) {
				// Creating a property page by creating a fake post instance
				$post = new stdClass;
				// fill properties of $post with everything a page in the database would have
				$post->ID = -1;						// use an illegal value for page ID
				$post->post_author = 1;				// post author id
				$post->post_date = null;			// date of post
				$post->post_date_gmt = null;
				$post->post_content = '';
				$post->post_title = self::$listing_details['location']['address'];
				$post->post_excerpt = '';
				$post->post_status = 'publish';
				$post->comment_status = 'closed';	// mark as closed for comments, since page doesn't exist
				$post->ping_status = 'closed';		// mark as closed for pings, since page doesn't exist
				$post->post_password = '';			// no password
				$post->post_name = self::$listing_details['id'];
				$post->to_ping = '';
				$post->pinged = '';
				$post->modified = $post->post_date;
				$post->modified_gmt = $post->post_date_gmt;
				$post->post_content_filtered = '';
				$post->post_parent = 0;
				$post->guid = null;
				$post->menu_order = 0;
				$post->post_style = '';
				$post->post_type = 'property';
				$post->post_mime_type = '';
				$post->comment_count = 0;

				// set filter results
				$posts = array($post);

				// reset wp_query properties to simulate a found page
				$wp_query->is_page = true;
				$wp_query->is_singular = true;
				$wp_query->is_single = true;
				$wp_query->is_home = false;
				$wp_query->is_archive = false;
				$wp_query->is_category = false;
				unset($wp_query->query['error']);
				$wp_query->query_vars['error'] = '';
				$wp_query->is_404 = false;
			}
		}
		elseif (!empty($wp_query->query_vars['taxonomy']) && !empty($wp_query->query_vars[$wp_query->query_vars['taxonomy']])
			&& (!empty($wp_query->query_vars['neighborhood']) || !empty($wp_query->query_vars['zip']) || !empty($wp_query->query_vars['city']) || !empty($wp_query->query_vars['state']))) {
			// Fetch needed vars..
			$tax = $wp_query->query_vars['taxonomy'];
			$locs = PL_Taxonomy_Helper::get_tax_loc_map();
			$loc = isset($locs[$tax]) ? $locs[$tax] : $tax; 
			$slug = self::format_url_slug($wp_query->query_vars[$tax]);

			// Attempt to fetch the inferred 'queried object'...
			$qo = $wp_query->get_queried_object();

			// Create a term object if we don't have anything saved for this location...
			if (!is_object($qo)) {
				$response = PL_Listing::locations();
				
				// Check if this area is a valid member of the list of known locations...
				if (!empty($response[$loc])) {
					$key = array_search( $slug, array_map( array(__CLASS__, 'format_url_slug'), $response[$loc] ) );
					if ($key !== false) {
						// Try to fetch a related area page...
						$area_page = self::check_for_area_page($loc, $slug);

						// If a matching area page exists, return that single area CPT so that its content is what gets rendered...
						if (!empty($area_page)) {
							return array($area_page);
						}
						
						// Create a virtual taxonomy page instead...
						$qo = new stdClass();
						$qo->term_id = -1;
						$qo->name = $response[$loc][$key];
						$qo->disp_name = self::format_taxonomy_name($response[$loc][$key]);
						$qo->slug = $slug;
						$qo->term_group = 0;
						$qo->term_taxonomy_id = -1;
						$qo->taxonomy = $tax;
						$qo->description = '';
						$qo->parent = 0;
						$qo->count = 1;
						$wp_query->$tax = $slug;
						$wp_query->queried_object = $qo;
						$wp_query->queried_object_id = -1;
						self::$taxonomy_object = $qo;
					}
				}
			}
			// Even when we have a valid term object, check to see if it correlates to an area page and handle accordingly...
			else {
				// Try to fetch a related area page...
				$area_page = self::check_for_area_page($loc, $slug);

				// Set the posts array to the single, matching area CPT so that it fetches that content instead...
				if (!empty($area_page)) {
					$posts = array($area_page);
				}
			}
		}

		return $posts;
	}

	public static function format_url_slug($slug) {
		$slug = str_replace(':', '-', $slug);
		return sanitize_title_with_dashes($slug);
	}

	public static function format_taxonomy_name($name) {
		if ( strpos($name, ':') !== false) {
			$name_segments = explode(':', $name);
			$name = trim($name_segments[0]);
		}
		return $name;
	}

	public static function get_listing_details() {
		return self::$listing_details;
	}

	public static function get_taxonomy_object() {
		return self::$taxonomy_object;
	}

	public static function check_for_area_page($loc, $slug) {
		global $wp_query;

		// Default the value returned to null, unless we find a matching area page...
		$matched_area_page = null;

		// Fetch area pages that match the passed location type...
		$area_pages = get_posts(
			array(
				'post_type' => 'area', 
				'meta_key' => 'area_type', 
				'meta_value' => $loc
			)
		);
		
		// Iterate through the matched area pages to check and see if any match the location page trying to be accessed...
		foreach($area_pages as $area_page) {
			// Fetch the area page's name (it's stored as a postmeta value)...
			$location = get_post_custom_values('area_name', $area_page->ID);
			
			if (self::format_url_slug($location[0]) == $slug) {
				// Nullify existing query object, in case one exists...
				$wp_query->queried_object = null;

				// Add post_type field to the post itself, as it doesn't exist in the query_vars...
				$area_page->post_type = 'area';

				// Set the posts array to this single area CPT so that it fetches that content...
				$matched_area_page = $area_page;

				$wp_query->is_singular = true;
				$wp_query->is_single = true;
				$wp_query->is_home = false;
				$wp_query->is_archive = false;
				$wp_query->is_category = false;
				$wp_query->is_404 = false;
				$wp_query->is_tax = false;
				$wp_query->tax_query = null;
				$wp_query->query = null;
				$wp_query->request = null;
			}
		}
		
		return $matched_area_page;
	}

	/**
	 * Provide template for property details page URLs
	 */
	public static function get_link_template () {
		$permalink_struct = get_option('permalink_structure');
		if (empty($permalink_struct)) {
			// non pretty format
			$link = '?property=%id%';
		}
		else {
			$link = "/property/%region%/%locality%/%postal%/%neighborhood%/%address%/%id%/";
		}
		return home_url($link);
	}

	/**
	 * Create a pretty link for property details page
	 */
	public static function get_url ($placester_id, $listing = array()) {
		$default = array(
				'region' => 'region',
				'locality' => 'locality',
				'postal' => 'postal',
				'neighborhood' => 'neighborhood',
				'address' => 'address',
				'id' => ''
		);
		$listing = wp_parse_args($listing, array('location' => $default));
		$listing = $listing['location'];
		$listing['id'] = $placester_id;
		// not using get_permalink because it's a virtual page
		$url = self::get_link_template();

		$tmpl_replace = $tmpl_keys = array();
		foreach ($default as $key=>$val) {
			$tmpl_replace[] = empty($listing[$key]) ? '-' : preg_replace('/[^a-z0-9\-]+/', '-', strtolower($listing[$key]));
			$tmpl_keys[] = '%'.$key.'%';
		}
		$url = str_replace($tmpl_keys, $tmpl_replace, $url);

		return $url;
	}

	/**
	 * Build a permalink for a property page
	 * Handles when we have a dummy property post object - normally only when viewing a property details page
	 */
	public static function get_property_permalink ($permalink, $post, $leavename) {
		if (!empty($permalink) && is_object($post) && $post->post_type == 'property' && !empty($post->post_name) && !in_array($post->post_status, array('draft', 'pending', 'auto-draft'))) {
			if (!empty(self::$listing_details) && self::$listing_details['id']==$post->post_name) {
				// viewing virtual details page
				return self::get_url($post->post_name, self::$listing_details);
			}
		}
		return $permalink;
	}

	/**
	 * Plugin version change - run updates, flush rewrites, etc
	 */
	public static function force_rewrite_update () {
		if (defined('PL_PLUGIN_VERSION')) {
			$current_version = get_option('pl_plugin_version');
			if ($current_version != PL_PLUGIN_VERSION) {
				// Run the updater script before updating the version number...
				include_once(trailingslashit(PL_PARENT_DIR) . 'updater.php');

				// Update version in DB
				update_option('pl_plugin_version', PL_PLUGIN_VERSION);

				global $wp_rewrite;
				$wp_rewrite->flush_rules();

				PL_Cache::invalidate();
			}
		}
	}

	/**
	 * Flush rewrites - maybe after we get a 404
	 */
	public static function dump_permalinks () {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}
	
}