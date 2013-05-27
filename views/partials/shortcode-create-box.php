<?php
/**
 * Displays main shortcode edit meta box used in the shortcode edit view
 */

// get all CPT custom field values
$values = get_post_custom( $post->ID );

// read the post type
$pl_post_type = isset( $values['pl_post_type'] ) ? $values['pl_post_type'][0] : '';

$pl_shortcode_types = PL_General_Widget_CPT::$post_types; 
$pl_shortcode_fields = PL_General_Widget_CPT::$fields;

?>
<div id="pl-controls-metabox-id" class="postbox ">
	
	<h3>Create Shortcode</h3>
	
	<div id="edit-sc-metabox-inner" class="inside">	

		<!-- Type and Template -->
		<div class="meta_section">

			<!-- Type -->
			<section id="edit-sc-choose-type" class="post_types_list_wrapper row-fluid">
				
				<div class="span2">
					<label class="section-label" for="pl_post_type_dropdown">Type:</label>
				</div>

				<div class="span9">

					<select id="pl_post_type_dropdown" name="pl_post_type_dropdown" class="">
						
						<option id="pl_post_type_undefined" value="pl_post_type_undefined">Select</option>
						
						<?php 
						$num_of_post_types = count( $pl_shortcode_types );
						$i = 0;
			
						foreach( $pl_shortcode_types as $post_type => $label ):
							$i++;
							$link_class = ($post_type == $pl_post_type) ? 'selected_type' : '';
							$selected = ( !empty($link_class) ) ? 'selected="selected"' : '';
							?>
							<option id="pl_post_type_<?php echo $post_type; ?>" class="<?php echo $link_class; ?>" value="pl_post_type_<?php echo $post_type; ?>" <?php echo $selected; ?>>
								<?php echo $label; ?>
							</option>
							<?php
						endforeach;
						?>
					</select>

				</div>
			
			</section><!-- /.post_types_list_wrapper -->

			<!-- Template / Layout -->
			<section id="edit-sc-choose-template" class="row-fluid">
				<div class="span2">
					<label class="section-label" for="pl_template">Template:</label>
				</div>
				<div class="span6">
					<?php foreach( PL_General_Widget_CPT::$codes as $code => $label ): ?>
						<div class="pl_template_block" id="<?php echo $code;?>_template_block" style="display: none;">
							<?php
							PL_Router::load_builder_partial('shortcode-template-list.php', array(
										'codes' => array( $code ),
										'p_codes' => array(
											$code => $label
										),
										'select_name' => 'pl_template_' . $code,
										'class' => '',
										'value' => $values['pl_cpt_template'][0],
								)
							);
							?>
						</div>
						<?php add_action( 'pl_template_extra_styles', array( $this, 'update_template_block_styles' ) );?>
					<?php endforeach;?>
				</div>
				<div class="offset1 span3">
					<a href="<?php echo admin_url('admin.php?page=placester_shortcodes_template_edit')?>" id="create-new-template-link">(create new)</a>
				</div>
			</section><!-- /edit-sc-choose-template -->

		</div><!-- /#post_types_list -->


		<!-- Options / Filters -->
		<div id="widget-meta-wrapper" style="display: none;">

			<div class="pl_widget_block">
				<div class="pl_map pl_form pl_search_listings pl_slideshow pl_neighborhood featured_listings static_listings">
					<h3>Options:</h3>
				</div>
				<?php
				// get meta values from custom fields
				// fill POST array for the forms (required after new widget is created)
				foreach( $pl_shortcode_fields as $field => $arguments ) {
					$value = isset( $values[$field] ) ? $values[$field][0] : '';
			
					if( !empty( $value ) && empty( $_POST[$field] ) ) {
						$_POST[$field] = $value;
					}
			
					echo PL_Form::item($field, $arguments, 'POST', false, 'general_widget_');
				}
				?>

				<?php
				// get radio values for neighborhood
				$radio_def = isset( $values['radio-type'] ) ? $values['radio-type'][0] : 'state';
				$select_id = 'nb-select-' . $radio_def;
				$select_def = isset( $values[ $select_id ] ) ? $values[ $select_id ][0] : '0';
				?>
			
				<section id="pl_location_tax" class="pl_neighborhood">
				<?php $taxonomies = PL_Taxonomy_Helper::get_taxonomies();?>
				<?php foreach ($taxonomies as $slug => $label): ?>
					<section>
						<input type="radio" id="<?php echo $slug ?>" name="radio-type" value="<?php echo $slug ?>" <?php echo ($radio_def==$slug?'checked="checked"':'')?>>
						<label for="<?php echo $slug ?>"><?php echo $label ?></label>
					</section>
				<?php endforeach ?>	
				</section>
				
				<section class="pl_neighborhood">
				<?php foreach( $taxonomies as $slug => $label ): ?>
					<?php $terms = PL_Taxonomy_Helper::get_taxonomy_items( $slug ); ?>
					<div id="nb-taxonomy-<?php echo $slug;?>" class="nb-taxonomy" <?php echo ($radio_def==$slug?'':'style="display: none;"')?>>
						<select id="nb-id-select-<?php echo $slug;?>" name="nb-select-<?php echo $slug;?>">
						<?php foreach( $terms as $term ): ?>
							<option value="<?php echo $term['term_id']?>" <?php echo ($radio_def==$slug && $select_def==$term?'selected="selected"':'')?>><?php echo $term['name'] ?></option>
						<?php endforeach;?>
						</select>
					</div>
				<?php endforeach;?>
				</section>

				<section id="pl_featured_listing_block" class="featured_listings pl_slideshow">
					<?php
						include PLS_OPTRM_DIR . '/views/featured-listings.php';
				
						// Generate the popup dialog with featured			
						echo pls_generate_featured_listings_ui(array(
											'name' => 'Featured Meta',
											'desc' => '',
											'id' => 'featured-listings-type',
											'type' => 'featured_listing'
										) ,
										$pl_featured_meta_value,
										'pl_featured_listing_meta');
								 
					?>
				</section><!-- end of #pl_featured_listing_block -->
				
				
			</div><!-- /.pl_widget_block -->

			<input type="hidden" name="pl_post_type" id="pl_post_type" value="pl_map" />
				
			<?php wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );?>
			
			<div class="clear"></div>
		
		</div> <!-- /#widget-meta-wrapper -->
	</div><!-- /.inside -->
</div><!-- /.postbox -->