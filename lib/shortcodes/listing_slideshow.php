<?php
/**
 * Post type/Shortcode for displaying the slideshow
 *
 */

class PL_Listing_Slideshow_CPT extends PL_Search_Listing_CPT {

	protected $shortcode = 'listing_slideshow';

	protected $title = 'Slideshow';

	protected $help =
		'<p>
		You can create a slideshow for your Featured Listings by using the
		[listing_slideshow post_id="<em>slideshowid</em>"] shortcode.
		</p>';

	protected $options = array(
		'context'		=> array( 'type' => 'select', 'label' => 'Template', 'default' => '' ),
		'width'			=> array( 'type' => 'int', 'label' => 'Width', 'default' => 610, 'description' => '(px)' ),
		'height'		=> array( 'type' => 'int', 'label' => 'Height', 'default' => 320, 'description' => '(px)' ),
		'widget_class'	=> array( 'type' => 'text', 'label' => 'CSS Class', 'default' => '', 'description' => '(optional)' ),
		'animation' 	=> array( 'type' => 'select', 'label' => 'Transition Between Images', 'options' => array(
				'fade' => 'fade',
				'horizontal-slide' => 'horizontal-slide',
				'vertical-slide' => 'vertical-slide',
				'horizontal-push' => 'horizontal-push',
			),
			'default' => 'fade' ),
		'animationSpeed'	=> array( 'type' => 'int', 'label' => 'Transition Speed', 'default' => 800, 'description' => 'How long the transition takes, ms' ),	// how fast animtions are
		'advanceSpeed'		=> array( 'type' => 'int', 'label' => 'Advance Speed', 'default' => 5000, 'description' => 'How long to wait before transitioning to next image, ms' ),	// if timer is enabled, time between transitions
		'timer'				=> array( 'type' => 'checkbox', 'label' => 'Timer', 'default' => true ),				// true or false to have the timer
		'pauseOnHover'		=> array( 'type' => 'checkbox', 'label' => 'Pause on hover', 'default' => true ),		// if you hover pauses the slider
		'pl_featured_listing_meta' => array( 'type' => 'featured_listing_meta', 'default' => '' ),
	);

	protected $slideshow_subcodes = array(
		'ls_index'		=> array('help' => 'Index of the listing in the slideshow, starting with 1.'),
		// TODO: these should be removed
		'ls_url'		=> array('help' => 'The url to view the listing.'),
		'ls_address'	=> array('help' => 'The street address of the listing.'),
		'ls_beds'		=> array('help' => 'The number of bedrooms.'),
		'ls_baths'		=> array('help' => 'The number of bathrooms.'),
	);

	protected $template = array(
		'snippet_body' => array(
			'type'			=> 'textarea',
			'label'			=> 'Caption text for each slideshow image',
			'description'	=> 'You can use the template tags with any valid HTML in this field to lay each listing.',
			'help'			=> '',
			'css'			=> 'mime_html',
		),

		'css' => array(
			'type'			=> 'textarea',
			'label'			=> 'CSS',
			'description'	=> 'You can use any valid CSS in this field to style the slideshow, which will also inherit the CSS from the theme.',
			'help'			=> '',
			'css'			=> 'mime_css',
		),

		'before_widget'	=> array(
			'type'			=> 'textarea',
			'label'			=> 'Add content before the slideshow',
			'description'	=> 'You can use any valid HTML in this field and it will appear before the slideshow images.
For example, you can wrap the whole slideshow with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.',
			'help'			=> '',
			'css'			=> 'mime_html',
		),

		'after_widget' => array(
			'type' => 'textarea',
			'label' => 'Add content after the slideshow',
			'description' => 'You can use any valid HTML in this field and it will appear after the slideshow images.',
			'help' => '',
			'css' => 'mime_html',
		),
	);

	private static $singleton = null;



	public static function init() {
		self::$singleton = parent::_init(__CLASS__);
		self::$singleton->subcodes += self::$singleton->slideshow_subcodes;
	}

