<?php 
/**
 * Generates preview metabox used in the shortcode edit view
 */
?>
<div id="pl-previewer-metabox-id" class="postbox ">
	<h3 class="hndle"><span>Preview</span></h3>
	<div class="inside">
		<div id='preview-wrapper'>
			<div id='preview-meta-widget'>
				<img id="preview_load_spinner" src="<?php echo PL_PARENT_URL . 'images/preview_load_spin.gif'; ?>" alt="Widget options are Loading..." width="30px" height="30px" style="margin-left: 100px; margin-top: 100px;" />
			</div>
			<div id="pl-review-wrapper">
				<a id="pl-review-link" href="" style="display:none;">Open Preview in a popup</a>
				<div id="pl-review-popup" class="dialog" style="display: none;">Loading preview...</div>
			</div>
		</div>
	</div>
</div>