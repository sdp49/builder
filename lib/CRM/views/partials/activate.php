<?php
// Returns activation functionality for turning on an integrated CRM.
//
// NOTE: $id and $api_key must be set...

if (empty($id)) { return; }

// Populate the CRM info object if it's not set...
if (empty($api_key)) {
	$obj = PL_CRM_Controller::getCRMInstance($id);
	$api_key = $obj->getAPIKey();
}

?>
<div class="activate-crm-box">
	<div>
		API Key set to: <span><?php echo $api_key; ?></span>
		<a href="#" id="reset_<?php echo $id ?>" class="reset-creds-button">Enter new API Key</a>
	</div>
	<div>
		<a href="#" id="activate_<?php echo $id ?>" class="activate-button">Active CRM</a>
	</div>	
</div>