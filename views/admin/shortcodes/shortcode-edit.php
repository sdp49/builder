<?php
global $shortcode_subpages;

$post_ID = (int)(!empty($_REQUEST['post'])?$_REQUEST['post']:0);
$post = get_post($post_ID);
$post_type = 'pl_general_widget';
if (!$post) {
	// creating
	$post_ID = wp_insert_post( array('post_type'=>$post_type));
	$post = get_post($post_ID);
}
if ( $post ) {
	$post_type_object = get_post_type_object( $post_type );
}

$is_post_new = true;
if( ! empty( $_GET['post'] ) ) {
	$is_post_new = false;
}
$notice = '';
$message = '';
$form_link = 'post.php';
$form_action = 'editpost';
$nonce_action = 'update-' . $post_type . '_' . $post_ID;

$user = wp_get_current_user();
if ( $user->exists() ) {
	$user_ID = $user->ID;
}
else {
	$user_ID = 0;
}

// manage featured and static listing form values
$pl_featured_meta_value = '';
if( ! empty( $values['pl_featured_listing_meta'] ) ) {
	if( is_array( $values['pl_featured_listing_meta'] ) ) {
		$pl_featured_meta_value = $values['pl_featured_listing_meta'][0];
		$pl_featured_meta_value = @unserialize( $pl_featured_meta_value );

		if( false === $pl_featured_meta_value ) {
			$pl_featured_meta_value = @json_decode( $values['pl_featured_listing_meta'][0], true );
		} else if( is_array( $pl_featured_meta_value ) && isset( $pl_featured_meta_value[0] ) ) {
			$pl_featured_meta_value = $pl_featured_meta_value[0];
		}
		if(is_array( $pl_featured_meta_value ) && isset( $pl_featured_meta_value['featured-listings-type'] )) {
			$pl_featured_meta_value = $pl_featured_meta_value['featured-listings-type'];
		}
	} else if(isset( $values['pl_featured_listing_meta']['featured-listings-type'] )) {
		$pl_featured_meta_value = $values['pl_featured_listing_meta']['featured-listings-type'];
	}
}
$_POST['pl_featured_meta_value'] = $pl_featured_meta_value;

$pl_static_listings_option = isset( $values['pl_static_listings_option'] ) ? unserialize($values['pl_static_listings_option'][0]) : '';
if( is_array( $pl_static_listings_option ) ) {
	foreach( $pl_static_listings_option as $key => $value ) {
		if( ! empty( $value ) ) {
			$_POST[$key] = $value;
		}
	}
}


// get link for iframe
$permalink = '';
if( ! $is_post_new ) {
	$permalink = get_permalink($post->ID);
}

?>
<div class="wrap pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Shortcode Settings'); ?>
 
	<div id="pl_sc_edit">
		<?php if ( $notice ) : ?>
		<div id="notice" class="error"><p><?php echo $notice ?></p></div>
		<?php endif; ?>
		<?php if ( $message ) : ?>
		<div id="message" class="updated"><p><?php echo $message; ?></p></div>
		<?php endif; ?>
		<form name="post" action="<?php echo $form_link?>" method="post" id="post"<?php do_action('post_edit_form_tag'); ?>>
			<?php wp_nonce_field($nonce_action); ?>
			<input type="hidden" id="user-id" name="user_ID" value="<?php echo $user_ID; ?>" />
			<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" id="post_ID" name="post_ID" value="<?php echo esc_attr($post_ID) ?>" />
			<input type="hidden" id="post_type" name="post_type" value="pl_general_widget" />
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div id="titlediv">
							<div id="titlewrap">
								<label class="screen-reader-text" id="title-prompt-text" for="title"><?php echo apply_filters( 'enter_title_here', __( 'Enter title here' ), $post ); ?></label>
								<input type="text" name="post_title" size="30" value="<?php echo esc_attr( htmlspecialchars( $post->post_title ) ); ?>" id="title" autocomplete="off" title="<?php _e('Please enter a title for this shortcode.')?>" />
							</div>
							<div class="inside">
								<div id="edit-slug-box" class="hide-if-no-js"></div>
							</div>
							<?php wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );?>
						</div><!-- /titlediv -->
						<div id="normal-sortables" class="meta-box-sortables">
							<?php PL_Router::load_builder_partial('shortcode-create-box.php', array('post'=>$post));?>
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
							var pl_sc_template_url = '<?php echo admin_url('admin.php?page=placester_shortcodes_template_edit')?>';
						</script>	
					</div>
				</div><!-- /post-body -->
			</div>
		</form>
		
		<div id="pl-fl-meta" style="display: none;">
			<?php
				$static_list_form = PL_Form::generate_form(
							PL_Config::PL_API_LISTINGS('get', 'args'),
							array(	'method' => "POST",
									'title' => true,
									'wrap_form' => false,
									'echo_form' => false,
									'include_submit' => false,
									'id' => 'pls_admin_my_listings'),
							'general_widget_');

				echo $static_list_form;
			 ?>
		</div>
		
		<div id="ajax-response"></div>
		<br class="clear" />
	</div>
</div>