<?php
/**
 * Displays main shortcode edit meta box used in the shortcode edit view
 */

// get all CPT custom field values
$values = get_post_custom( $post->ID );

// read the post type
$pl_post_type = empty( $values['pl_post_type'] ) ? '' : $values['pl_post_type'][0];
$pl_cpt_template = empty( $values['pl_cpt_template'] ) ? '' : $values['pl_cpt_template'][0];
$pl_shortcode_types = PL_General_Widget_CPT::get_shortcodes();

$options_class = $filters_class = '';
?>
<div class="postbox">
	
	<h3>Create Shortcode</h3>
	
	<div class="inside">	

		<!-- Type and Template -->
		<div>

			<!-- Type -->
			<section class="post_types_list_wrapper row-fluid">
				
				<div class="span2">
					<label class="section-label" for="shortcode_type">Type:</label>
				</div>

				<div class="span9">

					<select id="pl_sc_shortcode_type" name="shortcode_type" class="">
						
						<option id="pl_sc_shortcode_undefined" value="undefined">Select</option>
						
						<?php
						foreach( $pl_shortcode_types as $shortcode_type => $sct_args ):
							$link_class = ($shortcode_type == $pl_post_type) ? 'selected_type' : '';
							$selected = ( !empty($link_class) ) ? 'selected="selected"' : '';
							?>
							<option id="pl_sc_shortcode_<?php echo $sct_args['shortcode']; ?>" class="<?php echo $link_class; ?>" value="<?php echo $shortcode_type; ?>" <?php echo $selected; ?>>
								<?php echo $sct_args['title']; ?>
							</option>
							<?php
							// build our class list for the Options and Templates sections in this loop
							if (!empty($sct_args['options'])) {
								$options_class .= ' '.$shortcode_type;
							} 
							if (!empty($sct_args['filters'])) {
								$filters_class .= ' '.$shortcode_type;
							} 
						endforeach;
						?>
					</select>

				</div>
			
			</section><!-- /.post_types_list_wrapper -->

			<!-- Template / Layout -->
			<section id="choose_template" style="display:none;">
				<div class="span2">
					<label class="section-label" for="pl_template">Template:</label>
				</div>
				<div class="span6">
					<?php foreach( $pl_shortcode_types as $shortcode_type => $sct_args ): ?>
						<?php if(!empty($sct_args['options']['pl_cpt_template'])):?>
							<div class="pl_template_block <?php echo $shortcode_type; ?>" id="<?php echo $sct_args['shortcode'];?>_template_block" style="display: none;">
								<?php
								$value = isset( $values['pl_cpt_template'] ) ? $values['pl_cpt_template'] : $sct_args['options']['pl_cpt_template']['default'];
								PL_Router::load_builder_partial('shortcode-template-list.php', array(
											'shortcode' => $sct_args['shortcode'],
											'post_type' => $shortcode_type,
											'select_name' => $shortcode_type.'[pl_cpt_template]',
											'class' => '',
											'value' => $value,
									)
								);
								?>
							</div>
						<?php endif;?>
					<?php endforeach;?>
					<div class="edit-sc-template-edit">
						<a id="edit_sc_template_create" href="" id="create-new-template-link">(create new)</a>
						<a id="edit_sc_template_edit" href="" id="create-new-template-link" style="display:none;">(edit)</a>
					</div>
				</div>
			</section><!-- /edit-sc-choose-template -->

		</div><!-- /#post_types_list -->


		<!-- Options / Filters -->
		<div id="widget_meta_wrapper"  class="sc-meta-section" style="display: none;">

			<div class="pl_widget_block <?php echo $options_class;?>">
				<div>
					<h3>Options:</h3>
				</div>
				<?php
				// get meta values from custom fields
				// fill POST array for the forms (required after new widget is created)
				foreach( $pl_shortcode_types as $shortcode_type => $sct_args ) {
					foreach($sct_args['options'] as $field => $f_args) {
						if ($field == 'pl_cpt_template') {
							// template field already handled
							continue;
						}
						$value = isset( $values[$field] ) ? $values[$field][0] : '';
						if( !empty( $value ) && empty( $_POST[$field] ) ) {
							$_POST[$shortcode_type][$field] = $value;
						}
						else {
							$_POST[$shortcode_type][$field] = $f_args['default'];
						}
						$f_args['css'] = (!empty($f_args['css']) ? $f_args['css'].' ' : '') . $shortcode_type;
						PL_Form::item($field, $f_args, 'POST', $shortcode_type, 'general_widget_', true);
					}
				}
				?>
			</div>

			<div class="pl_widget_block <?php echo $filters_class;?>">
				<div>
					<h3>Filters:</h3>
				</div>
				<?php
				// get meta values from custom fields
				// fill POST array for the forms (required after new widget is created)
				foreach( $pl_shortcode_types as $shortcode_type => $sct_args ) {
					if (!empty($sct_args['filters'])) {
						?>
						<div class="pl_widget_block <?php echo $shortcode_type?>">
						<?php foreach($sct_args['filters'] as $field => $f_args) {
							if ($f_args['type'] == 'subgrp') {
								echo '<h4>'.$f_args['label'].'</h4>';
								$grpval = isset( $values[$field] ) ? $values[$field][0] : array();
								foreach($f_args['subgrp'] as $subfield => $sf_args) {
									$value = isset( $grpval[$subfield] ) ? $grpval[$subfield] : '';
									if( !empty( $value ) && empty( $_POST[$field][$subfield] ) ) {
										$_POST[$shortcode_type][$field][$subfield] = $value;
									}
									else {
										$_POST[$shortcode_type][$field][$subfield] = $sf_args['default'];
									}
									$sf_args['css'] = (!empty($sf_args['css']) ? $sf_args['css'].' ' : '') . $shortcode_type;
									PL_Form::item($subfield, $sf_args, 'POST', $shortcode_type.'['.$field.']', 'general_widget_', true);
								}
							}
							else {
								$value = isset( $values[$field] ) ? $values[$field][0] : '';
								if( !empty( $value ) && empty( $_POST[$field] ) ) {
									$_POST[$shortcode_type][$field] = $value;
								}
								else {
									$_POST[$shortcode_type][$field] = $f_args['default'];
								}
								$f_args['css'] = (!empty($f_args['css']) ? $f_args['css'].' ' : '') . $shortcode_type;
								PL_Form::item($field, $f_args, 'POST', $shortcode_type, 'general_widget_', true);
							}
						}?>
						</div>
						<?php
					}
				}
				?>				
			</div><!-- /.pl_widget_block -->

			<?php wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );?>
			
			<div class="clear"></div>
		
		</div> <!-- /#widget-meta-wrapper -->
	</div><!-- /.inside -->
</div><!-- /.postbox -->