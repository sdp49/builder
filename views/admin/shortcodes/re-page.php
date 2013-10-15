<?php
global $shortcode_subpages, $plugin_page;

$post_type_object = get_post_type_object('page');
if ( ! current_user_can( $post_type_object->cap->edit_posts ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

$values = wp_parse_args($_REQUEST, array('action'=>'edit', 'curr_action'=>'edit', 'prev_action'=>'edit', 'shortcode'=>'pl_idx', 'tpl_id'=>'', 'filters'=>array()));

if (!empty($values['submit_prev'])) {
	$values['action'] = $values['prev_action'];
}

if ($values['action']=='idx_template_selected') {
	$next_action = 'filters_selected';
} 
elseif ($values['action']=='filters_selected') {
	$args = array_merge(array('context'=>$values['tpl_id']), $values['filters']);
	$new_page = array(
		'post_name' => 'property-search',
		'post_title' => 'Real Estate Search',
		'post_content' => PL_Shortcode_CPT::generate_shortcode_str($values['shortcode'], $args),
		'post_type' => 'page',
		'post_status' => 'draft',
	);
	$page_id = wp_insert_post($new_page);
	// set the page template
	$page_templates = apply_filters('pls_available_theme_page_templates');
	if (count($page_templates)) {
		$templates = get_page_templates();
		foreach ($templates as $title=>$slug) {
			if (!empty($page_templates[$slug]['type']) && $page_templates[$slug]['type']=='full_width') {
				update_post_meta($page_id, '_wp_page_template', $slug);
				break;
			}
		}
	}
	wp_redirect(admin_url('post.php?action=edit&post='.$page_id));
	die;
}

$submit_link = admin_url('admin.php?page='.$plugin_page);

?>
<div class="wrap">
	<h2>Real Estate Page Creator</h2>

	<form action="<?php echo $submit_link ?>" method="post">

		<input type="hidden" name="action" value="<?php echo $next_action ?>" />
		<input type="hidden" name="curr_action" value="<?php echo $values['action'] ?>" />
		<input type="hidden" name="prev_action" value="<?php echo $values['prev_action'] ?>" />
		<input type="hidden" name="shortcode" value="<?php echo $values['shortcode'] ?>" />
		<input type="hidden" name="tpl_id" value="<?php echo $values['tpl_id'] ?>" />

		<?php if ($values['action']=='edit'): ?>
		<div class="pl_repage_group pl_repage_idx">
			<h4>1. What kind of page do you want to create?</h4>
			<?php $next_action = 'idx_template_selected'; ?> 
			<?php PL_Router::load_builder_partial('shortcode-wizard-idx.php', array('submit_link'=>$submit_link.'&action='.$next_action.'&curr_action='.$values['action'].'&prev_action='.$values['curr_action'])) ?>
		</div>
	
		<?php elseif ($values['action']=='idx_template_selected'): ?>
		<div class="pl_repage_group pl_repage_idx_filters">
			<h4>2. Please select your filters for the IDX Search Page</h4>
			<p>You can add filters if you want to limit the search results.	Hit next if you'd prefer not to have any filters.</p> 
			<?php PL_Router::load_builder_partial('shortcode-wizard-filters.php') ?>
		</div>
		
		<?php endif ?>

	</form>
	
</div>