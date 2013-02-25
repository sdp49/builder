<div class="googleplus-wrapper">
	<form id="pls_settings_form" action="options.php" method="POST">
			<p>Enter your Google+ account ID</p>
			<?php settings_fields( self::$social_setting_key ) ?>
			<?php do_settings_sections( 'placester_social' ) ?>
			
			<input type="submit" value="Save" />
	</form> <!-- end of #dxtemplate-form -->
</div>