<?php
/**
 * Post type/Shortcode to generate a list of featured listings
 *
 */
include_once(PL_LIB_DIR . 'shortcodes/search_listings.php');

class PL_Featured_Listings_CPT extends PL_Search_Listing_CPT {

	protected static $pl_post_type = 'featured_listings';

	protected static $shortcode = 'featured_listings';

	protected static $title = 'Featured Listings';

	protected static $help = 
		'<p>
		</p>';

	protected static $options = array(
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'pl_featured_listing_meta' => array( 'type' => 'featured_listing_meta', 'default' => '' ),
	);

	protected static $filters = array();

	// Use the same subcodes, template as search listings shortcode
	// protected static $subcodes = array();
	// protected static $template = array();
	

	public function __construct() {
		parent::__construct();
		add_shortcode($this::$shortcode, array(__CLASS__, 'handle_shortcode'));
	}
	
	public function handle_shortcode($args, $content) {
		return 'aaa';
	}
	
	protected static $template = array(
		'snippet_body'	=> array(
			'type' => 'textarea',
			'label' => 'HTML to format each individual listing',
			'css' => 'mime_html', 
			'default' => '
<!-- Listing -->
<div class="wf-listing">
	<div class="wf-image">
		<a href="[url]">
			[image width=300]							
		</a>
		<p class="wf-price">[price]</p>
	</div>
	<p class="wf-address">
		<a href="[url]">[address] [locality], [region]</a>
	</p>
	<p class="wf-basics">
		<span class="hidden-phone">Beds: <strong>[beds]</strong>&nbsp;</span> <span class="hidden-phone">Baths: <strong>[baths]</strong>&nbsp;</span> <span class="wf-mls">MLS #: [mls_id]</span>
	</p>
</div>
			',
			'description'	=> 'You can use any valid HTML in this field to format the subcodes.' ),

		'css' => array(
			'type' => 'textarea',
			'label' => 'CSS',
			'css' => 'mime_css',
			'default' => '
.visible-phone { display: none !important; }

.visible-tablet { display: none !important; }

.hidden-desktop { display: none !important; }

.visible-desktop { display: inherit !important; }

@media (min-width: 768px) and (max-width: 979px) { .hidden-desktop { display: inherit !important; }
  .visible-desktop { display: none !important; }
  .visible-tablet { display: inherit !important; }
  .hidden-tablet { display: none !important; } }
@media (max-width: 767px) { .hidden-desktop { display: inherit !important; }
  .visible-desktop { display: none !important; }
  .visible-phone { display: inherit !important; }
  .hidden-phone { display: none !important; } }
.visible-print { display: none !important; }

@media print { .visible-print { display: inherit !important; }
  .hidden-print { display: none !important; } }
.non-row-wrapper { padding-bottom: 40px; margin-left: -3%; max-width: 1080px; width: 100%; }
@media (min-width: 1280px) { .non-row-wrapper { margin-left: 1%; } }
@media (min-width: 768px) and (max-width: 979px) { .non-row-wrapper { margin-left: 0%; } }
@media (max-width: 767px) { .non-row-wrapper { margin-left: -1%; } }
@media (max-width: 420px) { .non-row-wrapper { margin-left: -1%; } }
.non-row-wrapper .sort_wrapper { margin-left: 3%; padding: 10px 0 !important; }
.non-row-wrapper .sort_wrapper .sort_item { float: left !important; width: 30% !important; }
.non-row-wrapper .sort_wrapper .sort_item label { float: left; width: 100%; }
.non-row-wrapper #container { width: 100% !important; }
.non-row-wrapper #container tr { width: 30%; display: inline-block; margin-left: 2.9%; }
@media (min-width: 768px) and (max-width: 979px) { .non-row-wrapper #container tr { margin-left: 2%; } }
@media (max-width: 767px) { .non-row-wrapper #container tr { margin-left: 2%; width: 47%; } }
@media (max-width: 420px) { .non-row-wrapper #container tr { margin-left: 2%; width: 97%; } }
.non-row-wrapper #container tr .wf-listing { width: 100%; }
@media (min-width: 768px) and (max-width: 979px) { .non-row-wrapper #container tr .wf-listing { width: 100%; } }
@media (max-width: 767px) { .non-row-wrapper #container tr .wf-listing { width: 100%; } }
@media (max-width: 420px) { .non-row-wrapper #container tr .wf-listing { width: 100%; } }
.non-row-wrapper #container thead { display: none; }
.non-row-wrapper #container .dataTables_paginate .paginate_active { font-weight: 600; }

.wf-listing { width: 30%; display: inline-block; margin-left: 2.9%; }
@media (min-width: 768px) and (max-width: 979px) { .wf-listing { margin-left: 2%; } }
@media (max-width: 767px) { .wf-listing { margin-left: 2%; width: 47%; } }
@media (max-width: 420px) { .wf-listing { margin-left: 2%; width: 97%; } }

.wf-listing .wf-image { width: 100%; }
.wf-listing .wf-image a img { width: 100%; }

.wf-listing { vertical-align: top; padding-bottom: 30px; }
.wf-listing .wf-image img { border: none !important; float: left !important; width: 100% !important; max-width: 100% !important; }
.wf-listing .wf-image .wf-price { color: white; text-decoration: none; font-size: 0.9em; padding: 6px 12px; margin: -37px 0 0 0 !important; float: left; background: black; background: rgba(0, 0, 0, 0.8); }
.wf-listing .wf-address, .wf-listing .wf-basics { float: left; width: 100%; font-family: Arial, sans-serif; }
.wf-listing .wf-address { margin: 10px 0 0 !important; font-size: 18px; line-height: 20px; height: 42px; overflow: hidden; }
.wf-listing .wf-basics { margin: 10px 0 0; font-size: 14px; color: #4b4b4b; }
			',
			'description'	=> 'You can use any valid CSS in this field to customize the listings, which will also inherit the CSS from the theme.' ),

		'before_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content before the listings',
			'css' => 'mime_html',
			'default' => '<div class="non-row-wrapper">',
			'description'	=> 'You can use any valid HTML in this field and it will appear before the listings. For example, you can wrap the whole list with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.' ),

		'after_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content after the listings',
			'css' => 'mime_html',
			'default' => '</div>',
			'description'	=> 'You can use any valid HTML in this field and it will appear after the listings.' ),
	);

	/**
	 * No filters
	 * @see PL_SC_Base::_get_filters()
	 */
	protected function _get_filters() {
		return array();
	}
}

PL_Featured_Listings_CPT::init(__CLASS__);
