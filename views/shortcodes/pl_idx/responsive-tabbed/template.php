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

'snippet_body' => '
<div id="pl_idx">
  
  [search_form]
  
  <div id="pl_idx-tabs-wrapper" class="pl_idx-tabs-wrapper">

    <!-- tabs -->
    <ul id="pl_idx-tabs" class="pl_idx-tabs">
      <li class="active">
        <a class="pl_idx_tab_listings" href="#" data-class="pl_col-111">List</a>
      </li>
      <li>
        <a class="pl_idx_tab_map" href="#" data-class="pl_col-122">Map</a>
      </li>
      <li>
        <a class="pl_idx_tab_gallery" href="#" data-class="pl_col-122">Gallery</a>
      </li>
    </ul>

    <!-- tab content -->
    <div id="idx_results" class="idx_results">
      <div id="idx_map_item">[search_map]</div>
      <div id="idx_results-list" class="pl_listings pl_col-111">[search_listings]</div>
    </div>

  </div>

</div>
',

'javascript' => '
jQuery(function($) {

  // Hide Map on initial load
  $("#idx_map_item").hide();

  // Tabbing
  $("#pl_idx-tabs li").live("click", function(e) {
    e.preventDefault();

    // Toggle .active for styling
    $(this).addClass("active");
    $(this).siblings().removeClass("active");

    // Change content classes
    $("#idx_results #idx_results-list").removeClass();
    var new_classes = $(this).children("a").attr("data-class");
    $("#idx_results #idx_results-list").addClass(new_classes + " " + "pl_listings");

    // Show/hide map
    if ($(this).children("a").attr("class") == "pl_idx_tab_map") {
      $("#idx_map_item").show();
      mapRefresh();
    } else {
      $("#idx_map_item").hide();
    }

  });

	function mapRefresh() {
		var pl_map = $("#pl_idx-tabs .custom_google_map").data("pl_map");
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
