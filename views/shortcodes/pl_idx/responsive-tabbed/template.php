<?php

$template = array(

'title' => 'Responsive Tabbed Template',

'css' => '
/* Hide the map if not in map view */
#idx_list #idx_map_item,
#idx_gallery #idx_map_item {
	display: none;
}
',

'snippet_body' => '
[search_form]
<div id="pl_idx">
	<!-- tabs -->
	<ul>
		<li><a class="pl_idx_tab_listings" href="#idx_list">Listings</a></li>
		<li><a class="pl_idx_tab_map" href="#idx_map">Map</a></li>
		<li><a class="pl_idx_tab_gallery" href="#idx_gallery">Gallery</a></li>
	</ul>
	<!-- tab panels, everything starts in the first panel -->
	<div id="idx_list" class="pl_listings pl_col-111">
		<div id="idx_items" class="pl_idx_tab_listings">
			<div id="idx_map_item">[search_map]</div>
			<div id="idx_list_item" class="pl_listings">[search_listings]</div>
		</div>
	</div>
	<div id="idx_map" class="pl_listings pl_col-122"></div>
	<div id="idx_gallery" class="pl_listings pl_col-122"></div>
</div>
',

'search_form' => '
<div class="pl_form pl_col-124">
	
    <div class="pl_form-item pl_col no-search">
      <label>Min Beds</label>
      [min_beds]
    </div>

    <div class="pl_form-item pl_col hide-tablet no-search">
      <label>Min Baths</label>
      [min_baths]
    </div>

    <div class="pl_form-item pl_col hide-tablet no-search">
      <label>Min Price</label>
      [min_price]
    </div>

    <div class="pl_form-item pl_col no-search">
      <label>Max Price</label>
      [max_price]
    </div>

    <div class="pl_form-item pl_col no-search">
      <label>Purchase Type</label>
      [purchase_types]
    </div>

    <div class="pl_form-item pl_col">
      <label>City</label>
      [cities]
    </div>

    <div class="pl_form-item pl_col">
      <label>State</label>
      [states]
    </div>

    <div class="pl_form-item pl_col">
      <label>Zip Code</label>
      [zips]
    </div>
  
    <div class="pl_form-group pl_col pl_form-group--right">
      <div class="pl_form-item pl_form-item--btn">
        <input type="submit" value="Search" />
      </div>
    </div>
		
	<div class="pl_clearfix"></div>

</div>	
',

'search_listings' => '
<div class="pl_listing pl_col">
      
    <div class="pl_listing-inner">

      <p class="pl_listing-address">
        <a href="[url]">[full_address]</a>
      </p>
      
      <div class="pl_listing-img-wrapper">
        <a href="[url]">[image width=300]</a>
        <div class="pl_listing-fav">
          <a href="#">Save</a>
        </div>
      </div>

      <div class="pl_listing-info">

        <p class="pl_listing-meta">
          <span class="pl_listing-price">[price]</span>
          <span class="pl_listing-beds">[beds] beds</span>
          <span class="pl_listing-baths">[baths] baths</span>
          <span class="pl_listing-sqft">[sqft] sqft</span>
        </p>

        <p class="pl_listing-desc">[desc]</p>

        <div class="pl_listing-compl">[compliance]</div>
      
      </div>
    
    </div>

  </div>
',

'javascript' => '
jQuery(function($) {
	// when a tab is selected, move the listings to the new tab
	$("#pl_idx").tabs({
		select: function(event, ui) {
			var idx_items = $("#idx_items").detach();
			$(ui.panel).append(idx_items);
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

// 'before_widget' => '
// <div class="pl-tpl-idx-responsive-tabbed">',

// 'after_widget' => '</div>',

'sc_api_ver' => 1,

'tmpl_ver' => '1.0',

'group' => 'Index',

'description' => 'This search page includes list, gallery and map views in a search page.',

'keywords' => 'tabs,map,list,gallery',

);
