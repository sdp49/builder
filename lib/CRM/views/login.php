<?php

// Get details for registered CRM providers...
$crm_list = PL_CRM_Controller::$registeredCRMList;

?>

<div>
	<?php foreach ($crm_list as $id => $info): ?>
		<div id="<?php echo $id; ?>_box" class="">
			<img src="<?php echo $info["logo_img_path"]; ?>" />
			<?php
				$crm_class = $info["class"];
				$crm_obj = new $crm_class();

				// Find out if this particular CRM has been "registered" 
				// (i.e., user has already entered account credentials for this provider)
				
			?>
		</div>
	<?php endforeach; ?>
</div>

