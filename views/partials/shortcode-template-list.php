<?php
/**
 * Generates a drop list of available shortcode templates
 */

$pl_snippet_list = array();
foreach ( PL_General_Widget_CPT::$codes as $code=>$name ) {
	$pl_snippet_list[$code] = PL_Snippet_Helper::get_shortcode_snippet_list($code);
}
$pl_active_snippets = PL_Snippet_Helper::get_active_snippet_map();
$pl_snippet_types = array('default' => 'Default', 'custom' => 'Custom'); // Order matters, here...


$select_name = '';


?>
<select id="cpt_template" class="snippet_list"
<?php if( ! empty( $select_name ) ) { echo 'name="'. $select_name . '"'; } ?>>
	<?php foreach ($pl_snippet_types as $curr_type => $title_type): ?>
		<optgroup label="<?php echo $title_type?>">
			<?php foreach ($pl_snippet_list[$code] as $snippet => $type): ?>
				<?php if ($type != $curr_type) { 
					continue;
				} ?>
				<?php if( empty( $value ) ): ?>
				<option id="<?php echo $snippet ?>" value="<?php echo $snippet ?>"
					class="<?php echo $type ?>"
					<?php echo $pl_active_snippets[$code] == $snippet ? 'selected' : '' ?>>
					<?php else: ?>
				<option id="<?php echo $snippet ?>" value="<?php echo $snippet ?>"
					class="<?php echo $type ?>"
					<?php echo $value == $snippet ? 'selected' : '' ?>>
					<?php endif; ?>
					<?php echo $snippet ?>
				</option>
			<?php endforeach ?>
		</optgroup>
	<?php endforeach ?>
</select>
