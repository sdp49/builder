<?php
global $shortcode_subpages;

$form_action = 'edit';
$post_type = 'pl_general_widget';
$post_def = array('post_type'=>$post_type, 'post_title'=>'', 'post_content'=>'');
$post_ID = (int)(empty($_REQUEST['ID'])?0:$_REQUEST['ID']);
$action = (empty($_REQUEST['action'])?$form_action:$_REQUEST['action']);
$post = array();
$notice = '';
$message = '';
$form_link = '';
$pl_shortcodes = PL_Shortcode_CPT::get_shortcodes();


if ($post_ID) {
	$post = PL_Shortcode_CPT::load_shortcode($post_ID);
	if (empty($post) || empty($post['shortcode'])) {
		$post_ID = 0;
		$post = array();
		$action = $form_action;
		$notice = 'Unable to locate that custom shortcode.';					
	}
	else {
		$post = array($post['shortcode']=>$post, 'shortcode'=>$post['shortcode'], 'post_title'=>$post['post_title'], 'post_content'=>$post['post_content']);
	}
}


if (!empty($_POST['publish'])) {
	if (empty($_POST['post_title'])) {
		$notice = 'Please provide a name for this shortcode.';	
	}
	elseif ($_POST['shortcode']=='undefined' || empty($pl_shortcodes[$_POST['shortcode']]) || empty($_POST[$_POST['shortcode']])) {
		$notice = 'Please select a shortcode.';	
	}
	else {
		if (PL_Shortcode_CPT::save_shortcode($post_ID, $_POST['shortcode'], array_merge($_POST, $_POST[$_POST['shortcode']]))) {
			wp_redirect(admin_url('admin.php?page=placester_shortcodes'));
			die;
		}
		else {
			$notice = 'Error saving shortcode.';
		}
	}
	$post = $_POST;
}
$post = wp_parse_args($post, $post_def);

// for post edits, prepare the frame related variables (iframe and script)
if( $post_ID ) {
	$iframe_controller = '<script id="plwidget-' . $post_ID . '" src="' . PL_PARENT_URL . 'js/fetch-widget.js?id=' . $post_ID . '"></script>';
}

// user & security
$user = wp_get_current_user();
if ( $user->exists() ) {
	$user_ID = $user->ID;
}
else {
	$user_ID = 0;
}
$nonce_action = 'update-' . $post_type . '_' . $post_ID;


?>
<div class="wrap pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Create Custom Shortcode'); ?>

	<div id="pl_sc_edit">
		<?php if ( $notice ) : ?>
		<div id="notice" class="error"><p><?php echo $notice ?></p></div>
		<?php endif; ?>
		<?php if ( $message ) : ?>
		<div id="message" class="updated"><p><?php echo $message; ?></p></div>
		<?php endif; ?>
		<form name="sc_edit_form" action="<?php echo $form_link?>" method="post">
			<?php wp_nonce_field($nonce_action); ?>
			<input type="hidden" name="user_ID" value="<?php echo $user_ID; ?>" />
			<input type="hidden" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" name="ID" value="<?php echo esc_attr($post_ID) ?>" />
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div id="titlediv">
							<div id="titlewrap">
								<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo __( 'Enter title here' ); ?></label>
								<input type="text" name="post_title" size="30" value="<?php echo esc_attr( htmlspecialchars( $post['post_title'] ) ); ?>" id="title" autocomplete="off" title="<?php _e('Please enter a title for this shortcode.')?>" />
							</div>
							<div class="inside">
								<div id="sc_slug_box" class="hide-if-no-js">
									<div class="iframe_link"></div>
									<div class="shortcode_link"></div>
								</div>
							</div>
						</div><!-- /titlediv -->
						<div id="normal-sortables" class="meta-box-sortables">
							<?php PL_Router::load_builder_partial('shortcode-create-box.php', array('values'=>$post,'pl_shortcodes'=>$pl_shortcodes));?>
						</div>
					</div>
					<div id="postbox-container-1" class="postbox-container">
					
						<?php 
							if ( 0 == $post_ID ) {
								$save_title = __('Create');
							}
							else {
								$save_title = __('Update');
							}
						?>
						<div id="submitdiv" class="postbox">
							<h3 class="hndle"><span><?php echo $save_title;?></span></h3>
							<div class="inside">
								<div class="submitbox" id="submitpost">
								
									<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
									<div style="display:none;">
									<?php submit_button( __( 'Save' ), 'button', 'save' ); ?>
									</div>
								
									<div id="major-publishing-actions">
										
										<div id="delete-action">
										<?php if ( $post_ID && current_user_can( "delete_post", $post_ID ) ): ?>
											<?php if ( !EMPTY_TRASH_DAYS ): ?>
												<?php $delete_text = __('Delete Permanently'); ?>
											<?php else: ?>
												<?php $delete_text = __('Move to Trash'); ?>
											<?php endif;?>
											<a class="submitdelete deletion" href="<?php echo get_delete_post_link($post_ID); ?>"><?php echo $delete_text; ?></a>
										<?php endif ?>
										</div>
									
										<div id="publishing-action">
											<span class="spinner"></span>
											<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
											<?php submit_button( $save_title, 'button button-primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
										</div>
										
										<div class="clear"></div>
									</div>
								
								</div>
							</div>
						</div>					
					
						<?php
						// preview pane
						PL_Router::load_builder_partial('shortcode-preview.php', array('post'=>$post));
						// link for template editing
						?>
						<script type="text/javascript">
							var pl_sc_template_url = '<?php echo admin_url('admin.php?page=placester_shortcodes_template_edit')?>';
						</script>
					</div>
				</div><!-- /post-body -->
			</div>
		</form>

		<div id="pl-fl-meta" style="display: none;">
			<?php
				// featured listings dialog
				$static_list_form = PL_Form::generate_form(
							PL_Config::PL_API_LISTINGS('get', 'args'),
							array(	'method' => "POST",
									'title' => true,
									'wrap_form' => false,
									'echo_form' => false,
									'include_submit' => false,
									'id' => 'pls_admin_my_listings'),
							'sc_edit_');

				echo $static_list_form;
			 ?>
		</div>

		<div id="ajax-response"></div>
		<br class="clear" />
	</div>
</div>