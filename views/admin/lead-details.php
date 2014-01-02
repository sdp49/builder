<?php 

$lead_details = PL_UI_Saved_Search::get_lead_details_by_id($_GET['id']);

// var_dump($lead_details);

 ?>
<h2>Details for <?php echo $lead_details['full_name'] ?></h2>
<ul class="person-details">
	<li>Phone: <?php echo $lead_details['phone'] ?></li>
	<li>Date Created: <?php echo $lead_details['created'] ?></li>
	<li>Last Updated: <?php echo $lead_details['updated'] ?></li>
	<li class="last"># of Saved Searches: <?php echo $lead_details['saved_searches'] ?></li>
</ul>


<div class="both"></div>
<h2>Saved Searches</h2>
<div id="container">
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
<div style="display:none" id="delete_listing_confirm">
  <div id="delete_response_message"></div>
  <div>Are you sure you want to permanently delete <span id="delete_listing_address"></span>?</div>  
</div>
