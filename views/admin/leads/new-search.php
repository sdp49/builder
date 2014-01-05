<?php 

$lead_details = PL_UI_Saved_Search::get_lead_details_by_id($_GET['id']);

 ?>

<h2>Create a New Search for <?php echo $lead_details['full_name'] ?></h2>