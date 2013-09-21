<?php

$template = array(

'title' => 'Sample Tabbed Layout',

'css' => '
.pl-tpl-idx-twentyten .loading_overlay,
.pl-tpl-idx-twentyten .empty_overlay,
.pl-tpl-idx-twentyten .custom_google_map {
	width: 100% !important;
}
/* Listings */
.pl_idx_tab_listings .pl-tpl-sl-twentyten .listing-item-details {
	width: 60% !important;
}		
/* Map */
.pl_idx_tab_listings #idx_map_item,
.pl_idx_tab_gallery #idx_map_item {
	display: block !important;
    position: absolute;
    left: -1000px;
}
/* Gallery */
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .placester_properties tr {
	display: block !important;
	float: left !important;
	width: 32.9% !important;
}
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .placester_properties td {
	border: none !important;
}
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .listing-item {
	padding: 10px !important;
	position: relative;
}
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .listing-thumbnail {
	float: none !important;
	margin: 0 !important;
	width: 100% !important;
}
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .listing-item-details {
}
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .listing-item-address {
	margin: 15px 0 0 0 !important;
	height: 45px;
	font-size: 14px !important;
}
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .basic-details li {
	display: none;
}
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .basic-details li.basic-details-price {
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
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .actions,
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .listing-description,
.pl_idx_tab_gallery .pl-tpl-sl-twentyten .compliance-wrapper {
	display: none !important;
}
/* Map */
.pl_idx_tab_map .pl-tpl-sl-twentyten .placester_properties tr {
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .placester_properties td {
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .listing-item {
	padding: 5px !important;
	position: relative;
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .listing-thumbnail {
	float: left;
	width: 5em !important;
	height: 3.5em !important;
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .listing-thumbnail img {
	width: 100% !important;
	height: 100% !important;
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .listing-item-details {
	float: left;
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .listing-item-address {
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .basic-details ul {
	max-width: none !important;
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .basic-details li {
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .basic-details li.basic-details-price {
}
.pl_idx_tab_map .pl-tpl-sl-twentyten .actions,
.pl_idx_tab_map .pl-tpl-sl-twentyten .listing-description,
.pl_idx_tab_map .pl-tpl-sl-twentyten .compliance-wrapper {
	display: none !important;
}
',

'snippet_body' => '
[search_form]
<div id="pl_idx">
	<ul>
		<li><a class="pl_idx_tab_map" href="#idx_map">Map</a></li>
		<li><a class="pl_idx_tab_listings" href="#idx_listings">Listings</a></li>
		<li><a class="pl_idx_tab_gallery" href="#idx_gallery">Gallery</a></li>
	</ul>
	<div id="idx_map"></div>
	<div id="idx_listings"></div>	
	<div id="idx_gallery"></div>	
	<div id="idx_items" class="pl_idx_tab_map">	
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
',

'search_listings' => '',

'before_widget' => '
<script>
jQuery(function($) {
	$(\'#pl_idx\').tabs({
		select: function(event, ui) {
			$(\'#idx_items\').attr(\'class\', ui.tab.className);
		}              
	});
});		
</script>
<div class="pl-tpl-idx-twentyten">',

'after_widget' => '</div>',

);
