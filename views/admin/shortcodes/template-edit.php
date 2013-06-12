<?php
global $shortcode_subpages, $page_now, $plugin_page;

$action = (empty($_REQUEST['action'])?'':$_REQUEST['action']);
$title = (empty($_REQUEST['title'])?'':$_REQUEST['title']);
$pl_post_type = ( empty($_REQUEST['type'])?'pl_form':$_REQUEST['type']);
$ID = (empty($_REQUEST['id'])?'':$_REQUEST['id']);

$shortcode = PL_Shortcode_CPT::get_context_template($pl_post_type);

if ($action == 'delete' && $ID) {
	PL_Shortcode_CPT::delete_shortcode_template($ID);
	wp_redirect('admin.php?page=placester_shortcodes');
	die;		
}
if ($action == 'edit') {
	// template has already been saved by ajax
	wp_redirect('admin.php?page=placester_shortcodes');
	die;
}

if ($shortcode) {
	// load snippets
	$snippets = PL_Shortcode_CPT::load_shortcode_template($shortcode, $title);
	if ($title) {
		$ID = $shortcode.'-'.$title;
	}
}
else {
	$pl_post_type = 'pl_map';
	$snippets = array();
	$ID = '';
}

// create a temprary post that we can use to preview the template
$post_ID = wp_insert_post(array('post_type'=>'pl_general_widget'));
$post = get_post($post_ID);

$notice = '';
$message = '';

$form_link = '';
$delete_link = $page_now.'?page='.$plugin_page.'&action=delete&id='.$ID;
$form_action = 'edit';
$nonce_action = 'edit-sc-template_' . $ID;

// get link for iframe
$permalink = get_permalink($post->ID);

?>
<div class="wrap pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Shortcode Settings'); ?>

	<div id="pl_sc_tpl_edit">
		<?php if ( $notice ) : ?>
		<div id="notice" class="error"><p><?php echo $notice ?></p></div>
		<?php endif; ?>
		<?php if ( $message ) : ?>
		<div id="message" class="updated"><p><?php echo $message; ?></p></div>
		<?php endif; ?>

		<form name="post" action="<?php echo $form_link?>" method="post" id="post"<?php do_action('post_edit_form_tag'); ?>>
			<?php wp_nonce_field($nonce_action); ?>
			<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" id="post_ID" name="post_ID" value="<?php echo esc_attr($post_ID) ?>" />
			<input type="hidden" id="sc_tpl_id" name="sc_tpl_id" value="<?php echo esc_attr($ID) ?>" />
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<?php wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );?>
						<div id="normal-sortables" class="meta-box-sortables">
							<?php PL_Router::load_builder_partial('shortcode-template-box.php', array(
									'post'=>$post, 
									'title'=>$title,
									'pl_post_type'=>$pl_post_type,
									'data'=>$snippets,
								));?>
						</div>
					</div>
					<div id="postbox-container-1" class="postbox-container">
						<div id="submitdiv" class="postbox">
							<?php $action_title = ($ID ? __('Save') : __('Create'))?>
							<h3 class="hndle"><span><?php echo $action_title;?></span></h3>
							<div class="inside">
								<div class="submitbox" id="submitpost">
								
									<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
									<div style="display:none;">
									<?php submit_button( __( 'Save' ), 'button', 'save' ); ?>
									</div>
								
									<div id="major-publishing-actions">
										<?php if ($ID):?>
										<div id="delete-action">
											<a class="submitdelete deletion" href="<?php echo $delete_link; ?>"><?php echo __('Delete'); ?></a>
										</div>
										<?php endif;?>
										<div id="publishing-action">
											<input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php echo $action_title; ?>" />
										</div>
										<div class="clear"></div>
									</div>
								
								</div>
							</div>
						</div>					
						<?php
						// read width/height and slideshow values
						$width = 250;
						$_POST['width'] = $width;
						$height = 250;
						$_POST['height'] = $height;
						$animationSpeed = 800;
						$_POST['animationSpeed'] = $animationSpeed;
						$style = ' style="width: ' . $width . 'px;height: ' . $height . 'px"';
						// for post edits, prepare the frame related variables (iframe and script)
						if( ! empty( $permalink ) ) {
							$iframe = '<iframe src="' . $permalink . '"'. $style .'></iframe>';
							$iframe_controller = '<script id="plwidget-' . $post->ID . '" src="' . PL_PARENT_URL . 'js/fetch-widget.js?id=' . $post->ID . '"'	. $style . '></script>';
						} 
						PL_Router::load_builder_partial('shortcode-preview.php', array('post'=>$post));
						?>
					</div>
				</div><!-- /post-body -->
			</div>
		</form>
	
		<div id="ajax-response"></div>
	</div>
</div>