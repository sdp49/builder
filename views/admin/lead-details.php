<?php 

$lead_details = PL_UI_Saved_Search::get_lead_details_by_id($_GET['id']);

// var_dump($lead_details);

 ?>
<h2 class="person-name-contact">
  <span class="name"><?php echo $lead_details['full_name'] ?></span>  
  <span class="phone"><?php echo $lead_details['phone'] ?></span>
  <span class="email"><a mailto="<?php echo $lead_details['email'] ?>"><?php echo $lead_details['email'] ?></a></span>
  <span class="created">(Created on: <?php echo $lead_details['created'] ?>)</span>
</h2>

<div class="both"></div>

<div id="container" class="saved-searches">
  <p class="saved-searches-title">Saved Searches (<?php echo $lead_details['saved_searches'] ?>)</p>
  <table id="placester_saved_search_list" class="widefat post fixed placester_properties" cellspacing="0">
    <thead>
      <tr>
        <th><span>Date Created</span></th>
        <th><span>Search Name</span></th>
        <th><span>Fields Saved</span></th>
        <th><span>Last Updated</span></th>
        <th><span># of Saved Searches</span></th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>

<div class="both"></div>

<div id="container" class="favorite-listings">
  <p class="saved-searches-title">Favorite Listings (<?php echo $lead_details['favorited_listings']  ?>)</p>
  <table id="placester_favorite_listings_list" class="widefat post fixed placester_properties" cellspacing="0">
    <thead>
      <tr>
        <th><span></span></th>
        <th><span>Address</span></th>
        <th><span>Beds</span></th>
        <th><span>Baths</span></th>
        <th><span>Price</span></th>
        <th><span>Sqft</span></th>
        <th><span>MLS ID</span></th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>


<!-- <div style="display:none" id="delete_listing_confirm">
  <div id="delete_response_message"></div>
  <div>Are you sure you want to permanently delete <span id="delete_listing_address"></span>?</div>  
</div> -->
