<?php
/**
 * Edit taxonomies for displaying groups of properties
 */
global $pagenow;

$page = $_REQUEST['page'];
$location = "$pagenow?page=$page";

$taxlist = array('neighborhood'=>'neighborhood', 'city'=>'locality', 'state'=>'region');
print_r($_REQUEST);
$taxnow = empty($_REQUEST['taxonomy']) ? '' : $_REQUEST['taxonomy']; 
if (!array_key_exists($taxnow, $taxlist)) {
	$taxnow = current(array_keys($taxlist));;
}
$taxonomy = $taxnow;

$tax = get_taxonomy($taxonomy);
if (!$tax) {
	wp_die(__('Invalid taxonomy'));
}
if (!current_user_can($tax->cap->manage_terms)) {
	wp_die(__('Cheatin&#8217; uh?'));
}

if(!class_exists('PL_Property_Terms_Table')){
	require_once(PL_LIB_DIR . 'property-terms-table.php');
}

$wp_list_table = new PL_Property_Terms_Table(array('taxonomy'=>$taxonomy));
$pagenum = $wp_list_table->get_pagenum();
$current_screen = get_current_screen();
$title = $tax->labels->name;
$post_type = 'property';

add_screen_option('per_page', array('label' => 'Per page', 'default' => 20, 'option' => 'edit_' . $tax->name . '_per_page'));
$action = $wp_list_table->current_action();

switch ($action) {

case 'add-tag':
	check_admin_referer('add-tag', '_wpnonce_add-tag');
	if (!current_user_can($tax->cap->edit_terms)) {
		wp_die(__('Cheatin&#8217; uh?'));
	}
	$ret = wp_insert_term($_POST['tag-name'], $taxonomy, $_POST);
	if ($ret && !is_wp_error($ret)) {
		$message = 1;
	}
	else {
		$message = 4;
	}
	break;

case 'delete':
	if (!empty($_REQUEST['tag_ID'])) {
		$tag_ID = (int) $_REQUEST['tag_ID'];
		check_admin_referer('delete-tag_' . $tag_ID);
		if (!current_user_can($tax->cap->delete_terms)) {
			wp_die(__('Cheatin&#8217; uh?'));
		}
		wp_delete_term($tag_ID, $taxonomy);
		$message = 2;
	}
	break;

case 'bulk-delete':
	check_admin_referer('bulk-tags');
	if (!current_user_can($tax->cap->delete_terms)) {
		wp_die(__('Cheatin&#8217; uh?'));
	}
	$tags = (array) $_REQUEST['delete_tags'];
	foreach ($tags as $tag_ID) {
		wp_delete_term($tag_ID, $taxonomy);
	}
	$message = 6;
	break;

case 'edit':
	$title = $tax->labels->edit_item;
	$tag_ID = (int) $_REQUEST['tag_ID'];
	$tag = get_term($tag_ID, $taxonomy, OBJECT, 'edit');
	if (! $tag) {
		wp_die(__('You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?'));
	}
	PL_Router::load_builder_partial('property-terms-form.php', array('taxonomy'=>$taxonomy, 'tax'=>$tax, 'tag_ID'=>$tag_ID, 'tag'=>$tag));
	exit;

case 'editedtag':
	$tag_ID = (int) $_POST['tag_ID'];
	check_admin_referer('update-tag_' . $tag_ID);
	if (!current_user_can($tax->cap->edit_terms)) {
		wp_die(__('Cheatin&#8217; uh?'));
	}
	$tag = get_term($tag_ID, $taxonomy);
	if (! $tag) {
		wp_die(__('You attempted to edit an item that doesn&#8217;t exist. Perhaps it was deleted?'));
	}
	$ret = wp_update_term($tag_ID, $taxonomy, $_POST);
	if ($ret && !is_wp_error($ret)) {
		$message = 3;
	}
	else {
		$message = 5;
	}
	break;
}

$wp_list_table->prepare_items();

if (!current_user_can($tax->cap->edit_terms)) {
	wp_die(__('You are not allowed to edit this item.'));
}

$locations = (array)PL_Listing_Helper::locations_for_options($taxlist[$taxonomy]);

