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
	private static $rewrite_rules = array(
		'property/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]*)/([^/]+)/?$' => 'index.php?pls_page=property&property=$matches[6]',
	);
	private static $flush_rules = false;
	public static $listing_details = null;



	public static function init () {
		add_action( 'init', array(__CLASS__, 'setup_rewrite') );
		add_filter( 'pre_get_posts', array(__CLASS__, 'detect_virtual_pages') );
		add_filter( 'query_vars', array(__CLASS__, 'setup_url_vars') );
		add_filter( 'the_posts', array(__CLASS__, 'the_posts') );
		add_filter( 'post_link', array(__CLASS__, 'get_property_permalink'), 10, 3);
				
		add_action( 'wp_footer', array(__CLASS__,'force_rewrite_update') );
		add_action( 'admin_footer', array(__CLASS__,'force_rewrite_update') );
		add_action( '404_template', array( __CLASS__, 'dump_permalinks') );
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
		        	delete_post_meta( $page->ID, '_wp_page_template' );
		        	add_post_meta( $page->ID, '_wp_page_template', get_template_directory_uri().'/'.$page_info['template']);
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
	 * Deletes all properties and their associated post meta.
	 *
	 * @return bool true if delete successful
	 */
	public static function delete_all () {
		global $wpdb;

		$q_ids = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s", self::$property_post_type);
		$prop_ids = $wpdb->get_col($q_ids);

		if (!is_array($prop_ids) || count($prop_ids) === 0) {
			return false;
		}

		$id_str = implode(',', $prop_ids);
    	$results = $wpdb->query( "DELETE FROM $wpdb->posts WHERE ID IN ($id_str)");

    	if (empty($results)) {
    		return false;
    	}

		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id IN ($id_str)");

		// NOTE: This call produces highly negative side-effects, as neighborhood meta-info created by clients
		// relies on the related term and taxonomy to exist, even after clearing properties...re-evaluate ASAP!
		//
		// self::delete_all_terms();

		self::ping_yoast_sitemap();

    	return true;
	}

	/**
	 * Given a name (property id), deletes the corresponding WP post and all associated data
	 *
	 * @param  string $name post_name (property id)
	 * @return bool 	true if successful
	 */
	public static function delete_by_name ($name) {
		global $wpdb;

		if (!$name) {
			return false;
		}

		$q_id = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = %s", $name, self::$property_post_type);
		$post_id_arr = $wpdb->get_col($q_id);

		if (!is_array($post_id_arr) || count($post_id_arr) === 0) {
			return false;
		}

		$post_id = $post_id_arr[0];
		$result = (bool) wp_delete_post($post_id, true);

		if ($result) {
			self::ping_yoast_sitemap();
		}

		return $result;
	}

	/**
	 * Deletes all terms (and their relationships) associated with Property taxonomies.
	 *
	 * @todo can we prompt Yoast to rebuild its sitemap?
	 *
	 * NOTE: Decomissioned until further evaluation -- see note in the call to this function inside of "delete_all"
	 */
/*
	public static function delete_all_terms() {
		global $wpdb;

		$args = array(
			'hide_empty' => false,
			'fields' => 'ids'
		);

		$all_terms = get_terms( self::$all_taxonomies, $args );

		if (!is_array($all_terms) || count($all_terms) === 0) {
			return;
		}

		$term_str = implode(',', $all_terms);
		$wpdb->query("DELETE FROM $wpdb->terms WHERE term_id IN ($term_str)");
		$term_tax_ids = $wpdb->get_col("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id IN ($term_str)");

		if (!is_array($term_tax_ids) || count($term_tax_ids) === 0) {
			return;
		}

		$term_tax_str = implode(',', $term_tax_ids);
		$wpdb->query("DELETE FROM $wpdb->term_taxonomy WHERE term_taxonomy_id IN ($term_tax_str)");
		$q_term_rel = $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ($term_tax_str)");
	}
*/

	/**
	 * Load rules
	 */
	function setup_rewrite(){

		//register_post_type(self::$property_post_type, array('labels' => array('name' => __( 'Properties' ),'singular_name' => __( 'property' )),'public' => true,'has_archive' => true, 'rewrite' => true, 'query_var' => true, 'taxonomies' => array('category', 'post_tag')));

		$rules = get_option('rewrite_rules');
		foreach(self::$rewrite_rules as $rule=>$rewrite) {
			add_rewrite_rule($rule, $rewrite, 'top');
		}
	}

	/**
	 * Setup wp_query values to detect parameters
	 */
	public function setup_url_vars( $vars )	{
		array_push($vars, 'pls_page');
		array_push($vars, 'property');

		return $vars;
	}

	/**
	 * Fetch listing details if this is a details page
	 */
	public function detect_virtual_pages( $query ) {
		if (!empty($query->query_vars['pls_page'])) {
			switch($query->query_vars['pls_page']) {
				case 'property':
					if (!empty($query->query_vars['property'])) {
						$args = array('listing_ids' => array($query->query_vars['property']));
						$response = PL_Listing::get($args);

						if (!empty($response['listings'][0])) {
							$query->set('post_type', self::$property_post_type);
							self::$listing_details = $response['listings'][0];
							break;
						}
					}
					$query->is_404 = true;
					break;
			}
		}
	}

	/**
	 * If details page and have a listing, make a dummy post
	 */
	public function the_posts( $posts ) {
		global $wp, $wp_query;

		if (!empty($wp_query->query_vars['pls_page'])) {
			if ($wp_query->query_vars['pls_page'] == 'property') {
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
		}

		return $posts;
	}

	/**
	 * Build a permalink for a property page
	 * Handles when we have a dummy property post object - normally only when viewing a property details page 
	 */
	public static function get_property_permalink ($permalink, $post, $leavename) {
		if ( !empty($permalink) && is_object($post) && $post->post_type == 'property' && !empty($post->post_name) && !in_array($post->post_status, array('draft', 'pending', 'auto-draft')) ) {
			if (!empty(self::$listing_details) && self::$listing_details['id']==$post->post_name) {
				return PL_Page_Helper::get_url($post->post_name, self::$listing_details);
			}
			else {
				$args = array('listing_ids' => array($query->query_vars['property']));
				$response = PL_Listing::get($args);
				if (!empty($response['listings'][0])) {
					return PL_Page_Helper::get_url($post->post_name, $response['listings'][0]);
				}
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

				self::$flush_rules = true;
			}
		}

		if (self::$flush_rules) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();

			PL_Cache::invalidate();

			// self::delete_all();
		}
	}

	/**
	 * Flush rewrites - maybe after we get a 404
	 */
	public static function dump_permalinks () {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/**
	 * If Yoast sitemaps are enabled, causes Yoast to request the sitemap (populating caches)
	 * and to request that search engines re-index the site.
	 */
	public static function ping_yoast_sitemap() {
		global $wpseo_sitemaps;

		if (!$wpseo_sitemaps) {
			$path = WP_PLUGIN_DIR . '/wordpress-seo/inc/class-sitemaps.php';
			if (file_exists($path)) {
				require_once $path;
				$wpseo_sitemaps = new WPSEO_Sitemaps();
			} else {
				return;
			}
		}

		if (method_exists($wpseo_sitemaps, 'hit_sitemap_index')) {
			$wpseo_sitemaps->hit_sitemap_index();
		}

		if (method_exists($wpseo_sitemaps, 'ping_search_engines')) {
			$wpseo_sitemaps->ping_search_engines();
		}
	}
}