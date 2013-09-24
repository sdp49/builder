<?php
/**
 * Post type/Shortcode to generate a single combo control containing search form, map, listings
 *
 */

class PL_IDX_CPT extends PL_SC_Base {

	protected $shortcode = 'pl_idx';

	protected $title = 'IDX Shortcode';

	protected $help = '';

	protected $options = array(
		'context'				=> array('type' => 'select', 'label' => 'Template', 'default' => '' ),
		'width'					=> array('type' => 'int', 'label' => 'Width(px)', 'default' => 600 ),
		'height'				=> array('type' => 'int', 'label' => 'Height(px)', 'default' => 600 ),
	);

	protected $subcodes = array(
		'search_form'		=> array('help' => 'Search form'),
		'search_map'		=> array('help' => 'Search map'),
		'search_listings'	=> array('help' => 'Search results'),
	);

	protected $default_tpl_id = 'sample-tabbed';
	
	protected $template = array(
		'snippet_body'	=> array(
			'type' => 'textarea',
			'label' => 'HTML',
			'css' => 'mime_html',
			'description' => 'You can use the template tags with any valid HTML in this field to lay out the template.'
		),

		'search_form'	=> array(
			'type' => 'textarea',
			'label' => 'HTML to format search form',
			'css' => 'mime_html',
			'description' => 'You can use the template tags with any valid HTML in this field to lay out the form. Leave this field empty to use the built in template.'
		),
		
		'search_listings'	=> array(
			'type' => 'textarea',
			'label' => 'HTML to format each individual listing',
			'css' => 'mime_html',
			'description' => 'You can use the template tags with any valid HTML in this field to lay out each listing. Leave this field empty to use the built in template.'
		),
				
		'css' => array(
			'type' => 'textarea',
			'label' => 'CSS',
			'css' => 'mime_css',
			'description' => 'You can use any valid CSS in this field to style the template, which will also inherit the CSS from the theme.'
		),

		'before_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content before the template body',
			'css' => 'mime_html',
			'description' => 'You can use any valid HTML in this field and it will appear at the beginning of the template. For example, you can wrap the whole list with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.'
		),

