<?php
global $shortcode_subpages;

$title = (empty($_REQUEST['id'])?'':$_REQUEST['id']);
$pl_post_type = ( empty($_REQUEST['type'])?'pl_map':$_REQUEST['type']);
$shortcode = PL_General_Widget_CPT::get_context_template($pl_post_type);

if ($shortcode) {
	// load snippets
	$snippets = PL_General_Widget_CPT::load_shortcode_template($shortcode, $title);
}
else {
	$pl_post_type = 'pl_map';
	$snippets = array();
}

// create a temprary post that we can use to preview the template
$post_ID = wp_insert_post(array('post_type'=>'pl_general_widget'));
$post = get_post($post_ID);

$notice = '';
$message = '';

$form_link = '';
$form_action = 'editpost';
$nonce_action = 'update-post_' . $post_ID;

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
			<input type="hidden" id="post_type" name="post_type" value="pl_general_widget" />
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
						<?php
						PL_Router::load_builder_partial('save-box-side.php', array('post'=>$post));
						// read width/height and slideshow values
						$width =	isset( $values['width'] ) && ! empty( $values['width'][0] ) ? $values['width'][0] : '250';
						$_POST['width'] = $width;
						$height = isset( $values['height'] ) && ! empty( $values['height'][0] ) ? $values['height'][0] : '250';
						$_POST['height'] = $height;
						$animationSpeed = isset( $values['animationSpeed'] ) && ! empty( $values['animationSpeed'][0] ) ? $values['animationSpeed'][0] : '800';
						$_POST['animationSpeed'] = $animationSpeed;
						$widget_class = isset( $values['widget_class'] ) && ! empty( $values['widget_class'][0] ) ? 'class="'	. $values['widget_class'][0] . '"' : '';
						
						$style = ' style="width: ' . $width . 'px;height: ' . $height . 'px"';
						
						// for post edits, prepare the frame related variables (iframe and script)
						if( ! empty( $permalink ) ) {
							$iframe = '<iframe src="' . $permalink . '"'. $style . $widget_class .'></iframe>';
							$iframe_controller = '<script id="plwidget-' . $post->ID . '" src="' . PL_PARENT_URL . 'js/fetch-widget.js?id=' . $post->ID . '"'	. $style . ' ' . $widget_class . '></script>';
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