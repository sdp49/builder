<?php
global $shortcode_subpages;

$post_type_object = get_post_type_object('page');
if ( ! current_user_can( $post_type_object->cap->edit_posts ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

$values = wp_parse_args($_POST, array('action'=>'edit', 'options'=>array(), 'filters'=>array()));

if (!empty($values['submit'])) {
	if ($values['action'] == 'save') {
		$args = array_merge($values['options'], $values['filters']);
		$new_page = array(
				'post_title' => 'Property Search Page',
				'post_content' => PL_Shortcode_CPT::generate_shortcode_str('pl_idx', $args),
				'post_status' => 'auto-draft',
				'post_date' => date('Y-m-d H:i:s'),
				'post_type' => 'page',
				'post_category' => array(0)
		);
		$post_id = wp_insert_post($new_page);
		wp_redirect(admin_url('post.php?action=edit&post='.$post_id));
		die;
	}
}

$templates = PL_Shortcode_CPT::template_list('pl_idx', false, true);
$tpl_types = array();
$sct_args = PL_Shortcode_CPT::get_shortcode_attrs('pl_idx', true);

?>
<div class="wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Custom Shortcodes'); ?>

	<form action="" method="POST">
	
		<input type="hidden" name="options[context]" value="" />
		<input type="hidden" name="action" value="save" />
		
		<div id="pl_tmplt_picker">
			<h2>Real Estate Page Creator - Select a Template</h2>
			<p>Select one of the following templates for your Real Estate Search Page:</p> 
		
			<?php if (count($tpl_types) > 1): ?>
			<div id="pl_tmplt_picker">
				<label>Template Type</label>
				<select name="template_type">
					<?php foreach($tpl_types as $tpl_type): ?>
					<option><?php echo $tpl_type ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<?php endif ?>
			
			<div class="pl_tmplts">
				<?php foreach($templates as $id=>$template_info): ?>
					<?php if (empty($template_info['template'])): ?>
					<div class="pl_tmplt pl_custom">
						<h3 class="pl_tmplt_title"><?php echo $template_info['title'] ?></h3>
						<div class="screenshot"></div>
						<div class="pl_tmplt_description">Custom template.</div>
						<div class="pl_tmplt_actions"><a href="#" class="pl_tmplt_select" data-tmplt_id="<?php echo $id ?>">Select Template</a></div>
					</div>
					<?php else: ?>
					<div class="pl_tmplt pl_default">
						<h3 class="pl_tmplt_title"><?php echo $template_info['title'] ?></h3>
						<div class="screenshot">
							<image src="<?php echo $template_info['template']['screenshot'] ?>" />
						</div>
						<div class="pl_tmplt_description"><?php echo $template_info['template']['description'] ?></div>
						<div class="pl_tmplt_actions"><a href="#" class="pl_tmplt_select" data-tmplt_id="<?php echo $id ?>">Select Template</a></div>
					</div>
					<?php endif ?>
				<?php endforeach ?>
			</div>

		</div>

		<div id="pl_filter_picker" style="display:none;">
			<h2>Real Estate Page Creator - Filter Results</h2>
			<p>You can add filters if you want to limit the search results.</p>
			<p><strong>Note:</strong> if you wish to limit searches to certain locations or property types for the whole site, then please use the <a href="<?php echo admin_url('admin.php?page=placester_settings_filtering')?>" target="_blank">Global Property Filtering</a> settings.</p> 
			<?php
				$js_filters = array();
				$select = $filter = $cat = '';
				foreach($sct_args['filters'] as $f_args) {
					$parent = 'filter_options';
					$selectvalue = 'sc_edit-filter_options-';
					$value = '';
					if ($f_args['group']) {
						$parent .= '['.$f_args['group'].']';
						$selectvalue .= $f_args['group'] . '-';
						if (isset( $values[$f_args['group']][$f_args['attribute']] )) {
							$js_filters[] = array(
								'id'=>$selectvalue.$f_args['attribute'],
								'value'=>$values[$f_args['group']][$f_args['attribute']]);
						}
					}
					else {
						if (isset( $values[$f_args['attribute']] )) {
							$js_filters[] = array(
								'id'=>$selectvalue.$f_args['attribute'],
								'value'=>$values[$f_args['attribute']]);
						}
					}
					$filter .= PL_Form::item($f_args['attribute'], $f_args, 'POST', $parent, 'sc_edit-', false);
					if ($cat!=$f_args['cat']) {
						if ($cat) {
							$select .= '</optgroup>';
						}
						$cat = $f_args['cat'];
						$select .= '<optgroup label="'.$cat.'">';
					}
					$select .='<option value="'.$selectvalue.$f_args['attribute'].'">'.$f_args['label'].'</option>';
				}
				if ($cat) {
					$select .= '</optgroup>';
				}
			?>
			<select name="filter" class="filter_select"><?php echo $select ?></select>
			<div class="pl_filters">
				<?php echo $filter ?>
			</div>
			<a href="#" class="button-secondary add_filter">Add Filter</a>
			<div class="active_filters"></div>
			<script>var active_filters = <?php echo json_encode($js_filters) ?>;</script>
			
			<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Next"></p>
			
		</div>
		
		
	</form>
		

</div>