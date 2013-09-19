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
		'search_form_tpl'		=> array('type' => 'select', 'label' => 'Search Form Template', 'default' => '', 'description' => '' ),
		'search_map_tpl'		=> array('type' => 'select', 'label' => 'Map Template', 'default' => '', 'description' => '' ),
		'search_listings_tpl'	=> array('type' => 'select', 'label' => 'Listings Template', 'default' => '', 'description' => '' ),
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
	
	private static $singleton = null;

	private static $template_data = array();


	public static function init() {
		self::$singleton = parent::_init(__CLASS__);
		$inst = self::$singleton;
		add_shortcode( $inst->shortcode, array( __CLASS__, 'shortcode_handler' ) );
	}
	
	/**
	 * Called when a shortcode is found in a post.
	 * @param array $atts
	 * @param string $content
	 */
	public static function shortcode_handler( $atts, $content ) {
		self::$template_data = array();
		
		$atts = wp_parse_args($atts, array(
			'search_form_tpl'		=> '',
			'search_map_tpl'		=> '',
			'search_listings_tpl'	=> '',
		));		
		$atts['context'] = empty($atts['context']) ? 'shortcode' : $atts['context'];

		self::$template_data['search_form'] = array( 
				'method' => array('PL_Component_Entity', 'search_form_entity'), 
				'params' => array('context'=>$atts['search_form_tpl']));
		self::$template_data['search_map'] = array( 
				'method' => array('PL_Component_Entity', 'search_map_entity'), 
				'params' => array('context'=>$atts['search_map_tpl'], 'sync_map_to_list'=>true));
		self::$template_data['search_listings'] = array( 
				'method' => array('PL_Component_Entity', 'search_listings_entity'), 
				'params' => array('context'=>$atts['search_listings_tpl']));

		if (!has_filter('pls_idx_html_' . $atts['context'])) {
			add_filter('pls_idx_html_' . $atts['context'], array(__CLASS__,'pl_idx_html_callback'), 10, 3);
		}
		
		return self::wrap( 'idx', apply_filters('pls_idx_html_' . $atts['context'], '', self::$template_data, $atts) );
	}
	
	public static function pl_idx_html_callback($html, $form_data, $request) {
		wp_enqueue_style( 'jquery-ui', trailingslashit( PLS_JS_URL ) . 'libs/jquery-ui/css/smoothness/jquery-ui-1.8.17.custom.css' );
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');
		
		$body = $header = $footer = '';
		$template = PL_Shortcode_CPT::load_template($request['context'], 'pl_idx');

		$header .= empty($template['css']) ? '' : '<style type="text/css">'.$template['css'].'</style>';
		$header .= empty($template['before_widget']) ? '' : do_shortcode($template['before_widget']);
		$footer .= empty($template['after_widget']) ? '' : do_shortcode($template['after_widget']);
		$body = self::do_templatetags($template['snippet_body'], $form_data);
		return $header.$body.$footer;
	}

	public function get_options_list($with_choices = false) {
		if ($with_choices) {
			$this->options['search_form_tpl']['options'][] = 'default';
			foreach(PL_Shortcode_CPT::template_list('search_form') as $key=>$tpl) {
				$this->options['search_form_tpl']['options'][$key] = $tpl['title'];
			}
			$this->options['search_map_tpl']['options'][] = 'default';
			foreach(PL_Shortcode_CPT::template_list('search_map') as $key=>$tpl) {
				$this->options['search_map_tpl']['options'][$key] = $tpl['title'];
			}
			$this->options['search_listings_tpl']['options'][] = 'default';
			foreach(PL_Shortcode_CPT::template_list('search_listings') as $key=>$tpl) {
				$this->options['search_listings_tpl']['options'][$key] = $tpl['title'];
			}
		}
		return $this->options;
	}
	
	public static function do_templatetags($content, &$data) {
		return self::_do_templatetags(__CLASS__, array_keys(self::$singleton->subcodes), $content);
	}
	
	public static function templatetag_callback($m) {
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return substr($m[0], 1, -1);
		}

		$tag = $m[2];
		$attr = array_merge((array)self::$template_data[$tag]['params'], (array)shortcode_parse_atts($m[3]));
		if ( isset( self::$template_data[$tag] ) ) {
			// use form data from partial to construct
			return $m[1] . call_user_func(self::$template_data[$tag]['method'], $attr) . $m[6];
		}
		else {
			return $m[0];
		}
	}
}

PL_IDX_CPT::init();
