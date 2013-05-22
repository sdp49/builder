<?php
/**
 * Displays meta box used in the shortcode edit view
 */

$is_post_new = true;
if( ! empty( $_GET['post'] ) ) {
	$is_post_new = false;
}

// get all CPT custom field values
$values = get_post_custom( $post->ID );

// read the post type
$pl_post_type = isset( $values['pl_post_type'] ) ? $values['pl_post_type'][0] : '';

$pl_shortcode_types = PL_General_Widget_CPT::$post_types; 

// manage featured and static listing form values
$pl_featured_meta_value = '';
if( ! empty( $values['pl_featured_listing_meta'] ) ) {
	if( is_array( $values['pl_featured_listing_meta'] ) ) {
		$pl_featured_meta_value = $values['pl_featured_listing_meta'][0];
		$pl_featured_meta_value = @unserialize( $pl_featured_meta_value );

		if( false === $pl_featured_meta_value ) {
			$pl_featured_meta_value = @json_decode( $values['pl_featured_listing_meta'][0], true );
		} else if( is_array( $pl_featured_meta_value ) && isset( $pl_featured_meta_value[0] ) ) {
			$pl_featured_meta_value = $pl_featured_meta_value[0];
		}
		if(is_array( $pl_featured_meta_value ) && isset( $pl_featured_meta_value['featured-listings-type'] )) {
			$pl_featured_meta_value = $pl_featured_meta_value['featured-listings-type'];
		}
	} else if(isset( $values['pl_featured_listing_meta']['featured-listings-type'] )) {
		$pl_featured_meta_value = $values['pl_featured_listing_meta']['featured-listings-type'];
	}
}



$_POST['pl_featured_meta_value'] = $pl_featured_meta_value;

$pl_static_listings_option = isset( $values['pl_static_listings_option'] ) ? unserialize($values['pl_static_listings_option'][0]) : '';
if( is_array( $pl_static_listings_option ) ) {
	foreach( $pl_static_listings_option as $key => $value ) {
		if( ! empty( $value ) ) {
			$_POST[$key] = $value;
		}
	}
}

