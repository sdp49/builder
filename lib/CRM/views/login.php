<?php

// Get details for registered CRM providers...
$crm_list = PL_CRM_Controller::$registeredCRMList;

?>

<div class="crm-options-box">
	<?php foreach ($crm_list as $id => $info): ?>
		<div id="<?php echo $id; ?>-box" class="">
			<div class="logo">
				<?php $img_url = plugin_dir_url(trailingslashit(dirname(__FILE__)) . "images/{$info['logo_img']}") . $info["logo_img"]; ?>
				<img src="<?php echo $img_url; ?>" />
			</div>
			<?php
				$crm_class = $info["class"];
				$crm_obj = new $crm_class();
				
				// Find out if this particular CRM has been "integrated"
				// (i.e., user has already entered account credentials for this provider)
				$not_integrated = is_null($crm_obj->getAPIKey());
			?>
			<?php if ($not_integrated): ?>
				<?php // No creds stored for this CRM, allow user to enter them OR sign-up for a new account... ?>
				<div class="integrate-crm-box">
					<div class="enter-creds-box">
						<span>Enter your API Key:</span>
						<input id="<?php echo $id; ?>_api_key" class="api-key-field" type="text" />
						<a href="#" id="integrate_<?php echo $id; ?>" class="integrate-button">Integrate</a>
					</div>
					<div class="cred-lookup-box">
						<span> Don't know your API key?
							<a href="<?php echo $info["cred_lookup_url"]; ?>" class="api-lookup" target="blank">Find it here</a>
						</span>
					</div>						
				</div>
				<div class="sign-up-box">
					<span>Don't have an account with provider?
						<a href="<?php echo $info["referral_url"]; ?>" target="_blank">Sign-up here</a>
					</span>
				</div>
			<?php else: ?>
				<div class="activate-crm">
					<a href="#" id="activate_<?php echo $id ?>" class="activate-button">Active CRM</a>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

