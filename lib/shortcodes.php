<?php

/******
	This class defines the shortcodes that can be used to access blueprint
	functionality from any page or post.

	It handles both the implementation of each code in addition to hooking
	them into the proper events and filters.
******/

PL_Shortcodes::init();
class PL_Shortcodes {

	public static function init() {

		// register several helpful shortcodes for filters, for metadata, locaiton and common
		add_shortcode('pl_filter', array(__CLASS__, 'pl_filter_shortcode_handler'));

		// Separately register the Compliance shortcode as it's not completely relevant
		// to the widget types
		add_shortcode( 'compliance', array( __CLASS__, 'compliance_shortcode_handler' ) );

		//basically initializes the bootloader object if it's been defined because a
		//shortcode has been called
		add_action('wp_footer', array(__CLASS__, 'init_bootloader'));
	}


	/*** Shortcode Handlers ***/


	/**
	 * [compliance] handler
	 */
	public static function compliance_shortcode_handler( $atts ) {
		ob_start();
		PLS_Listing_Helper::get_compliance(array(
			'context' => 'listings'
		));
		$content = ob_get_clean();

		return self::wrap( 'compliance', $content );

	}

	/**
	 * Handle filters for listings as [pl_filter] shortcode
	 *
	 * Expected attributes:
	 *
	 * group - group="metadata" or group="location", for wrapping filter calls by group
	 * filter - filter="listing_types", filter="zoning_types" and used together with a group call
	 * value - the value of the filter
	 *
	 * //TODO: merge with component_entities convert_filters
	 *
	 * @param unknown_type $atts
	 * @param unknown_type $content
	 */
	public static function pl_filter_shortcode_handler( $atts, $content = '' ) {
		$out = '';
		$av_filters = PL_Shortcode_CPT::get_listing_filters();

		if( !isset( $atts['filter'] ) || ! isset( $atts['value'] ) ) {
			return "";
		}

		extract($atts);

		$filterlogic = $filter . '_match';
		$av_filter = $filter;
		$filterstr = $filter;
		if( isset( $group ) ) {
			$filterstr = $group . '[' . $filter . ']';
			$filterlogic = $group . '[' . $filterlogic . ']';
			$av_filter = $group . '.' . $av_filter;
		}
		$jsfilter = '';
		if (strpos($value, '||') !==false ) {//print_r($atts);die;
			$values = explode('||', $value);
			if (count($values) > 1) {
				$filterstr .= '[]';
				$jsfilter .= apply_filters('pl_filter_wrap_filter', "{ 'name': '" . $filterlogic . "', 'value' : 'in'} ");
			}
			foreach ($values as $value) {
				$jsfilter .= apply_filters('pl_filter_wrap_filter', "{ 'name': '" . $filterstr . "', 'value' : '" . $value . "'} ");
			}
		}
		else {
			if (!empty($av_filters[$av_filter]['type']) && ($av_filters[$av_filter]['type']=='text' || $av_filters[$av_filter]['type']=='textarea')) {
				$jsfilter .= apply_filters('pl_filter_wrap_filter', "{ 'name': '" . $filterlogic . "', 'value' : 'like'} ");
			}
			$jsfilter .= apply_filters('pl_filter_wrap_filter', "{ 'name': '" . $filterstr . "', 'value' : '" . $value . "'} ");
		}
		return $jsfilter;
	}


	/*** Helper Functions ***/


	/**
	 * Give themes a chance to wrap output and individual fields rendered by shortcodes
	 */
	public static function wrap( $shortcode, $content = '' ) {
		ob_start();
		do_action( $shortcode . '_pre_header' );
		// do some real shortcode work
		echo $content;
		do_action( $shortcode . '_post_footer' );
		return ob_get_clean();
	}

	public static function init_bootloader () {
		ob_start();
		?>
			<script type="text/javascript">
			jQuery(document).ready(function( $ ) {
				if (typeof bootloader === 'object') {
		  			bootloader.init();
			  	}
			});
			</script>
		<?php
		echo ob_get_clean();
	}


	/*** Admin Functions ***/


	/**
	 * Buffer shortcode admin pages to give us a chance to redirect if necessary
	 */
	public static function admin_buffer_op($page_hook) {
		add_action('load-'.$page_hook, array(__CLASS__, 'admin_header'));
		add_action('admin_footer-'.$page_hook, array(__CLASS__, 'admin_footer'));
	}

	public static function admin_header() {
		ob_start();
	}

	public static function admin_footer() {
		ob_end_flush();
	}

	public static function debug($var, $lines = 1) {
		echo '<pre>';
		$traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		for ($trace=1; $trace<=$lines; $trace++) {
			echo $traces[$trace]['file'].':'.(!empty($traces[$trace]['class'])?$traces[$trace]['class'].':':'').$traces[$trace]['function'].':'.(!empty($traces[$trace]['line'])?$traces[$trace]['line'].':':'')."\n";
		}
		var_dump($var);
		echo '</pre>';
	}
}

?>