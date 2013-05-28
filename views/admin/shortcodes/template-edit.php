<?php
global $pagenow, $shortcode_subpages, $submenu_file, $parent_file, $plugin_page;

$post_ID = wp_insert_post( array('post_type'=>$post_type));
$post = get_post($post_ID);


$notice = '';
$message = '';
$form_link = '';
$form_action = 'editpost';
$nonce_action = 'update-post_' . $post_ID;


// get link for iframe
$permalink = '';
if( ! $is_post_new ) {
	$permalink = get_permalink($post->ID);
}

?>
<div class="wrap pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Shortcode Settings'); ?>

	<div id="pl_shortcode_template_edit">
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
							<?php PL_Router::load_builder_partial('shortcode-template-box.php', array('post'=>$post));?>
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
							$iframe_controller = '<script id="plwidget-' . $post->ID . '" src="' . PL_PARENT_URL . 'js/fetch-widget.js?id=' . $_GET['post'] . '"'	. $style . ' ' . $widget_class . '></script>';
						} 
						PL_Router::load_builder_partial('shortcode-preview.php', array('post'=>$post));
						?>
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								// populate slug box for the edit screen
								<?php if( ! $is_post_new ) { ?>
									$('#edit-slug-box').after('<div class="iframe-link"><strong>Embed Code:</strong> <?php echo esc_html( $iframe_controller ); ?></div><div class="shortcode-link"></div>');
									$('#pl_post_type_dropdown').trigger('change');
								<?php }	?>
								
								// $('#pl_post_type_dropdown').trigger('change');
								$('#preview-meta-widget').html('<?php echo isset($iframe) ? $iframe : '' ?>');
							});
						</script>	
					</div>
				</div><!-- /post-body -->
			</div>
		</form>
	
		<div id="ajax-response"></div>
	</div>
</div>