<?php
/**
 * Meta box for saving a post.
 * Based on the publish box in /wp-admin/includes/meta-boxes.php
 */

global $action;

$post_type = $post->post_type;
$post_type_object = get_post_type_object($post_type);
$can_publish = current_user_can($post_type_object->cap->publish_posts);

if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
	$title = __('Create');
}
else {
	$title = __('Update');
}

?>
<div id="submitdiv" class="postbox">
	<h3 class="hndle"><span><?php echo $title;?></span></h3>
	<div class="inside">
		<div class="submitbox" id="submitpost">
		
			<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
			<div style="display:none;">
			<?php submit_button( __( 'Save' ), 'button', 'save' ); ?>
			</div>
		
			<div id="major-publishing-actions">
				<div id="delete-action">
				<?php if ( current_user_can( "delete_post", $post->ID ) ): ?>
					<?php if ( !EMPTY_TRASH_DAYS ): ?>
						<?php $delete_text = __('Delete Permanently'); ?>
					<?php else: ?>
						<?php $delete_text = __('Move to Trash'); ?>
					<?php endif;?>
					<a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a>
				<?php endif ?>
				</div>
			
				<div id="publishing-action">
					<span class="spinner"></span>
					<?php if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ): ?>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>" />
						<?php submit_button( __( 'Create' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'p' ) ); ?>
					<?php else:?>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Save') ?>" />
						<input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e('Save') ?>" />
					<?php endif; ?>
				</div>
				<div class="clear"></div>
			</div>
		
		</div>
	</div>
</div>