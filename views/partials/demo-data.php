<?php
	$whoami = PL_Helper_User::whoami();
	// error_log(serialize($whoami));

	$org_zip_exists = ( isset($whoami['location']) && isset($whoami['location']['postal']) && !empty($whoami['location']['postal']) );
	$user_zip_exists = ( isset($whoami['user']) && isset($whoami['user']['location']) && isset($whoami['user']['location']['postal']) && !empty($whoami['user']['location']['postal']) );

	if ( $org_zip_exists || $user_zip_exists ) {
		$zip = ( $org_zip_exists ? $whoami['location']['postal'] : $whoami['user']['location']['postal'] );
	}
?>

<div id="demo_data_wizard">
	<p class="message"></p>	
	<div class="clear"></div>
	<p>"Demo" listings from the <span><?php echo $zip; ?></span> area will be displayed in your site</p>
	<input type="hidden" id="demo_zip" name="demo_zip" value="<?php echo $zip; ?>" />
	<div>
		<!-- Map goes here... -->
		<div id="map_canvas" style="height: 100px; width: 100px; overflow: hidden"></div>
	</div>
</div>
