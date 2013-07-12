<?php
	// Render a contact's details...
	if (isset($crm_id) && isset($contact_id)) { return; }

	$crm_obj = PL_CRM_Controller::getCRMInstance($id);
	$contact_data = $crm_obj->getContactDetails($contact_id);
?>

<div class="contact-details-box">
</div>