// get link for iframe
$permalink = '';
if( ! $is_post_new ) {
	$permalink = get_permalink($post->ID);
}
?>
<div id="pl-controls-metabox-id" class="postbox ">
	
	<h3>Create Shortcode</h3>
	
	<div id="edit-sc-metabox-inner" class="inside">	

		<!-- Type and Template -->
		<div id="post_types_list" class="meta_section">

    <!-- </section> -->

			<!-- Type -->
			<section id="edit-sc-choose-type" class="post_types_list_wrapper row-fluid" style="clear: both; padding-top: 10px;">
				
				<div class="span2">
        	<p class="section-label">Type:</p>
      	</div>

      	<div class="span9">

					<select id="pl_post_type_dropdown" name="pl_post_type_dropdown" class="chosen">
						
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
							echo ( $i < $num_of_post_types ) ? '<span class="pl_type_separator"> |</span>' : '';
						endforeach;
						?>
					</select>

				</div>
			
			</section><!-- /.post_types_list_wrapper -->

			<!-- Template / Layout -->
			<section id="edit-sc-choose-template" class="row-fluid">
	      <div class="span2">
	        <p class="section-label">Template:</p>
  	    </div>
				<div class="span6">
					<?php PL_Router::load_builder_partial('shortcode-template-list.php', array('post'=>$post, 'select_class'=>'chosen'));?>
				</div>
				<div class="offset1 span3">
					<a href="<?php echo admin_url('admin.php?page=placester_shortcodes_template_edit')?>" id="create-new-template-link">(create new)</a>
				</div>
			</section><!-- /edit-sc-choose-template -->

		</div><!-- /#post_types_list -->


		<!-- Options / Filters -->
		<div id="widget-meta-wrapper" style="display: none; min-height: 370px">
			<?php
			// read width/height and slideshow values
			$width =  isset( $values['width'] ) && ! empty( $values['width'][0] ) ? $values['width'][0] : '250';
			$_POST['width'] = $width;
			$height = isset( $values['height'] ) && ! empty( $values['height'][0] ) ? $values['height'][0] : '250';
			$_POST['height'] = $height;
			$animationSpeed = isset( $values['animationSpeed'] ) && ! empty( $values['animationSpeed'][0] ) ? $values['animationSpeed'][0] : '800';
			$_POST['animationSpeed'] = $animationSpeed;
			$widget_class = isset( $values['widget_class'] ) && ! empty( $values['widget_class'][0] ) ? 'class="'  . $values['widget_class'][0] . '"' : '';
			
			$style = ' style="width: ' . $width . 'px;height: ' . $height . 'px"';
			
			// for post edits, prepare the frame related variables (iframe and script)
			if( ! empty( $permalink ) ):
				$iframe = '<iframe src="' . $permalink . '"'. $style . $widget_class .'></iframe>';
				$iframe_controller = '<script id="plwidget-' . $post->ID . '" src="' . PL_PARENT_URL . 'js/fetch-widget.js?id=' . $_GET['post'] . '"'  . $style . ' ' . $widget_class . '></script>';
			endif; ?>

			<div class="pl_widget_block">
				
				<section class="pl_map pl_form pl_search_listings pl_slideshow pl_neighborhood featured_listings static_listings">
					<label>Options</label>
				</section>
					<?php // get meta values from custom fields
					// fill POST array for the forms (required after new widget is created)
					foreach( $pl_shortcode_types as $field => $arguments ) {
						$value = isset( $values[$field] ) ? $values[$field][0] : '';
			
						if( !empty( $value ) && empty( $_POST[$field] ) ) {
							$_POST[$field] = $value;
						}
			
						echo PL_Form::item($field, $arguments, 'POST', false, 'general_widget_');
					}
					?>
			</div><!-- /.pl_widget_block -->

			<section class="featured_listings">
				<h2>Pick a Listing</h2>
			</section>

			<div id="pl-fl-meta">
				<div style="width: 400px;">
					<div id="pl_featured_listing_block" class="featured_listings pl_slideshow" style="min-height: 40px;">
						<?php 
							include PLS_OPTRM_DIR . '/views/featured-listings.php';
							// Enqueue all required stylings and scripts
							wp_enqueue_style('featured-listings', OPTIONS_FRAMEWORK_DIRECTORY.'css/featured-listings.css');
							wp_register_script('datatable', trailingslashit( PLS_JS_URL ) . 'libs/datatables/jquery.dataTables.js' , array( 'jquery'), NULL, true );
							wp_enqueue_script('datatable'); 
							wp_enqueue_script('jquery-ui-core');
							wp_enqueue_style('jquery-ui-datepicker');
							wp_enqueue_script('jquery-ui-datepicker');
							wp_enqueue_style('jquery-ui-dialog', OPTIONS_FRAMEWORK_DIRECTORY.'css/jquery-ui-1.8.22.custom.css');
							wp_enqueue_script('jquery-ui-dialog');
							wp_enqueue_script('options-custom', OPTIONS_FRAMEWORK_DIRECTORY.'js/options-custom.js', array('jquery'));
							wp_enqueue_script('featured-listing', OPTIONS_FRAMEWORK_DIRECTORY.'js/featured-listing.js', array('jquery'));
					
							// Generate the popup dialog with featured			
							echo pls_generate_featured_listings_ui(array(
												'name' => 'Featured Meta',
												'desc' => '',
												'id' => 'featured-listings-type',
												'type' => 'featured_listing'
												) ,$pl_featured_meta_value
												, 'pl_featured_listing_meta');
						?>
					</div><!-- end of #pl_featured_listing_block -->
					<section id="pl_static_listing_block" class="static_listings pl_search_listings">
						<?php 
							$static_list_form = PL_Form::generate_form(
										PL_Config::PL_API_LISTINGS('get', 'args'),
										array('method' => "POST", 
												'title' => true,
												'wrap_form' => false, 
										 		'echo_form' => false, 
												'include_submit' => false, 
												'id' => 'pls_admin_my_listings'),
										'general_widget_');
	
							echo $static_list_form;
						 ?>
					</section><!-- end of #pl_static_listing_block -->
				</div>
			</div>
			<input type="hidden" name="pl_post_type" id="pl_post_type" value="pl_map" />
			<?php $atts = array();
			
			// get radio values for neighborhood
			$radio_def = isset( $values['radio-type'] ) ? $values['radio-type'][0] : 'state';
			$select_id = 'nb-select-' . $radio_def;
			$select_def = isset( $values[ $select_id ] ) ? $values[ $select_id ][0] : '0';
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				// manage neighborhood
				$('#<?php echo $radio_def; ?>').attr('checked', true);
				$('#nb-taxonomy-<?php echo $radio_def; ?>').css('display', 'block');
				$('#nb-id-select-<?php echo $radio_def; ?>').val(<?php echo $select_def; ?>);
		
				$('#pl_location_tax input:radio').on('click', radioClicks);
		
				function radioClicks() {
					var radio_value = this.value;
		
					$('.nb-taxonomy').each(function() {
						if( radio_value !== 'undefined') {
							if( this.id.indexOf(radio_value, this.id.length - radio_value.length) !== -1 ) {
								$(this).css('display', 'block');
							} else {
								$(this).css('display', 'none');
							}
						}
					});
				}
	
				$('#metadata-max_avail_on_picker').datepicker();
				$('#metadata-min_avail_on_picker').datepicker();
	
				// click a new post type as a widget type
				$('#post_types_list select').change(function() {
					if( $('#title').val() === '' ) {
						alert('Please enter widget title first.');
						return;
					} 
					
					//var selected_cpt = $(this).attr('id').substring('pl_post_type_'.length);
					var selected_cpt = $(this).parent().find(':selected').val().substring('pl_post_type_'.length);
	
					if( selected_cpt == 'undefined' ) {
						// clicking "Select" shouldn't reflect the choice
						return;
					}
					
					// $('#post_types_list a').removeClass('selected_type');
					// $(this).addClass('selected_type');
					$('#pl_post_type').val(selected_cpt);
	
					// hide values not related to the post type and reveal the ones to be used
					$('#widget-meta-wrapper .pl_widget_block > section, #pl_location_tax').each(function() {
						var section_class = $(this).attr('class');
						if( section_class !== undefined  ) {
							if( section_class.indexOf( selected_cpt ) !== -1  ) {
								$(this).show();
								// $(this).find('input').removeAttr('disabled');
								// $(this).find('select').removeAttr('disabled');
							} else {
								$(this).hide();
								// $(this).find('input, select').attr('disabled', true);
							}
						}
					});
	
					// fix inner sections for some CPTs
					if( selected_cpt == 'static_listings' || selected_cpt == 'pl_search_listings' ) {
						$('.form_group, .form_group section').show();
						$('#pl_static_listing_block #advanced').hide();
						$('#pl_static_listing_block #amenities').hide();
						$('#pl_static_listing_block #custom').hide();
						$('#general_widget_zoning_types').hide();
						$('#general_widget_purchase_types').hide();
					} else if( selected_cpt == 'pl_neighborhood' ) {
						$('.pl_neighborhood.pl_widget_block, .pl_neighborhood section').show();
					}
	
					// display template blocks
					$('.pl_template_block').each(function() {
						var selected_cpt = $('#pl_post_type').val();
						var block_id = $(this).attr('id');
						selected_cpt = selected_cpt.replace('pl_', '');
	
						if( block_id.indexOf( selected_cpt ) !== -1 ) {
							$(this).css('display', 'block');
						} else {
							$(this).css('display', 'none');
						}
					});
	
					$('.pl_template_section_title').show();
	
					$('#general_widget_pl_template_before_block').show();
					$('#general_widget_pl_template_after_block').show();
	
					// display/hide featured/static listings
					var featured_class = $('#pl_featured_listing_block').attr('class');
					var static_class = $('#pl_static_listing_block').attr('class');
	
					if( featured_class.indexOf( selected_cpt ) === -1 ) {
						$('#pl_featured_listing_block').hide();
					} else {
						$('#pl_featured_listing_block').show();
					}
	
					if( static_class.indexOf( selected_cpt ) === -1 ) {
						$('#pl_static_listing_block').hide();
					} else {
						$('#pl_static_listing_block').show();
					}
					
					$('#preview-meta-widget').html('<img id="preview_load_spinner" src="<?php echo PL_PARENT_URL . 'images/preview_load_spin.gif'; ?>" alt="Widget options are Loading..." width="30px" height="30px" style="position: absolute; top: 100px; left: 100px" />');
	
					// call the custom widget_autosave to send values to backend
					widget_autosave();
					
					$('#widget-meta-wrapper input, #widget-meta-wrapper select').css('background', '#ffffff');
					$('#widget-meta-wrapper input:disabled, #widget-meta-wrapper select:disabled').css('background', '#dddddd');
				});
	
				// call the custom autosave for every changed input and select
				$('#widget-meta-wrapper section input, #widget-meta-wrapper section select').on('change', function() {
					widget_autosave();				
				});
				$('#pl_template_before_block, #pl_template_after_block').on('change', function() {
					widget_autosave();				
				});
				$('#save-featured-listings').on('click', function() {
					setTimeout( widget_autosave, 1000 );
				});
	
				$('#pl-review-link').on('click', function(e) {
					e.preventDefault();
	
					var iframe_content = $('#preview-meta-widget').html();
	
					var options_width = jQuery('#widget-meta-wrapper input#width').val() || 750;
					var options_height = jQuery('#widget-meta-wrapper input#height').val() || 500;
					
					$('#pl-review-popup').html( iframe_content );
					$('#pl-review-popup iframe').css('width', options_width + 'px');
					$('#pl-review-popup iframe').css('height', options_height + 'px');
	
					$('#pl-review-popup').dialog({
							width: 800,
							height: 600
						});
				});
	
				// hide advanced values for static listings area
				$('#pl_static_listing_block #advanced').css('display', 'none');
				$('#pl_static_listing_block #amenities').css('display', 'none');
				$('#pl_static_listing_block #custom').css('display', 'none');
				$('<a href="#basic" id="pl_show_advanced" style="line-height: 50px;">Show Advanced filters</a>').insertBefore('#pl_static_listing_block #advanced');
				$('<a href="#basic" id="pl_hide_advanced" style="line-height: 50px; display: none;">Hide Advanced filters</a>').insertAfter('#pl_static_listing_block #custom');
	
				$('#pl_show_advanced').on('click', function() {
					$(this).hide();
					$('#pl_static_listing_block #advanced').css('display', 'block');
					$('#pl_static_listing_block #amenities').css('display', 'block');
					$('#pl_static_listing_block #custom').css('display', 'block');
					$('#pl_hide_advanced').show();
				});
	
				$('#pl_hide_advanced').on('click', function() {
					$(this).hide();
					$('#pl_static_listing_block #advanced').css('display', 'none');
					$('#pl_static_listing_block #amenities').css('display', 'none');
					$('#pl_static_listing_block #custom').css('display', 'none');
					$('#pl_show_advanced').show();
				});
	
				// populate slug box for the edit screen
				<?php if( ! $is_post_new ) { ?>
					$('#edit-slug-box').after('<div class="iframe-link"><strong>Embed Code:</strong> <?php echo esc_html( $iframe_controller ); ?></div><div class="shortcode-link"></div>');
					$('#pl_post_type_dropdown').trigger('change');
				<?php }	?>
	
				// reset before the view, hide everything
				$('#widget-meta-wrapper section, #pl_featured_listing_block').hide();
				$('.pl_template_block section').show();
				$('#widget-meta-wrapper').show();
	
				// Update preview when creating a new template
				$('.save_snippet').on('click', function() {
					$('#pl_post_type_dropdown').trigger('change');
				});
	
				<?php if( ! $is_post_new ) { ?>
					$('#pl_post_type_dropdown').trigger('change');
				<?php }	?>
	
				$('#pl-previewer-metabox-id .handlediv').on('click', function() {
					if ( $('#pl-previewer-metabox-id').hasClass('closed') ){
						$('#pl-previewer-metabox-id').css('min-height', '350px');
					} else {
						$('#pl-previewer-metabox-id').css('min-height', '0');
					}
				});
				
				// $('#pl_post_type_dropdown').trigger('change');
				$('#preview_load_spinner').remove();
				$('#preview-meta-widget').html('<?php echo isset($iframe) ? $iframe : '' ?>');
			});
			</script>	
				
			<?php wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );?>
		
			<section id="pl_location_tax" class="pl_neighborhood">
			<?php $taxonomies = PL_Taxonomy_Helper::get_taxonomies();?>
			<?php foreach ($taxonomies as $slug => $label): ?>
				<section>
					<input type="radio" id="<?php echo $slug ?>" name="radio-type" value="<?php echo $slug ?>">
					<label for="<?php echo $slug ?>"><?php echo $label ?></label>
				</section>
			<?php endforeach ?>	
			</section>
		
			<section class="pl_widget_block pl_neighborhood">
			<?php
			$taxonomies = PL_Taxonomy_Helper::$location_taxonomies;
			foreach( $taxonomies as $slug => $label ) {
				$terms = PL_Taxonomy_Helper::get_taxonomy_items( $slug );
					
				echo "<div id='nb-taxonomy-$slug' class='nb-taxonomy' style='display: none;'>";
				echo "<select id='nb-id-select-$slug' name='nb-select-$slug'>";
				foreach( $terms as $term ) {
					echo "<option value='" . $term['term_id'] . "'>" . $term['name'] . "</option>";
				}
					echo "</select>";
				echo "</div>";
			}
			?>
			</section>
			
			<div class="clear"></div>
		
		</div>
	</div>
</div>