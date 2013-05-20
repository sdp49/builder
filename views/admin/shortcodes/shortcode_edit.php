<?php
global $pagenow, $shortcode_subpages, $submenu_file, $parent_file, $plugin_page;

$post_ID = (int)(!empty($_REQUEST['post'])?$_REQUEST['post']:0);
$post = get_post($post_ID);
$post_type = 'pl_general_widget';
if (!$post) {
	$post = get_default_post_to_edit( $post_type, true );
}
if ( $post ) {
	$post_type_object = get_post_type_object( $post_type );
} 

require_once(ABSPATH.'wp-admin/includes/meta-boxes.php');
add_meta_box('submitdiv', __( 'Publish' ), 'post_submit_meta_box', $post_type, 'side', 'core');

do_action('add_meta_boxes', $post_type, $post);
do_action('add_meta_boxes_' . $post_type, $post);
do_action('do_meta_boxes', $post_type, 'normal', $post);
do_action('do_meta_boxes', $post_type, 'advanced', $post);
do_action('do_meta_boxes', $post_type, 'side', $post);
add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );


$notice = '';
$message = '';
$screen = get_current_screen();
$page = explode('_page_',$screen->base);
$form_link = $pagenow.'?page='.$page[1];
$form_action = 'editpost';
$nonce_action = 'update-post_' . $post_ID;


?>
<div class="wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Shortcode Settings'); ?>
	<?php if ( $notice ) : ?>
	<div id="notice" class="error"><p><?php echo $notice ?></p></div>
	<?php endif; ?>
	<?php if ( $message ) : ?>
	<div id="message" class="updated"><p><?php echo $message; ?></p></div>
	<?php endif; ?>
	<form name="post" action="<?php echo $form_link?>" method="post" id="post"<?php do_action('post_edit_form_tag'); ?>>
		<?php wp_nonce_field($nonce_action); ?>
		<input type="hidden" id="post_ID" name="post_ID" value="<?php echo esc_attr($post_ID) ?>" />
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div id="titlediv">
						<div id="titlewrap">
							<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo apply_filters( 'enter_title_here', __( 'Enter title here' ), $post ); ?></label>
							<input type="text" name="post_title" size="30" value="<?php echo esc_attr( htmlspecialchars( $post->post_title ) ); ?>" id="title" autocomplete="off" />
						</div>
						<div class="inside">
							<div id="edit-slug-box" class="hide-if-no-js">
							</div>
						</div>
						<?php
						wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
						?>
					</div><!-- /titlediv -->
					<?php do_meta_boxes($post_type, 'normal', $post); ?>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<?php 
					do_action('submitpost_box');
					do_meta_boxes($post_type, 'side', $post);
					?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
					<?php
					do_meta_boxes(null, 'normal', $post);
					do_action('edit_form_advanced');
					do_meta_boxes(null, 'advanced', $post);
					?>
				</div>
				<?php
				do_action('dbx_post_sidebar');
				?>
			</div><!-- /post-body -->
		</div>
	</form>
	
	<div id="ajax-response"></div>
	<br class="clear" />
</div>