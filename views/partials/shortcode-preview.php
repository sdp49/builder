<?php
/**
 * Generates preview metabox used in the shortcode edit view
 */
?>
<div id="pl-previewer-metabox-id" class="postbox ">
	<h3 class="hndle"><span>Preview</span></h3>
	<div class="inside">
		<div id='preview-wrapper'>
			<div class="preview_load_spinner" style="display:none;">
				<img src="<?php echo PL_PARENT_URL . 'images/preview_load_spin.gif'; ?>" alt="Widget options are Loading..." />
			</div>
			<div id='preview_meta_widget'><?php echo $iframe;?></div>
			<div id="pl-review-wrapper">
				<a id="pl-review-link" href="" style="display:none;">Open Preview in a popup</a>
				<div id="pl-review-popup" class="dialog" style="display: none;">Loading preview...</div>
			</div>
		</div>
	</div>
</div>