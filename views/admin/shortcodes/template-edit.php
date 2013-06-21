<?php
global $shortcode_subpages, $page_now, $plugin_page;

$action = (empty($_REQUEST['action'])?'':$_REQUEST['action']);
$ID = (empty($_REQUEST['id'])?'':$_REQUEST['id']);
$notice = $message = '';
$nonce_action = 'edit-sc-template_' . $ID;

if ($action == 'delete' && $ID) {
	if (!PL_Shortcode_CPT::template_in_use($ID)) {
		PL_Shortcode_CPT::delete_shortcode_template($ID);
	}
	wp_redirect('admin.php?page=placester_shortcodes_templates');
	die;
}
if ($action == 'edit') {
	if (empty($_POST['title'])) {
		$notice = 'Please provide a title for the template.';
	}
	elseif(!empty($_POST['save']) && !empty($_POST['shortcode'])) {
		if (!empty($_POST[$_POST['shortcode']])) {
			$data = array_merge($_POST, $_POST[$_POST['shortcode']]);
		}
		else {
			$data = $_POST;
		}
		$id = PL_Shortcode_CPT::save_shortcode_template($ID, $data);
		//wp_redirect('admin.php?page=placester_templates');
		wp_redirect('admin.php?page=placester_shortcodes_template_edit&id='.$id);
		die;
	}
}

// load template
$template = PL_Shortcode_CPT::load_shortcode_template($ID);
$title = (empty($_REQUEST['title'])?$template['title']:$_REQUEST['title']);
$shortcode = (empty($_REQUEST['shortcode'])?$template['shortcode']:$_REQUEST['shortcode']);
$form_link = '';
$delete_link = $page_now.'?page='.$plugin_page.'&action=delete&id='.$ID;
$form_action = 'edit';
?>
<div class="wrap pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Create Shortcode Template'); ?>

	<div id="pl_sc_tpl_edit">
		<?php if ( $notice ) : ?>
		<div id="notice" class="error"><p><?php echo $notice ?></p></div>
		<?php endif; ?>
		<?php if ( $message ) : ?>
		<div id="message" class="updated"><p><?php echo $message; ?></p></div>
		<?php endif; ?>

		<p>
		Use this form to build a shortcode template that can be used to control the appearance of Placester shortcodes.
		</p>
		
		<form name="post" action="<?php echo $form_link?>" method="post" id="post"<?php do_action('post_edit_form_tag'); ?>>
			<?php wp_nonce_field($nonce_action); ?>
			<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" id="id" name="id" value="<?php echo esc_attr($ID) ?>" />
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<?php wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );?>
						<div id="normal-sortables" class="meta-box-sortables">
							<?php PL_Router::load_builder_partial('shortcode-template-box.php', array(
									'title'=>$title,
									'shortcode'=>$shortcode,
									'values'=>$template,
								));?>
						</div>
					</div>
					<div id="postbox-container-1" class="postbox-container">
						<div id="submitdiv" class="postbox">
							<?php $action_title = ($ID ? __('Update') : __('Create'))?>
							<h3 class="hndle"><span><?php echo $action_title;?></span></h3>
							<div class="inside">
								<div class="submitbox" id="submitpost">

									<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
									<div style="display:none;">
									<?php submit_button( __( 'Update' ), 'button', 'save' ); ?>
									</div>

									<div id="major-publishing-actions">
										<?php if ($ID && !PL_Shortcode_CPT::template_in_use($ID)):?>
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
						PL_Router::load_builder_partial('shortcode-preview.php', array());
						?>
					</div>
				</div><!-- /post-body -->
			</div>
		</form>

		<div id="ajax-response"></div>
	</div>
</div>