	public function get_args($with_choices = false, $with_help = false) {
		$ret = parent::get_args($with_choices, $with_help);

		if ($with_help && !empty($this->subcodes) && !empty($this->template['snippet_body']) ) {
			$template_tags = '<h4>Slideshow Template Tags</h4>';
			$template_tags .= '<p>Use the following template tags to customize the captions for each slideshow image.
				When the template is rendered in a web page, the tag will be replaced with the corresponding attribute of the property listing:<br /></p>';
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
		return $ret;
	}

	/**
	 * Return array of options used to configure this custom shortcode
	 * @param $id int		: id of custom shortcode record
	 * @return array/bool	: array of results/false if id invalid/trashed
	 */
	public function get_options($id) {
		$options = array();
		if (($post = get_post($id, ARRAY_A, array('post_type'=>'pl_general_widget'))) && $post['post_status']=='publish') {
			$postmeta = get_post_meta($id);
			if (!empty($postmeta['shortcode'])) {
				foreach($this->options as $attr=>$vals) {
					if ($attr == 'context') {
						$key = 'pl_cpt_template';
					}
					elseif ($attr=='pl_featured_listing_meta') {
						// we have featured listings
						$options['post_id'] = $id;
						$options['post_meta_key'] = 'pl_featured_listing_meta';
						continue;
					}
					else {
						$key = $attr;
					}
					if (isset($postmeta[$key])) {
						$options[$attr] = maybe_unserialize($postmeta[$key][0]);
					}
				}
			}
			return $options;
		}
		return false;
	}

	public static function shortcode_handler($atts, $content) {
		$content = PL_Component_Entity::listing_slideshow($atts);

		return self::wrap('listing_slideshow', $content);
	}


	public static function do_templatetags($content, $listing_data) {
		PL_Component_Entity::$listing = $listing_data;
		return self::_do_templatetags(__CLASS__, array_keys(self::$singleton->subcodes), $content);
	}

	public static function templatetag_callback($m) {
		if ($m[1]=='[' && $m[6]==']') {
			return substr($m[0], 1, -1);
		}

		$tag = $m[2];
		$atts = shortcode_parse_atts($m[3]);
		$content = $m[5];

		if ($tag == 'if') {
			$val = isset($atts['value']) ? $atts['value'] : null;
			if (empty($atts['group'])) {
				if ((!isset(PL_Component_Entity::$listing[$atts['attribute']]) && $val==='') ||
					(isset(PL_Component_Entity::$listing[$atts['attribute']]) && (PL_Component_Entity::$listing[$atts['attribute']]===$val || (is_null($val) && PL_Component_Entity::$listing[$atts['attribute']])))) {
					return self::_do_templatetags(__CLASS__, array_keys(self::$singleton->subcodes), $content);
				}
			}
			elseif ((!isset(PL_Component_Entity::$listing[$atts['group']][$atts['attribute']]) && $val==='') ||
				(isset(PL_Component_Entity::$listing[$atts['group']][$atts['attribute']]) && (PL_Component_Entity::$listing[$atts['group']][$atts['attribute']]===$val || (is_null($val) && PL_Component_Entity::$listing[$atts['group']][$atts['attribute']])))) {
				return self::_do_templatetags(__CLASS__, array_keys(self::$singleton->subcodes), $content);
			}
			return '';
		}
		$content = PL_Component_Entity::listing_slideshow_sub_entity($atts, $content, $tag);
		return self::wrap('listing_sub', $content);
	}

	/**
	 * Generate a shortcode for this shortcode type from arguments
	 * @param string $shortcode_type	: shortcode type we will be generating
	 * @param array $args				: shortcode post type record including postmeta values
	 * @return string					: returned shortcode
	 */
	public function generate_shortcode_str($args) {
		// prepare args
		$sc_args = '';
		$class_options = $this->options;
		foreach($args as $option => $value) {
			if (!empty($value)) {
				// only output options that are valid for this type
				if (!empty($class_options[$option])) {
					if ($class_options[$option]['type'] == 'featured_listing_meta' && !empty($args['id'])) {
						$sc_args .= " post_id='".$args['id']."' post_meta_key='pl_featured_listing_meta' ";
					}
					elseif (!is_array($value)) {
						$sc_args .= ' '.$option."='".$value."'";
					}
				}
			}
		}

		$shortcode = '[' . $this->shortcode . $sc_args . ']';

		return $shortcode;
	}
}

PL_Listing_Slideshow_CPT::init();