$messages[1] = __('Item added.');
$messages[2] = __('Item deleted.');
$messages[3] = __('Item updated.');
$messages[4] = __('Item not added.');
$messages[5] = __('Item not updated.');
$messages[6] = __('Items deleted.');
?>
<div class="wrap nosubsub">
	<?php echo PL_Helper_Header::pl_settings_subpages(); ?>
	<h2>Display Properties Grouped By Location</h2>
	<p>If your theme supports custom pages for displaying properties by location, you can use this screen to create pages for different location types.</p>
	<form id="taxonomy-select" action="" method="post">
		<label for="page-type">Edit Pages For:</label>
		<select name="taxonomy" id="page-type">
		<?php
		foreach($taxlist as $tlslug=>$tlloc) {
			$tltax = get_taxonomy($tlslug);
			if ($tltax) {
			?>
				<option value="<?php echo $tlslug ?>" <?php echo ($tlslug==$taxonomy ? 'selected="selected"' : '') ?>><?php echo $tltax->labels->name ?></option>
			<?php
			}
		} 
		?>
		</select>
		<input type="submit" name="submit" class="button" value="Select" />
	</form>
	
	<h3>
	<?php if (!empty($_REQUEST['s'])): ?> 
		<?php printf('<span class="subtitle">' . __('Search results for &#8220;%s&#8221;') . '</span>', esc_html(stripslashes($_REQUEST['s']))); ?>
	<?php endif ?>
	</h3>
	<?php if (isset($_REQUEST['message']) && ($msg = (int) $_REQUEST['message'])) : ?>
	<div id="message" class="updated"><p><?php echo $messages[$msg]; ?></p></div>
	<?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
	endif; ?>
	<div id="ajax-response"></div>
	<form class="search-form" action="<?php echo $pagenow?>" method="get">
		<input type="hidden" name="page" class="post_page" value="<?php echo $page ?>" />
		<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
		<?php $wp_list_table->search_box($tax->labels->search_items, 'tag'); ?>
	</form>
	<br class="clear" />
	<div id="col-container">
	
		<div id="col-right">
			<div class="col-wrap">
				<form id="posts-filter" action="" method="post">
					<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
					<?php $wp_list_table->display(); ?>
					<br class="clear" />
				</form>
			</div>
		</div><!-- /col-right -->
		
		<div id="col-left">
			<div class="col-wrap">
			<?php if (current_user_can($tax->cap->edit_terms)): ?>
				<div class="form-wrap">
					<h3>Add A <?php echo $tax->labels->singular_name; ?></h3>
					<p>If your theme supports custom pages for <?php echo $tax->labels->name; ?>, you can add a page for any <?php echo $tax->labels->singular_name; ?>
					provided in your MLS.</p>
					<form id="addtag" method="post" action="">
					<input type="hidden" name="action" value="add-tag" />
					<input type="hidden" name="screen" value="<?php echo esc_attr($current_screen->id); ?>" />
					<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
					<?php wp_nonce_field('add-tag', '_wpnonce_add-tag'); ?>
					
					<div class="form-field form-required">
						<label for="tag-name"><?php _ex('Name', 'Taxonomy Name'); ?></label>
						<select name="tag-name" id="tag-name">
						<?php foreach($locations as $location):?>
							<?php if (trim($location)!=''):?>
							<option><?php echo $location ?></option>
							<?php endif ?> 
						<?php endforeach;?>
						</select>
					</div>
					<div class="form-field">
						<label for="tag-description"><?php _ex('Description', 'Taxonomy Description'); ?></label>
						<textarea name="description" id="tag-description" rows="5" cols="40"></textarea>
						<p><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></p>
					</div>
					
					<?php
					if (!is_taxonomy_hierarchical($taxonomy)) {
						do_action('add_tag_form_fields', $taxonomy);
					}
					do_action($taxonomy . '_add_form_fields', $taxonomy);
					
					submit_button('Add '.$tax->labels->singular_name, 'submit');
					?>
					</form>
				</div>
			<?php endif ?>
			</div>
		</div><!-- /col-left -->

	</div><!-- /col-container -->
			
</div><!-- /wrap -->
<?php 
