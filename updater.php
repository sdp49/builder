<?php
/**
 * Update anything that needs to be updated from a previous version.
 * Runs each successive version to bring current version up to date:
 * Add function for the previous version if there has been a change that needs updating.
 * TODO: move the notifications to a common helper.
 */

PL_Updater::init();

class PL_Updater {
	
	private static $opt = 'placester_update_notices';
			
	static function init() {
		$prev_ver = get_option('pl_plugin_version', PL_PLUGIN_VERSION);
		if ($prev_ver != PL_PLUGIN_VERSION) {
			add_action('admin_notices', array('PL_Updater', 'admin_notices'));
			$updates = get_class_methods('PL_Updater');
			$updates = preg_replace(array('/^_/','/([0-9]+)_/'), array('', '$1.'), $updates);
			usort($updates, 'version_compare');
			$notices = get_option(self::$opt, array());
    		foreach($updates as $update) {
				if (!is_numeric(substr($update,1,2)) || version_compare($prev_ver, $update)>=0) continue;
				$func = 'PL_Updater::_'.str_replace('.', '_', $update);
				call_user_func($func);
		    	$notices[]= "Upgraded data to version $update";
    		}
		    update_option(self::$opt, $notices);
		}
	}

	public static function compare($a, $b) {
		return version_compare(substr(str_replace('_', '.', $a),1), substr(str_replace('_', '.', $b),1));
	}
	
	public static function admin_notices() {
		if ($notices = get_option(self::$opt)) {
			$plugin = get_plugin_data(trailingslashit(PL_PARENT_DIR).'placester.php');
			echo '<div class="updated"><p><em>'.$plugin['Name'].'</em> plugin:</p>';
			foreach ($notices as $notice) {
				echo "<p>$notice</p>";
			}
			echo '</div>';
			delete_option(self::$opt);
		}
	}

	private static function _1_1_9() {
		global $wpdb;
		
		// update old shortcode templates
		$template_opts = array('pls_search_map','pls_search_form','pls_search_listings','pls_pl_neighborhood','pls_listing_slideshow','pls_featured_listings','pls_static_listings');
		foreach($template_opts as $template_opt) {
			$query = "SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE '".$template_opt."_%'";
			$results = $wpdb->get_results($query);
			$fields = array('before_widget'=>'', 'after_widget'=>'', 'snippet_body'=>'', 'widget_css'=>'');
			$list = array();
			foreach($results as $result) {
				$matches = array();
				if (preg_match('/^'.$template_opt.'_((?!list$)(.+))$/', $result->option_name, $matches)) {
					$val = get_option($result->option_name, '');
					if (!is_array($val)) {
						$val = array('snippet_body'=>$result->option_value);
						$val = array_merge($fields, $val);
						$query = "UPDATE ".$wpdb->prefix."options
							SET option_name='".$template_opt."__".$matches[1]."',
								option_value='".serialize($val)."'
							WHERE option_name='".$result->option_name."'";
						$results = $wpdb->get_results($query);
					}
					else {
						// probably been updated already
						if (strpos($matches[1], '_')===0) {
							$matches[1] = substr($matches[1], 1);
						}
					}
					$list[] = $matches[1];
				}
			}
			update_option($template_opt.'_list', $list);
		}
	}
}
