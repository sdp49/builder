<?php $lead_details = PL_UI_Saved_Search::get_lead_details_by_id($_GET['id']); ?>

<input id="lead_id" type="hidden" value="<?php echo $_GET['id'] ?>" >
<h2 class="person-name-contact">
  <span class="name">Edit <?php echo $lead_details['full_name'] ?></span>  
</h2>
<form>
	
</form>