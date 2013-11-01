<?php

PL_Sitemaps::init();

class PL_Sitemaps {

	private static $max_entries = 500;

	public static function init() {
		add_filter('wpseo_sitemap_index', array(__CLASS__, 'append_sub_sitemaps'));
		add_action('init', array(__CLASS__, 'register_sitemaps'));
	}


	public static function append_sub_sitemaps($sitemap_list) {
		$base = $GLOBALS['wp_rewrite']->using_index_permalinks() ? 'index.php/' : '';
		$date = date('c');

		// property pages
		$response = PL_Listing::get();
		if (!empty($response['total'])) {
			$count = $response['total'];
			$n = ( $count > self::$max_entries ) ? (int) ceil( $count / self::$max_entries ) : 1;
			for ( $i = 0; $i < $n; $i++ ) {
				$count = ( $n > 1 ) ? $i + 1 : '';
				$sitemap_list .= self::format_sub_sitemap_entry($base, $date, 'property-details', $count);
			}
		}

		return $sitemap_list;
	}

	private static function format_sub_sitemap_entry($base, $date, $type, $count='') {
		$sitemap_list = '<sitemap>' . "\n";
		$sitemap_list .= '<loc>' . home_url( $base . $type . '-sitemap' . $count . '.xml' ) . '</loc>' . "\n";
		$sitemap_list .= '<lastmod>' . htmlspecialchars( $date ) . '</lastmod>' . "\n";
		$sitemap_list .= '</sitemap>' . "\n";
		return $sitemap_list;
	}

	public static function register_sitemaps() {
		global $wpseo_sitemaps;

		if ($wpseo_sitemaps) {
			$wpseo_sitemaps->register_sitemap('property-details', array(__CLASS__, 'property_details_sitemap'));
		}
	}

	public static function property_details_sitemap() {
		global $wpseo_sitemaps;

		$date = date( 'c' );
		$site_url = site_url('/property/');
		$n = (int)get_query_var('sitemap_n');
		$offset = ( $n > 1 ) ? ( $n - 1 ) * self::$max_entries : 0;

		$sitemap = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
		$sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" ';
		$sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		$rem = self::$max_entries;

		while ($rem > 0) {
			$args = array('offset'=>$offset, 'limit'=>self::$max_entries);
			//error_log('Fetching:');
			$response = PL_Listing::get($args);
			//error_log('Fetched:');

			foreach($response['listings'] as $listing) {
				$l = $listing['location'];
				$url = (empty($l['region'])?'region':$l['region']).'/'.(empty($l['locality'])?'locality':$l['locality']).'/'.(empty($l['postal'])?'postal':$l['postal']).'/'.(empty($l['neighborhood'])?'neighborhood':$l['neighborhood']).'/'.(empty($l['address'])?'address':$l['address']).'/'.$listing['id'];
				$url = $site_url.preg_replace('/[^a-z0-9\-\/]+/', '-', strtolower($url));
				$sitemap .= "\t<url>\n";
				$sitemap .= "\t\t<loc>".$url."/</loc>\n";
				$sitemap .= "\t\t<lastmod>" . $listing['updated_at'] . "</lastmod>\n";
				$sitemap .= "\t\t<changefreq>weekly</changefreq>\n";
				$sitemap .= "\t\t<priority>0.6</priority>\n";
				if (!empty($listing['images'])) {
					foreach($listing['images'] as $image) {
						if ($image['order'] == 1) {
							$sitemap .= "\t\t<image:image>\n";
							$sitemap .= "\t\t\t<image:loc>" . esc_html($listing['images'][0]['url']) . "</image:loc>\n";
							$sitemap .= "\t\t</image:image>\n";
							break;
						}
					}
				}
				$sitemap .= "\t</url>\n";
			}

			// if we are getting chunks in less than the requested amount then loop till got what we want
			if ($offset + $response['count'] == $response['total']) {
				break;
			}
			$offset += $response['count'];
			$rem -= $response['count'];
		}

		$sitemap .= '</urlset>';
		$wpseo_sitemaps->set_sitemap($sitemap);
	}
}
