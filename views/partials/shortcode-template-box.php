<?php
/**
 * Displays meta box used in the shortcode template edit view
 */

$pl_shortcode_types = PL_General_Widget_CPT::$post_types; 
$pl_shortcode_codes = PL_General_Widget_CPT::$codes;

$current_type = '';
?>

<div id="pl-controls-metabox-id"
	class="postbox ">
	<h3>Create Shortcode Template</h3>

	<div id="edit-template-metabox-inner" class="inside">

		<!-- Template Name -->
		<section id="edit-template-choose-name" class="row-fluid">

			<div class="span2">
				<p class="section-label">Template Name:</p>
			</div>

			<div class="span10">
				<input type="text" id="edit-template-name" />
			</div>

		</section>
		<!-- /#edit-template-choose-name -->

		<!-- Template Name -->
		<section id="edit-template-choose-template" class="row-fluid">

			<div class="span2">
				<p class="section-label">Template Type:</p>
			</div>

			<div class="span10">
				<select class="chosen">
					<?php foreach($pl_shortcode_types as $type=>$name):?>
					<option <?php echo ($current_type==$type?'selected="selected"':'')?>><?php echo $name?></option>
					<?php endforeach;?>
				</select>
			</div>

		</section>
		<!-- /#edit-template-choose-template -->

		<hr class="clearfix" />

		<!-- Template Contents -->
		<section id="edit-template-contents" class="row-fluid">

			<!-- Template HTML/CSS -->
			<div id="edit-html-css" class="span8">

				<!-- Use existing template lightbox -->
				<a href="#">Use existing template as a base for this new template</a>

				<!-- Add HTML -->
				<label for="html-textarea">HTML</label>
				<textarea id="html-textarea"></textarea>

				<!-- Add CSS -->
				<label for="css-textarea">CSS</label>
				<textarea id="css-textarea"></textarea>

				<!-- Add Content Before Widget -->
				<a href="#" id="toggle-before-widget" class="clearfix">Add content
					before the widget</a>
				<textarea id="before-widget"></textarea>

				<!-- Add Content After Widget -->
				<a href="#" id="toggle-after-widget" class="clearfix">Add content
					after the widget</a>
				<textarea id="after-widget"></textarea>

				<!-- Save Button -->
				<button id="save-template" class="green">Save</button>

			</div>

			<!-- Search Sub-Shortcodes -->
			<div id="subshortcodes" class="span4">

				<label for="search-subshortcodes">Sub-Shortcodes</label> 
				<input type="text" placeholder="search sub-shortcodes" />
				<select multiple>
				</select>
			</div>
			
		</section>
		<!-- /#edit-template-html-css -->

	</div>
	<!-- /edit-template-metabox-inner -->

</div>
<script type="text/javascript">
jQuery("select.chosen").chosen({no_results_text: "No results matched"});

jQuery(document).ready(function() {

	$('#toggle-before-widget').click(function() {
		if ($('textarea#before-widget').hasClass('is-visible')) {
			$('textarea#before-widget').removeClass('is-visible');
		} else {
			$('textarea#before-widget').addClass('is-visible');
		};
	});

	$('#toggle-after-widget').click(function() {
		if ($('textarea#after-widget').hasClass('is-visible')) {
			$('textarea#after-widget').removeClass('is-visible');
		} else {
			$('textarea#after-widget').addClass('is-visible');
		};
	});

	if ( $('textarea#before-widget').val() ) {
		$('textarea#before-widget').addClass('is-visible');
	};
	
	if ( $('textarea#after-widget').val() ) {
		$('textarea#after-widget').addClass('is-visible');
	};

});
</script>