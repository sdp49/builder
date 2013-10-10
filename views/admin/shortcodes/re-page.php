<?php
global $shortcode_subpages, $plugin_page;

$post_type_object = get_post_type_object('page');
if ( ! current_user_can( $post_type_object->cap->edit_posts ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

$values = wp_parse_args($_GET, array('action'=>'edit', 'tpl_id'=>''));

if ($values['action']=='idx_selected') {
	$args = array('context'=>$values['tpl_id']);
	$new_page = array(
		'post_name' => 'property-search',
		'post_title' => 'Real Estate Search',
		'post_content' => PL_Shortcode_CPT::generate_shortcode_str('pl_idx', $args),
		'post_type' => 'page',
		'post_status' => 'publish',
	);
	$page_id = wp_insert_post($new_page);
}
elseif ($values['action']=='listing_selected') {
	$tpls = $templates = PL_Listing_Customizer::get_template_list();
	if (!empty($tpls[$values['tpl_id']])) {
		PL_Listing_Customizer::set_active_template_id($values['tpl_id']);
		$tpl_title = $tpls[$values['tpl_id']]['title'];
	}
	else {
		$values['action'] = '';
	}
}

$submit_link = admin_url('admin.php?page='.$plugin_page);

?>
<div class="wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Custom Shortcodes') ?>
	<h2>Real Estate Page Creator</h2>

	<?php if ($values['action']=='idx_selected' && $page_id): ?>
	
		<div id="pl_repage_idx_selected">
			<p>You have selected a template for your property search page!</p>
			<p>You can view the page <a href="<?php echo get_permalink($page_id) ?>">here</a>. 
			If you would like to change the page title or add any other text to the page, you can edit it <a href="<?php echo get_edit_post_link($page_id) ?>">here</a>.
			</p>
		</div>
	
	<?php elseif ($values['action']=='listing_selected'): ?>
	
		<div id="pl_repage_listing_selected">
			<p>You have selected the '<?php echo $tpl_title?>' template for your listings pages. Your listing pages will be formatted by using the template.</p>
		</div>
	
	<?php else: ?>
	
		<div class="pl_repage_group pl_repage_idx">
		<h3>Search Page Templates</h3>
		<?php PL_Router::load_builder_partial('shortcode-wizard-idx.php', array('submit_link'=>$submit_link)) ?>
		</div>
	
		<div class="pl_repage_group pl_repage_listings">
		<h3>Listing Page Templates</h3>
		<?php PL_Router::load_builder_partial('shortcode-wizard-listings.php', array('submit_link'=>$submit_link)) ?>
		</div>
	
	<?php endif ?>
</div>