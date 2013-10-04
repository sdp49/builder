<?php

$template = array(

'title' => 'Sample Tabbed Layout',

'css' => '

.pl-tpl-idx-sample-tabbed .form-grp {
	float: left;
	margin: 0 2em 1em 0;
	min-height: 7em;
}

.pl-tpl-idx-sample-tabbed .form-grp h6 {
	margin: 0;
}

.pl-tpl-idx-sample-tabbed .form-grp label {
	display: block;
	float: left;
	width: 6em;
}

/* Basic Styling for Listings */

.pl-tpl-idx-sample-tabbed .clear {
	clear: both;
}
.pl-tpl-idx-sample-tabbed .pls_search_form_listings form {
	clear: both;
	padding: 1em 0 0 0;
}
.pl-tpl-idx-sample-tabbed p {
	display: block !important;
	float: none !important;
	border: none !important;
	margin: 0 0 .1em 0 !important;
	padding: 0 !important;
	background: none !important;
	line-height: 1.2em !important;
}

/* style each listing... */
.pl-tpl-idx-sample-tabbed .listing-item {
	display: block !important;
	float: none !important;
	clear: both !important;
	margin: 0 !important;
	border: none !important;
	padding: 10px 0 25px 0 !important;
	background: none !important;
	font-weight: 300 !important;
	overflow: hidden !important;
	font-size: 14px;
	position: relative;
}
/* thumbnail */
.pl-tpl-idx-sample-tabbed .listing-thumbnail {
	float: left !important;
	margin: 0 20px 5px 0 !important;
	width: 180px !important;
}
.pl-tpl-idx-sample-tabbed .listing-thumbnail img {
	display: block !important;
	margin: 0 !important;
	border: none !important;
	padding: 0 !important;
	width: 180px !important;
	height: 120px !important;
}
/* defaults for text */
.pl-tpl-idx-sample-tabbed .listing-item-details a {
	margin: 0 !important;
	padding: 0 !important;
	text-decoration: none !important;
}
/* info block */
.pl-tpl-idx-sample-tabbed .listing-item-details {
	margin: 0 !important;
	padding: 0 !important;
}
/* heading */
.pl-tpl-idx-sample-tabbed header {
	float: none !important;
	margin: 0 !important;
	padding: 0 !important;
}
.pl-tpl-idx-sample-tabbed p.h4 {
	max-width: 570px !important;
	font-size: 18px !important;
}
.pl-tpl-idx-sample-tabbed .h4 a {
	color: inherit;
}
.pl-tpl-idx-sample-tabbed .basic-details ul {
	float: none !important;
	margin: .3em 0 !important;
	padding: 0 !important;
	width: auto !important;
	max-width: 370px !important;
	list-style-type: none !important;
	list-style-image: none !important;
	overflow: hidden !important;
}
.pl-tpl-idx-sample-tabbed .basic-details li {
	list-style: square outside none !important;
	float: left !important;
	margin: 0 .8em 0.1em 0 !important;
	padding: 0 !important;
	list-style-type: none !important;
	list-style-image: none !important;
	line-height: 1.2em !important;
	font-size: 14px !important;
	font-weight: bold !important;
}
/* description and compliance */
.pl-tpl-idx-sample-tabbed p.listing-description,
.pl-tpl-idx-sample-tabbed .listing-item .compliance-wrapper p {
	float: left !important;
	margin: 0 0 .2em 0 !important;
	max-height: 52px !important;
	max-width: 370px !important;
	line-height: 17px !important;
	font-size: 14px !important;
	overflow: hidden !important;
}
.pl-tpl-idx-sample-tabbed .listing-item .compliance-wrapper p,
.pl-tpl-idx-sample-tabbed .pl-tpl-footer .compliance-wrapper p {
	font-size: .8em !important;
}
.pl-tpl-idx-sample-tabbed .listing-item .compliance-wrapper {
	float: right;
}
.pl-tpl-idx-sample-tabbed .listing-item .clear {
	clear: none;
}
.pl-tpl-idx-sample-tabbed .actions {
	float: none !important;
	position: absolute;
	bottom: 0;
	right: 0;
	margin: 0 !important;
	padding: 0 !important;
}
.pl-tpl-idx-sample-tabbed a.more-link {
	float: right !important;
	margin-left: 1em !important;
}
.pl-tpl-idx-sample-tabbed #pl_add_remove_lead_favorites,
.pl-tpl-idx-sample-tabbed .pl_add_remove_lead_favorites {
	float: right !important;
}

/* compliance -shortcode- in the footer */
.pl-tpl-idx-sample-tabbed .pl-tpl-footer .compliance-wrapper {
	margin: .5em 0;
	padding: 0;
}

/* controls */
.pl-tpl-idx-sample-tabbed .sort_item {
	float: left;
	margin: 0 2em 0 0;
	padding: 0;
}
.pl-tpl-idx-sample-tabbed .sort_item label {
	display: inline;
	padding: 0;
	line-height: 20px;
	font-size: 14px;
}
.pl-tpl-idx-sample-tabbed .sort_item select {
	margin: 0;
}
.pl-tpl-idx-sample-tabbed .dataTables_length {
	float: right;
	margin: -24px 0 0 0;
	padding: 0;
}
.pl-tpl-idx-sample-tabbed .dataTables_length label {
	line-height: 20px;
	font-size: 14px;
}
.pl-tpl-idx-sample-tabbed .dataTables_paginate a {
	margin: 0 1em 0 0;
	padding: 0;
	font-weight: 500;
}
.pl-tpl-idx-sample-tabbed .dataTables_paginate a.paginate_active {
	font-weight: 800;
}

/* table formatting */
.pl-tpl-idx-sample-tabbed #container {
	width: 100% !important;
}
.pl-tpl-idx-sample-tabbed table {
	margin: 0 !important;
	border: 0 !important;
	width: 100% !important;
}
.pl-tpl-idx-sample-tabbed table tr {
	float: none !important;
	border: none !important;
	margin: 0 !important;
	background: transparent !important;
}
.pl-tpl-idx-sample-tabbed table td {
	border: 1px solid #dfdfdf !important;
	border-width: 0 0 1px 0 !important;
	padding: 0 !important;
	background: transparent !important;
}
/* styling for alternate rows */
.pl-tpl-idx-sample-tabbed table tr.odd td {
}
.pl-tpl-idx-sample-tabbed table tr.even td {
}



