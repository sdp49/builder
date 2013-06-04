<?php
/**
 * Displays meta box used in the shortcode template edit view
 */

$title = empty($title)?'':$title;
$pl_post_type = empty($pl_post_type)?'':$pl_post_type;
$data = empty($data)?array():$data;
$data = wp_parse_args($data, array('before_widget'=>'', 'after_widget'=>'', 'snippet_body'=>'', 'widget_css'=>''));

$pl_shortcode_types = PL_General_Widget_CPT::$post_types; 
$pl_shortcode_codes = PL_General_Widget_CPT::$codes;

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
				<select id="tpl_post_type">
						<?php 
						$num_of_post_types = count( $pl_shortcode_types );
						$i = 0;
						$shortcode_ref = array();

						foreach( $pl_shortcode_types as $post_type => $label ):
							$i++;
							$link_class = ($post_type == $pl_post_type) ? 'selected_type' : '';
							$selected = ( !empty($link_class) ) ? 'selected="selected"' : '';
							?>
							<option id="pl_post_type_<?php echo $post_type; ?>" class="<?php echo $link_class; ?>" value="pl_post_type_<?php echo $post_type; ?>" <?php echo $selected; ?>>
								<?php echo $label; ?>
							</option>
							<?php
							$shortcode_ref[$post_type] = PL_Router::load_builder_partial('shortcode-ref.php', array('shortcode' => $code), true);
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
				<a href="#">Use existing template as a base for this new template</a>

				<!-- Add HTML -->
				<label for="area_snippet">HTML</label>
				<div class="edit-box">
					<textarea id="area_snippet" name="snippet_body" class="snippet"><?php echo $data['snippet_body']?></textarea>
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
				<script type="text/javascript">
					var shortcode_ref = <?php echo json_encode($shortcode_ref);?>
				</script>
				<div style="display: none;">
					<label for="search-subshortcodes">Sub-Shortcodes</label> 
					<input type="text" placeholder="search sub-shortcodes" />
				</div>			
				<div id="shortcodes"></div>
			</div>
			<?php foreach( PL_General_Widget_CPT::$codes as $code => $label ): ?>
				<div class="pl_template_block" id="<?php echo $code.'_template_block';?>" style="display: none;">
				<?php echo PL_Router::load_builder_partial('shortcode-ref.php', array('shortcode' => $code), true); ?>
				</div>
			<?php endforeach;?>
			
		</section><!-- /Template Contents -->

		<input type="hidden" name="pl_post_type" id="pl_post_type" value="pl_map" />
			
		<?php wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );?>
		
		<div class="clear"></div>
			
	</div><!-- /.inside -->

</div><!-- /.postbox -->
