<?php
/**
 * Displays meta box used in the shortcode template edit view
 */

$title = empty($title)?'':$title; // template name
$pl_post_type = empty($pl_post_type)?'pl_form':$pl_post_type; // shortcode type we are making a template for
$data = empty($data)?array():$data; // current template values

$data = wp_parse_args($data, array('before_widget'=>'', 'after_widget'=>'', 'snippet_body'=>'', 'widget_css'=>''));
$shortcode = '';
$pl_shortcode_types = PL_Shortcode_CPT::get_shortcodes(); 
?>

<div class="postbox ">
	<h3>Create Shortcode Template</h3>

	<div class="inside">
	
		<!-- Template Name -->
		<section class="row-fluid">

			<div class="span2">
				<label for="edit-template-name" class="section-label">Template Name:</label>
			</div>

			<div class="span10">
				<input type="text" id="title" class="snippet_name new_snippet_name" title="<?php _e('Please enter a name for this shortcode template.')?>" value="<?php echo $title?>" />
			</div>

		</section>
		<!-- /Template Name -->

		<!-- Template Type -->
		<section class="row-fluid">

			<div class="span2">
				<label for="tpl_post_type" class="section-label">Template Type:</label>
			</div>

			<div class="span10">
				<select id="pl_sc_tpl_post_type">
						<?php 
						$shortcode_ref = array();
						foreach( $pl_shortcode_types as $post_type => $values ):
							$link_class = $selected = '';
							if ($post_type == $pl_post_type) {
								$link_class = 'selected_type';
								$selected = 'selected="selected"';
								$shortcode = $values['shortcode'];
							}
							?>
							<option id="pl_sc_tpl_shortcode_<?php echo $values['shortcode']; ?>" class="<?php echo $link_class; ?>" value="<?php echo $values['post_type']; ?>" <?php echo $selected; ?>>
								<?php echo $values['title']; ?>
							</option>
							<?php
							// get help text, use later
							$shortcode_ref[$post_type] = PL_Router::load_builder_partial('shortcode-ref.php', array('shortcode' => $values['shortcode']), true);
						endforeach;
						?>
				</select>
			</div>

		</section>
		<!-- /Template Type -->

		<!-- Template Contents -->
		<section class="row-fluid sc-meta-section">

			<!-- Template HTML/CSS -->
			<div class="span6">

				<!-- Use existing template lightbox -->
				<a id="popup_existing_template" href="#">Use existing template as a base for this new template</a>

				<!-- Add HTML -->
				<label for="snippet_body">HTML</label>
				<div class="edit-box">
					<textarea id="snippet_body" name="snippet_body" class="snippet"><?php echo $data['snippet_body']?></textarea>
				</div>

				<!-- Add CSS -->
				<label for="widget_css">CSS</label>
				<div class="edit-box">
					<textarea id="widget_css" name="widget_css" class="snippet"><?php echo $data['widget_css']?></textarea>
				</div>

				<!-- Add Content Before Widget -->
				<a href="#" id="before_widget_wrapper_toggle" class="toggle clearfix">Add content before the widget</a>
				<div id="before_widget_wrapper" class="edit-box" style="display:none;">
					<textarea id="before_widget" name="before_widget" class="snippet"><?php echo $data['before_widget']?></textarea>
				</div>
				
				<!-- Add Content After Widget -->
				<a href="#" id="after_widget_wrapper_toggle" class="toggle clearfix">Add content after the widget</a>
				<div id="after_widget_wrapper" class="edit-box" style="display:none;">
					<textarea id="after_widget" name="after_widget" class="snippet"><?php echo $data['after_widget']?></textarea>
				</div>

			</div>

			<!-- Search Sub-Shortcodes -->
			<div id="subshortcodes" class="span4">
				<div style="display: none;">
					<label for="search-subshortcodes">Sub-Shortcodes</label> 
					<input type="text" placeholder="search sub-shortcodes" />
				</div>			
				<?php foreach($shortcode_ref as $post_type => $shortcode_help ): ?>
					<div class="shortcode_block <?php echo $post_type?>" style="display: none;">
					<?php echo $shortcode_help; ?>
					</div>
				<?php endforeach;?>
			</div>
			
		</section><!-- /Template Contents -->

		<?php wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );?>
		<input type="hidden" name="shortcode" value="<?php echo $shortcode ?>" />
		
		<div class="clear"></div>
			
	</div><!-- /.inside -->

</div><!-- /.postbox -->