/* Map */
#idx_map_item {
	display: block !important;
}
.pl_idx_tab_listings #idx_map_item,
.pl_idx_tab_gallery #idx_map_item {
	position: absolute;
	left: -1000px;
}
.pl-tpl-idx-sample-tabbed .loading_overlay,
.pl-tpl-idx-sample-tabbed .empty_overlay,
.pl-tpl-idx-sample-tabbed .custom_google_map {
	width: 100% !important;
}



/* Listings View */

.pl_idx_tab_listings .listing-item-details {
	width: 60% !important;
}



/* Map View */

.pl_idx_tab_map .placester_properties tr {
}
.pl_idx_tab_map .placester_properties td {
}
.pl_idx_tab_map .listing-item {
	padding: 5px !important;
	position: relative;
}
.pl_idx_tab_map .listing-thumbnail {
	float: left;
	width: 5em !important;
	height: 3.5em !important;
}
.pl_idx_tab_map .listing-thumbnail img {
	width: 100% !important;
	height: 100% !important;
}
.pl-tpl-idx-sample-tabbed .pl_idx_tab_map p.h4 {
	font-size: 16px !important;
}
.pl_idx_tab_map .listing-item-details {
}
.pl_idx_tab_map .listing-item-address {
}
.pl_idx_tab_map .basic-details ul {
	margin: 0 !important;
	max-width: none !important;
}
.pl_idx_tab_map .basic-details li {
	float: left;
	font-weight: normal !important;
}
.pl_idx_tab_map .basic-details li.basic-details-price {
}
.pl_idx_tab_map .actions,
.pl_idx_tab_map .listing-description,
.pl_idx_tab_map .compliance-wrapper {
	display: none !important;
}



