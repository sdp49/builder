<?php
// Ensure the var containing info about the active CRM is a valid array...
if (!is_array($crm_info)) { return; }

// Get an instance of the CRM's class library...
$crm_class = $crm_info["class"];
$crm_obj = new $crm_class();

// Get first 10 contacts...
// $contacts = $crm_obj->getContacts();

?>

<div class="crm-browse-box">
	<div class="browse-logo">
		<img src="<?php echo $crm_info["logo_img"]; ?>" />
	</div>
	<h3><?php echo var_export($crm_info); ?> is currently active...</h3>
</div>