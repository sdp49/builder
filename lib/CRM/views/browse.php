<?php
// Ensure the var containing info about the active CRM is a valid array...
if (!is_array($crm_info)) { return; }

// Make CRM vars more accessible...
extract($crm_info);

// Get an instance of the CRM's class library...
$crm_obj = new $class();

// Get first 10 contacts...
$contacts = $crm_obj->getContacts();

?>

<div class="crm-browse-box">
	<div class="">
		<a href="#" class="deactivate-button">Choose a different CRM</a>
	</div>

	<div class="browse-logo">
		<img src="<?php echo $logo_img; ?>" />
	</div>
	<h3><?php echo $crm_info["id"]; ?> is currently active...</h3>
</div>