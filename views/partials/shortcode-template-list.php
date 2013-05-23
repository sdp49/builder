<?php
/**
 * Generates a drop list of available shortcode templates with associated options
 */
$select_name = !empty($select_name) ? $select_name : '';
$value = !empty($value) ? $value : '';
$class = !empty($class) ? $class : '';
extract(PL_Page_Helper::get_types());

/*** Load initial data... ***/

$pl_snippet_list = array();
foreach ( $codes as $code ) {
  $pl_snippet_list[$code] = PL_Snippet_Helper::get_shortcode_snippet_list($code);
}
$pl_active_snippets = PL_Snippet_Helper::get_active_snippet_map();
$pl_snippet_types = array('default' => 'Default', 'custom' => 'Custom'); // Order matters, here...
// Check if the 'Property Details' functionality is enabled...
$pd_enabled = get_option(PL_Shortcodes::$prop_details_enabled_key);
?>

<?php foreach ($p_codes as $code => $name): ?>
<div class="snippet_container">
  <div class="shortcode_container">
    <input type="hidden" class="shortcode" value="<?php echo $code ?>" />
    <input type="hidden" class="active_snippet" value="<?php echo $pl_active_snippets[$code] ?>" />
    <section class="shortcode_ref">
      <select class="snippet_list <?php echo $class;?>"
      <?php if( ! empty( $select_name ) ) { echo 'name="'. $select_name . '"'; } ?>>
        <?php foreach ($pl_snippet_types as $curr_type => $title_type): ?>
          <optgroup label="<?php echo $title_type?>">
            <?php foreach ($pl_snippet_list[$code] as $snippet => $type): ?>
              <?php if ($type != $curr_type) { 
                continue;
              } ?>
              <?php if( empty( $value ) ): ?>
                <option value="<?php echo $snippet ?>" class="<?php echo $type ?>"
                  <?php echo $pl_active_snippets[$code] == $snippet ? 'selected="selected"' : '' ?>>
              <?php else: ?>
                <option value="<?php echo $snippet ?>" class="<?php echo $type ?>"
                  <?php echo $value == $snippet ? 'selected="selected"' : '' ?>>
              <?php endif; ?>
                <?php echo $snippet ?>
                </option>
            <?php endforeach ?>
          </optgroup>
        <?php endforeach ?>
      </select>
    </section>
  </div>
</div>
<?php endforeach ?>
