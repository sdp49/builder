<?php
/**
 * Displays meta box used in the shortcode template edit view
 */

$title = empty($title)?'':$title; // template name
$shortcode = empty($shortcode)?'':$shortcode; // shortcode type we are making a template for
$values = empty($values)?array():$values; // current template values
$pl_shortcodes = PL_Shortcode_CPT::get_shortcodes();
?>

<div class="postbox ">
	<h3>Create Shortcode Template</h3>

	<div class="inside">
	
		<!-- Template Name -->
		<section class="row-fluid">

			<div class="span2">
				<label for="pl_tpl_edit_title" class="section-label">Template Name:</label>
			</div>

			<div class="span10">
				<input type="text" id="pl_tpl_edit_title" class="snippet_name new_snippet_name" name="title" title="<?php _e('Please enter a name for this shortcode template.')?>" value="<?php echo $title?>" />
			</div>

		</section>
		<!-- /Template Name -->

		<!-- Template Type -->
		<section class="row-fluid">

			<div class="span2">
				<label for="pl_sc_tpl_shortcode" class="section-label">Template Type:</label>
			</div>

			<div class="span10">
				<select id="pl_sc_tpl_shortcode" name="shortcode">
						<?php 
						$shortcode_refs = array();
						foreach( $pl_shortcodes as $pl_shortcode => $sct_args ):
							$link_class = $selected = '';
							if ($shortcode == $pl_shortcode) {
								$link_class = 'selected_type';
								$selected = 'selected="selected"';
							}
							?>
							<option id="pl_sc_tpl_shortcode_<?php echo $pl_shortcode; ?>" class="<?php echo $link_class; ?>" value="<?php echo $pl_shortcode; ?>" <?php echo $selected; ?>>
								<?php echo $sct_args['title']; ?>
							</option>
							<?php
							// get help text, use later
							$shortcode_refs[$pl_shortcode] = PL_Router::load_builder_partial('shortcode-ref.php', array('shortcode' => $pl_shortcode), true);
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

				<?php
				foreach( $pl_shortcodes as $pl_shortcode => $sct_args ) {?>
					<div class="pl_template_block <?php echo $pl_shortcode;?>">
					<?php
					foreach($sct_args['template'] as $field => $f_args) {
						$value = isset( $values[$field] ) ? $values[$field] : '';
						if( !empty( $value ) && empty( $_POST[$pl_shortcode][$field] ) ) {
							$_POST[$pl_shortcode][$field] = $value;
						}
						else {
							$_POST[$pl_shortcode][$field] = $f_args['default'];
						}
						$f_args['css'] = (!empty($f_args['css']) ? $f_args['css'].' ' : '') . $field;
						PL_Form::item($field, $f_args, 'POST', $pl_shortcode, 'general_widget_', true);
					}?>
					</div>
					<?php
				}
				?>
				
			</div>

			<!-- Search Sub-Shortcodes -->
			<div id="subshortcodes" class="span4">
				<div style="display: none;">
					<label for="search-subshortcodes">Sub-Shortcodes</label> 
					<input type="text" placeholder="search sub-shortcodes" />
				</div>			
				<?php foreach($shortcode_refs as $shortcode_ref => $shortcode_help ): ?>
					<div class="shortcode_block <?php echo $shortcode_ref?>" style="display: none;">
					<?php echo $shortcode_help; ?>
					</div>
				<?php endforeach;?>
			</div>
			
		</section><!-- /Template Contents -->

		<?php wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );?>
		
		<div class="clear"></div>
			
	</div><!-- /.inside -->

</div><!-- /.postbox -->
