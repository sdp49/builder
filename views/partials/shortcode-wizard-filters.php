<?php

$sct_args = PL_Shortcode_CPT::get_shortcode_attrs('pl_idx', true);

?>
		<div id="pl_filter_picker">
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
			
			<p class="submit"><input type="submit" name="submit_prev" class="button-primary" value="Back"><input type="submit" name="submit_next" class="button-primary" value="Next"></p>
			
		</div>

