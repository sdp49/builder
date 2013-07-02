<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
class PL_Search_Listing_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_search_listing';

	protected static $shortcode = 'search_listings';

	protected static $title = 'Search Listings';

	protected static $help = 
		'<p>
        You can insert your "activated" Listings snippet by using the [search_form] shortcode in a page or a post.
        The listings view is intended to be used alongside the [search_form] shortcode defined above as a container
        for the results of the search, with the snippet representing how an <i>individual</i> listing that matches
        the search criteria will be displayed.
		</p>';

	protected static $filters = array();
	
	protected static $subcodes = array(
		'price'			=> array('help' => 'Property price'),
		'sqft'			=> array('help' => 'Total square feet'),
		'beds'			=> array('help' => 'Number of bedrooms'),
		'baths'			=> array('help' => 'Number of bathrooms'),
		'half_baths'	=> array('help' => 'Number of half bathrooms'),
		'avail_on'		=> array('help' => 'Date the property will be available'),
		'url'			=> array('help' => 'Link to page for the listing'),
		'address'		=> array('help' => 'Street address'),
		'locality'		=> array('help' => 'Locality'),
		'region'		=> array('help' => 'Region'),
		'postal'		=> array('help' => 'Zip/postal code'),
		'neighborhood'	=> array('help' => 'Neighborhood'),
		'county'		=> array('help' => 'County'),
		'country'		=> array('help' => 'Country'),
		//'coords'		=> array('help' => 'aa'),
		'unit'			=> array('help' => 'Unit'),
		'full_address'	=> array('help' => 'Full address'),
		'email'			=> array('help' => 'Email address for this listing'),
		'phone'			=> array('help' => 'Contact phone'),
		'desc'			=> array('help' => 'Property description'),
		'image'			=> array('help' => 'Property thumbnail image'),
		'mls_id'		=> array('help' => 'MLS #'),
		//'map'			=> array('help' => 'aa'),
		'listing_type'	=> array('help' => 'Type of listing'),
		'img_gallery'	=> array('help' => 'Image gallery'),
		//'amenities'		=> array('help' => 'aa'),
		'price_unit'	=> array('help' => ''),
		//'compliance'	=> array('help' => 'aa'),
	);

	protected static $template = array(
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'HTML', 'default' => '
<section class="my-lu">
	<div class="my-lu-head">
		<a href="[url]">[address] [locality], [region]</a>
	</div>
	<div class="my-lu-body">
		<div class="my-lu-image">[image]</div>

		<div class="my-lu-details">
			<ul>
				<li>[beds]<span> Bed(s)</span>
				</li>
				<li>[baths]<span> Bath(s)</span>
				</li>
				<li>[sqft]<span> Sqft</span>
				</li>
			</ul>
			<p class="my-lu-mls">MLS #: [mls_id]</p>
		</div>
		<p class="my-lu-price">
			Price: <span>[price]</span>
		</p>
		<p class="my-lu-desc">[desc]</p>
		<a class="my-lu-details" href="[url]">View Listing Details</a>
	</div>
</section>
',
			'description'	=> '
You can use any valid HTML in this field to format the subcodes.' ),

		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 
			'default' => '
/* sample list box */
.my-listings {
	clear: both;
	border: 1px solid #000;
	padding: 5px;
	width: 400px;
	font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	overflow: hidden;
}
/* make the selectors line up */
.my-listings label {
	display: block;
	float: left;
	width: 10em;
}
#placester_listings_list_length label {
	float: none;
	width: auto;
}

/* format the table that holds the listings */				
.my-listings .placester_properties {
	width: 100%;
}

/* format the pagination links */
.my-listings .paginate_button {
	padding-right: 1em;
}
/* page numbers */
.my-listings .dataTables_paginate span {
	padding-right: 1em;
}

/* section defined above to hold a single listing */				
section.my-lu {
	margin-bottom: 2px;
	background: #efefef;
	padding: 3px;
}
/* section defined above to hold the body of the listing */				
.my-lu-body {
	width: 100%;
	overflow: hidden;
}
/* section defined above to hold the listing heading */				
.my-lu-head {
	margin: 3px 0;
}
/* section defined above to hold the listing image */				
.my-lu-image {
	float: left;
}
/* sections defined above to hold the details of the listing */				
.my-lu-details,
.my-lu-price,
.my-lu-desc {
	float: right;
	clear: right;
	width: 50%;
	font-size: 12px;
}
.my-lu p,
.my-lu li {
	margin: 0;
	padding: 0;
}
.my-lu ul {
	margin: 0;
	padding-left: 1.2em;
}',
			'description'	=> '
You can use any valid CSS in this field to customize the listings, which will also inherit the CSS from the theme.' ),

		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the listings', 'default' => '<div class="my-listings">',
			'description'	=> '
You can use any valid HTML in this field and it will appear before the listings.
For example, you can wrap the whole list with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.' ),

		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the listings', 'default' => '</div>',
			'description'	=> '
You can use any valid HTML in this field and it will appear after the listings.' ),
	);




	protected function _get_filters() {
		if (class_exists('PL_Config')) {
			return PL_Config::PL_API_LISTINGS('get', 'args');
		}
		else {
			return array();
		}
	}
}

PL_Search_Listing_CPT::init(__CLASS__);