/* Gallery View */
.pl_idx_tab_gallery .placester_properties tr {
	display: block !important;
	float: left !important;
	width: 32.9% !important;
}
.pl_idx_tab_gallery .placester_properties td {
	border: none !important;
}
.pl_idx_tab_gallery .listing-item {
	padding: 10px !important;
	position: relative;
}
.pl_idx_tab_gallery .listing-thumbnail {
	float: none !important;
	margin: 0 !important;
	width: 100% !important;
}
.pl_idx_tab_gallery .listing-item-details {
}
.pl_idx_tab_gallery .listing-item-address {
	margin: 15px 0 0 0 !important;
	height: 45px;
	font-size: 14px !important;
}
.pl_idx_tab_gallery .basic-details li {
	display: none;
}
.pl_idx_tab_gallery .basic-details li.basic-details-price {
	display: block;
	position: absolute;
	left: 5px;
	bottom: 70px;
	margin: 0 !important;
	padding: .5em !important;
	background: #3f3f3f;
	font-size: 12px !important;
	color: #fff;
}
.pl_idx_tab_gallery .actions,
.pl_idx_tab_gallery .listing-description,
.pl_idx_tab_gallery .compliance-wrapper {
	display: none !important;
}
',

'snippet_body' => '
[search_form]
<div id="pl_idx">
	<ul>
		<li><a class="pl_idx_tab_listings" href="#idx_listings">Listings</a></li>
		<li><a class="pl_idx_tab_map" href="#idx_map">Map</a></li>
		<li><a class="pl_idx_tab_gallery" href="#idx_gallery">Gallery</a></li>
	</ul>
	<div id="idx_listings"></div>
	<div id="idx_map"></div>
	<div id="idx_gallery"></div>
	<div id="idx_items" class="pl_idx_tab_listings">
		<div id="idx_map_item">[search_map]</div>
		<div id="idx_listings_item">[search_listings]</div>
	</div>
</div>
',

'search_form' => '
<h3>Search Listings</h3>

<div class="form-grp">
	<h6>Location</h6>
	<div class="select-grp">
		<label>City</label>
		[cities]
	</div>
	<div class="select-grp">
		<label>State</label>
		[states]
	</div>
	<div class="select-grp">
		<label>Zipcode</label>
		[zips]
	</div>
</div>

<div class="form-grp">
	<h6>Price Range</h6>
	<div id="min_price_container" class="select-grp">
		<label>Price From</label>
		[min_price]
	</div>
	<div id="max_price_container" class="select-grp">
		<label>Price To</label>
		[max_price]
	</div>
</div>

<div class="form-grp">
	<h6>Details</h6>
	<div class="select-grp">
		<label>Bed(s)</label>
		[bedrooms]
	</div>
	<div class="select-grp">
		<label>Bath(s)</label>
		[bathrooms]
	</div>
</div>

<div class="clear"></div>
',

'search_listings' => '',

'javascript' => '
jQuery(function($) {
	$("#pl_idx").tabs({
		select: function(event, ui) {
			$("#idx_items").attr("class", ui.tab.className);
		}
	});

	$("#pl_idx").bind("tabsshow", function(event, ui) {
		if (ui.panel.id == "idx_map") {
			mapRefresh();
		}
	});

	function mapRefresh() {
		var pl_map = $("#pl_idx .custom_google_map").data("pl_map");
		// TODO: remove after updates to blueprint
		if (!pl_map && map) {
			pl_map = map;
		}
		if (pl_map) {
			google.maps.event.trigger(pl_map.map,"resize");
			pl_map.center_on_markers();
		}
	}
});',

'before_widget' => '
<div class="pl-tpl-idx-sample-tabbed">',

'after_widget' => '</div>',

);