		'after_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content after the template body',
			'css' => 'mime_html',
			'description' => 'You can use any valid HTML in this field and it will appear at the end of the template.
For example, you might want to include the [compliance] shortcode.'
		),
	);
	
	// stores fetched filter value
	protected static $idx_filter_options = array();

	private static $singleton = null;

	private static $template_data = array();




	public static function init() {
		self::$singleton = parent::_init(__CLASS__);
		$inst = self::$singleton;
		add_shortcode( $inst->shortcode, array( __CLASS__, 'shortcode_handler' ) );

		// add formatting for individual listings here because they are fetched using ajax
		$templates = PL_Shortcode_CPT::template_list('pl_idx', true);
		foreach ($templates as $id => $attr) {
			add_filter( 'pls_listings_list_ajax_item_html_search_listings_pl_idx_' . $id, array(__CLASS__,'pls_listings_list_ajax_item_html_search_listings_callback'), 10, 3 );
		}
		if (!has_filter('pls_idx_html_shortcode')) {
			add_filter( 'pls_listings_list_ajax_item_html_search_listings_pl_idx_shortcode', array(__CLASS__,'pls_listings_list_ajax_item_html_search_listings_callback'), 10, 3 );
		}
	}

	/**
	 * Get list of filter options from the api.
	 */
	public function get_filters_list($with_choices = false) {
		if (empty(self::$idx_filter_options)) {
			self::$idx_filter_options = PL_Shortcode_CPT::get_listing_filters(false, $with_choices);
		}
		return self::$idx_filter_options;
	}
	
	/**
	 * Called when a shortcode is found in a post.
	 * @param array $atts
	 * @param string $content
	 */
	public static function shortcode_handler( $atts, $content ) {
		self::$template_data = array();

		add_filter('pl_filter_wrap_filter', array( 'PL_Shortcodes', 'pl_filter_wrap_default_filters' ));
		$filters = '';
		if( ! empty( $content ) ) {
			$filters = do_shortcode( strip_tags( $content ) );
			$filters = str_replace('&nbsp;', '', $filters);
		}

		if (!empty($atts['id'])) {
			// if we are a custom shortcode fetch the record so we can display the correct filters
			// for the js
			$listing_filters = PL_Shortcode_CPT::get_shortcode_filters(self::$singleton->shortcode, $atts['id']);
			$filters = PL_Component_Entity::convert_filters($listing_filters) . $filters;
		}

		$atts['context'] = empty($atts['context']) ? 'shortcode' : $atts['context'];
		$comp_context = 'pl_idx_'.$atts['context'];
		
		self::$template_data['search_form'] = array( 
				'method' => array('PL_Component_Entity', 'search_form_entity'), 
				'param1' => array('context'=>$comp_context),
				'param2' => '',
		);
		self::$template_data['search_map'] = array( 
				'method' => array('PL_Component_Entity', 'search_map_entity'), 
				'param1' => array('dom_id'=>'idx_map_canvas', 'context'=>$comp_context, 'sync_map_to_list'=>true),
				'param2' => '',
		);
		self::$template_data['search_listings'] = array( 
				'method' => array('PL_Component_Entity', 'search_listings_entity'), 
				'param1' => array('context'=>$comp_context),
				'param2' => $filters,
		);

		if (!has_filter('pls_idx_html_' . $atts['context'])) {
			add_filter('pls_idx_html_' . $atts['context'], array(__CLASS__,'pl_idx_html_callback'), 10, 3);
			if ($comp_context) {
				// if we have a template then use it for the shortcode components
				add_filter( 'pls_listings_search_form_outer_'.$comp_context, array(__CLASS__,'pls_listings_search_form_outer_callback'), 10, 7 );
				add_filter( 'pls_listings_search_form_inner_'.$comp_context, array(__CLASS__,'pls_listings_search_form_inner_callback'), 10, 5 );
			}
		}
		
		return self::wrap( 'idx', apply_filters('pls_idx_html_' . $atts['context'], '', self::$template_data, $atts) );
	}
	
	public static function pl_idx_html_callback($html, $form_data, $request) {
		wp_enqueue_style( 'jquery-ui', trailingslashit( PLS_JS_URL ) . 'libs/jquery-ui/css/smoothness/jquery-ui-1.8.17.custom.css' );
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		
		$body = $header = $footer = '';
		self::$template_data['template'] = $template = PL_Shortcode_CPT::load_template($request['context'], 'pl_idx');

		$header .= empty($template['css']) ? '' : '<style type="text/css">'.$template['css'].'</style>';
		$header .= empty($template['before_widget']) ? '' : do_shortcode($template['before_widget']);
		$footer .= empty($template['after_widget']) ? '' : do_shortcode($template['after_widget']);
		$body = self::do_templatetags($template['snippet_body'], $form_data);
		return $header.$body.$footer;
	}
	
	public static function do_templatetags($content, &$data) {
		return self::_do_templatetags(__CLASS__, array_keys(self::$singleton->subcodes), $content);
	}
	
	public static function templatetag_callback($m) {
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return substr($m[0], 1, -1);
		}

		$tag = $m[2];
		$param1 = array_merge((array)self::$template_data[$tag]['param1'], (array)shortcode_parse_atts($m[3]));
		$param2 = self::$template_data[$tag]['param2'] . $m[5];

		if ( isset( self::$template_data[$tag] ) ) {
			// we found a tag try to construct it
			return $m[1] . call_user_func(self::$template_data[$tag]['method'], $param1, $param2) . $m[6];
		}
		else {
			return $m[0];
		}
	}

	/**
	 * Format the search form body using any template we might have.
	 * Called from PLS_Partials_Listing_Search_Form
	 */
	public static function pls_listings_search_form_inner_callback($form, $form_html, $form_options, $section_title, $context_var) {
		if (empty(self::$template_data['template']['search_form'])) {
			return $form;
		}
		return PL_Form_CPT::do_templatetags(self::$template_data['template']['search_form'], $form_html);
	}

	/**
	 * Format the whole search form.
	 * Called from PLS_Partials_Listing_Search_Form
	 */
	public static function pls_listings_search_form_outer_callback($form, $form_html, $form_options, $section_title, $form_data, $form_id, $context_var) {
		return $form;
	}

	/**
	 * Format single listing
	 */
	public static function pls_listings_list_ajax_item_html_search_listings_callback( $item_html, $listing, $context_var ) {
		if (empty(self::$template_data['template'])) {
			$context = substr(current_filter(), strlen('pls_listings_list_ajax_item_html_search_listings_pl_idx_'));
			self::$template_data['template'] = $template = PL_Shortcode_CPT::load_template($context, 'pl_idx');
		}
		if (empty(self::$template_data['template']['search_listings'])) {
			return $item_html;
		}
		return PL_Search_Listing_CPT::do_templatetags(self::$template_data['template']['search_listings'], $listing);
	}

	/**
	 * Callback to wrap formatting around search_map
	 */
	public static function pls_search_map_callback( $return, $listings, $request_params ) {
		return $return;
	}
}

PL_IDX_CPT::init();
