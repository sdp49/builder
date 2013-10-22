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
		'context'			=> array('type' => 'select', 'label' => 'Template', 'default' => '' ),
	);

	protected $subcodes = array(
		'search_form'		=> array('help' => 'Inserts a property search form.'),
		'search_map'		=> array('help' => 'Inserts a map to display the locations of property listings.'),
		'search_listings'	=> array('help' => 'Inserts a list of listings filtered by the search form.'),
	);

	protected $default_tpl_id = 'all-purpose-sales';

	protected $template = array(
		'snippet_body' => array(
			'type'			=> 'textarea',
			'label'			=> 'HTML',
			'description'	=> 'You can use the template tags with any valid HTML in this field to lay out the template.',
			'help'			=> '',
			'css'			=> 'mime_html',
		),

		'search_form' => array(
			'type'			=> 'textarea',
			'label'			=> 'HTML to format search form',
			'description'	=> 'You can use the search form template tags with any valid HTML in this field to lay out the form. Leave this field empty to use the built in template.',
			'help'			=> '',
			'css'			=> 'mime_html',
		),

		'search_listings' => array(
			'type'			=> 'textarea',
			'label'			=> 'HTML to format each individual listing',
			'description'	=> 'You can use the listing template tags with any valid HTML in this field to lay out each listing. Leave this field empty to use the built in template.',
			'help'			=> '',
			'css'			=> 'mime_html',
		),

		'css' => array(
			'type'			=> 'textarea',
			'label'			=> 'CSS',
			'description'	=> 'You can use any valid CSS in this field to style the template, which will also inherit the CSS from the theme.',
			'help'			=> '',
			'css'			=> 'mime_css',
		),

		'javascript' => array(
			'type'			=> 'textarea',
			'label'			=> 'JavaScript',
			'description'	=> 'You can use any valid JavaScript in this field to manipulate the template. You do not need to use <script> tags when adding JavaScript to this field.',
			'help'			=> '',
			'css'			=> 'mime_javascript',
		),

		'before_widget'	=> array(
			'type'			=> 'textarea',
			'label'			=> 'Add content before the template body',
			'description'	=> 'You can use any valid HTML in this field and it will appear at the beginning of the template. For example, you can wrap the whole list with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.',
			'help'			=> '',
			'css'			=> 'mime_html',
		),

		'after_widget' => array(
			'type'			=> 'textarea',
			'label'			=> 'Add content after the template body',
			'description'	=> 'You can use any valid HTML in this field and it will appear at the end of the template.
For example, you might want to include the [compliance] shortcode.',
			'help'			=> '',
			'css'			=> 'mime_html',
		),
	);

	// stores fetched filter value
	protected static $idx_filter_options = array();

	private static $template_data = array();




	public static function init() {
		parent::_init(__CLASS__);

		// add formatting for individual listings here because they are fetched using ajax
		$templates = PL_Shortcode_CPT::template_list('pl_idx', true);
		foreach ($templates as $id => $attr) {
			add_filter('pls_listings_list_ajax_item_html_search_listings_pl_idx_' . $id, array(__CLASS__,'pls_listings_list_ajax_item_html_search_listings_callback'), 10, 3);
		}
		if (!has_filter('pls_idx_html_shortcode')) {
			add_filter('pls_listings_list_ajax_item_html_search_listings_pl_idx_shortcode', array(__CLASS__,'pls_listings_list_ajax_item_html_search_listings_callback'), 10, 3);
		}
	}

	public function get_args($with_choices = false, $with_help = false) {
		$ret = parent::get_args($with_choices, $with_help);

		if ($with_help) {
			foreach ($ret['template'] as $field=>$fattrs) {
				if ($field == 'snippet_body') {
					// build help text
					$template_tags = '<h4>IDX Template Tags</h4>';
					$template_tags .= '<p>Use the following template tags to lay out your idx template.
						You can only use each one once, but you can use JavaScript to manipulate how each one appears
						by applying different CSS classes to them if, for example, you want to have a tabbed layout
						with listings presented in different layouts in each tab:<br /></p>';
					foreach($this->subcodes as $template_tag=>$atts) {
						$template_tags .= '<h4 class="subcode"><a href="#">['.$template_tag.']</a></h4>';
						if (!empty($atts['help'])) {
							$template_tags .= '<div class="description subcode-help">'. $atts['help'];
							if ($template_tag=='custom' || $template_tag=='if') {
								$template_tags = $template_tags . '<br />Click <a href="#" class="show_listing_attributes">here</a> to see a list of available listing attributes.';
							}
							$template_tags .= '</div>';
						}
					}
					$ret['template']['snippet_body']['help'] .=  $template_tags;
				}
				else {
					$attrs = PL_Shortcode_CPT::get_shortcode_attrs($field, false, true);
					if (!empty($attrs['template']['snippet_body']['help'])) {
						$ret['template'][$field]['help'] .=  $attrs['template']['snippet_body']['help'];
					}
				}
			}
		}
		return $ret;
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
	public function shortcode_handler($atts, $content) {
		$this->template_data = array();

		$listing_filters = array();
		$filters = '';
		if (!empty($content)) {
			$listing_filters = self::parse_filters($content);
		}
		if (!empty($atts['id'])) {
			// if we are a custom shortcode fetch the record so we can display the correct filters
			// for the js
			$listing_filters += PL_Shortcode_CPT::get_shortcode_filters($this->shortcode, $atts['id']);
			// and template and other attributes
			$options = PL_Shortcode_CPT::get_shortcode_options($this->shortcode, $atts['id']);
			if ($options!==false) {
				$atts = wp_parse_args($atts, $options);
			}
			else {
				unset($atts['id']);
			}
		}
		$filters = PL_Component_Entity::convert_filters($listing_filters);
		
		if (!empty($atts['context'])) {
			$tpl = PL_Shortcode_CPT::load_template($atts['context'], 'pl_idx');
			if ($tpl['title'] != $atts['context']) {
				unset($atts['context']);
			}
		}

		$atts['context'] = empty($atts['context']) ? 'shortcode' : $atts['context'];
		$comp_context = 'pl_idx_'.$atts['context'];

		// set up hooks to be called to render each idx component when it is found in the template
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
			add_filter('pls_idx_html_' . $atts['context'], array($this,'pl_idx_html_callback'), 10, 3);
			if ($comp_context) {
				// if we have a template then use it for the shortcode components
				add_filter('pls_listings_search_form_outer_'.$comp_context, array(__CLASS__,'pls_listings_search_form_outer_callback'), 10, 7);
				add_filter('pls_listings_search_form_inner_'.$comp_context, array(__CLASS__,'pls_listings_search_form_inner_callback'), 10, 5);
				// we dont have a wrapper for the listings and the filter for individual listings has to be registered where accessible by ajax (ie in the init)
			}
		}

		return self::wrap('idx', apply_filters('pls_idx_html_' . $atts['context'], '', self::$template_data, $atts, self::filters_to_json($listing_filters)));
	}

	/**
	 * Find search filters in a shortcode's content
	 * @param string $content	: shortcode content
	 * @return array			: list of filters
	 */
	private function parse_filters($content) {
		$filters = array();

		if (preg_match_all('/\[\s*pl_filter ([^\]]*)\]/', $content, $raw_filters)) {
			unset($raw_filters[0]);
			foreach ($raw_filters[1] as $raw_filter) {
				if (preg_match('/\bgroup\s*=\s*("([^\s"]+)"|\'([^\s\']+)\')/', $raw_filter, $raw_filter_attr)) {
					$group = substr($raw_filter_attr[1], 1, -1);
					if (preg_match('/\bfilter\s*=\s*("([^\s"]+)"|\'([^\s\']+)\')/', $raw_filter, $raw_filter_attr)) {
						$filter = substr($raw_filter_attr[1], 1, -1);
						if (preg_match('/\bvalue\s*=\s*("([^\s"]+)"|\'([^\s\']+)\'|([^\s]+))/', $raw_filter, $raw_filter_attr)) {
							$value = substr($raw_filter_attr[1], 1, -1);
							if (strpos($value, '||') !==false ) {
								$values = explode('||', $value);
								foreach ($values as $value) {
									$filters[$group][$filter][] = $value;
								}
							}
							else {
								$filters[$group][$filter][] = $value;
							}
						}
					}
				}
			}
		}

		return $filters;
	}

	/**
	 * Turns an array of filters into a suitable json array
	 */
	private function filters_to_json($filters = array()) {
		$json = '';
		$filter_list = array();

		if (is_array($filters)) {
			$av_filters = PL_Shortcode_CPT::get_listing_filters();
			foreach($filters as $key1 => $value1) {
				if (is_array($value1)) {
					// we store custom data as custom but it uses filter name metadata
					$key = $key1 == 'custom' ? 'metadata' : $key1;
					if (array_diff_key($value1,array_keys(array_keys($value1)))) {
						foreach($value1 as $key2 => $value2) {
							$skey = count($value2) > 1 ? '[]' :'';
							foreach($value2 as $value3) {
								$filter_list[] = array('name'=>$key.'['.$key2.']'.$skey, 'value'=>$value3);
								//$filter_list[$key.'['.$key2.']'.$skey] = $value3;
							}
							if ($skey) {
								$filter_list[] = array('name'=>$key.'['.$key2.'_match]'.$skey, 'value'=>'in');
								//$filter_list[$key.'['.$key2.'_match]'] = 'in';
							}
							elseif (!empty($av_filters[$key1.'.'.$key2]['type']) && ($av_filters[$key.'.'.$key2]['type']=='text'|| $av_filters[$key.'.'.$key2]['type']=='textarea')) {
								$filter_list[] = array('name'=>$key.'['.$key2.'_match]'.$skey, 'value'=>'like');
								//$filter_list[$key.'['.$key2.'_match]'] = 'like';
							}
						}
					}
					else {
						// list
						$skey = count($value1) > 1 ? '[]' :'';
						foreach($value1 as $value2) {
							$filter_list[] = array('name'=>$key.$skey, 'value'=>$value2);
							//$filter_list[$key.$skey] = $value2;
						}
						if ($skey) {
							$filter_list[] = array('name'=>$key.'_match]', 'value'=>'in');
							//$filter_list[$key.'_match'] = 'in';
						}
						elseif (!empty($av_filters[$key]['type']) && ($av_filters[$key]['type']=='text' || $av_filters[$key]['type']=='textarea')) {
							$filter_list[] = array('name'=>$key.'_match]', 'value'=>'like');
							//$filter_list[$key.'_match'] = 'like';
						}
					}
				}
			}
		}
		return json_encode($filter_list);
	}
	
	/**
	 * Generate body of idx shortcode
	 */
	public function pl_idx_html_callback($html, $template_data, $atts = array()) {
		wp_enqueue_style('jquery-ui', trailingslashit(PLS_JS_URL) . 'libs/jquery-ui/css/smoothness/jquery-ui-1.8.17.custom.css');
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-tabs');

		$body = $header = $footer = '';
		self::$template_data['template'] = $template = PL_Shortcode_CPT::load_template($atts['context'], 'pl_idx');

		$header .= empty($template['css']) ? '' : '<style type="text/css">'.$template['css'].'</style>';
		$header .= empty($template['before_widget']) ? '' : do_shortcode($template['before_widget']);
		$footer .= empty($template['javascript']) ? '' : '<script type="text/javascript">'.$template['javascript'].'</script>';
		$footer .= empty($template['after_widget']) ? '' : do_shortcode($template['after_widget']);
		$body = $this->do_templatetags($template['snippet_body'], $template_data);
		return $header.$body.$footer;
	}

	public function do_templatetags($content, &$data) {
		return self::_do_templatetags(array($this, 'templatetag_callback'), array_keys($this->subcodes), $content);
	}

	public function templatetag_callback($m) {
		if ($m[1]=='[' && $m[6]==']') {
			return substr($m[0], 1, -1);
		}

		$tag = $m[2];
		$param1 = array_merge((array)self::$template_data[$tag]['param1'], (array)shortcode_parse_atts($m[3]));
		$param2 = self::$template_data[$tag]['param2'] . $m[5];

		if (isset(self::$template_data[$tag])) {
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
	public function pls_listings_search_form_inner_callback($form, $form_html, $form_options, $section_title, $context_var) {
		if (empty(self::$template_data['template']['search_form'])) {
			return $form;
		}
		return PL_Shortcode_CPT::do_templatetags('search_form', self::$template_data['template']['search_form'], $form_html);
	}

	/**
	 * Format the whole search form.
	 * Called from PLS_Partials_Listing_Search_Form
	 */
	public function pls_listings_search_form_outer_callback($form, $form_html, $form_options, $section_title, $form_data, $form_id, $context_var) {
		return $form;
	}

	/**
	 * Format single listing
	 */
	public static function pls_listings_list_ajax_item_html_search_listings_callback($item_html, $listing, $context_var) {
		if (empty(self::$template_data['template'])) {
			$context = substr(current_filter(), strlen('pls_listings_list_ajax_item_html_search_listings_pl_idx_'));
			self::$template_data['template'] = $template = PL_Shortcode_CPT::load_template($context, 'pl_idx');
		}
		if (empty(self::$template_data['template']['search_listings'])) {
			return $item_html;
		}
		return PL_Shortcode_CPT::do_templatetags('search_listings', self::$template_data['template']['search_listings'], $listing);
	}

	/**
	 * Callback to wrap formatting around search_map
	 */
	public function pls_search_map_callback($return, $listings, $request_params) {
		return $return;
	}
}

PL_IDX_CPT::init();
