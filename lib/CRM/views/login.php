<?php
// Ensure the var containing info about the registered CRM providers is a valid array...
if (!is_array($crm_list)) { return; }
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
				<?php include("partials/integrate.php"); ?>
			<?php else: ?>
				<?php include("partials/activate.php"); ?>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

