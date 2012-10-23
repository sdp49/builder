<?php


class PL_Slideshow_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public $fields = array(
			'width' => array( 'type' => 'text', 'label' => 'Width' ),
			'height' => array( 'type' => 'text', 'label' => 'Height' ),
			'animation' => array( 'type' => 'select', 'label' => 'Animation', 'options' => array( 
									'fade' => 'fade',
									'horizontal-slide' => 'horizontal-slide',
									'vertical-slide' => 'vertical-slide',
									'horizontal-push' => 'horizontal-push',
								) ),
			'animationSpeed' => array( 'type' => 'text', 'label' => 'Animation Speed' ),
			'timer' => array( 'type' => 'checkbox', 'label' => 'Timer' ),
			'pauseOnHover' => array( 'type' => 'checkbox', 'label' => 'Pause on hover' ),
	);
		
	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Slideshows', 'pls' ),
						'singular_name' => __( 'slideshow', 'pls' ),
						'add_new_item' => __('Add New Slideshow', 'pls'),
						'edit_item' => __('Edit Slideshow', 'pls'),
						'new_item' => __('New Slideshow', 'pls'),
						'all_items' => __('All Slideshows', 'pls'),
						'view_item' => __('View Slideshows', 'pls'),
						'search_items' => __('Search Slideshows', 'pls'),
						'not_found' =>  __('No slideshows found', 'pls'),
						'not_found_in_trash' => __('No slideshows found in Trash', 'pls')),
				'menu_icon' => trailingslashit(PL_IMG_URL) . 'featured.png',
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array('title', 'editor'),
				'taxonomies' => array('category', 'post_tag')
		);
	
		register_post_type('pl_slideshow', $args );
	}
	
	
	public function meta_box() {
		add_meta_box( 'my-meta-box-id', 'Page Subtitle', array( $this, 'pl_slideshows_meta_box_cb' ), 'pl_slideshow', 'normal', 'high' );
	}
	
	// add meta box for featured listings- adding custom fields
	public function pl_slideshows_meta_box_cb( $post ) {
		$values = get_post_custom( $post->ID );
		
		// get meta values from custom fields
		foreach( $this->fields as $field => $arguments ) {
			$value = isset( $values[$field] ) ? $values[$field][0] : '';
		
			if( !empty( $value ) && empty( $_POST[$field] ) ) {
				$_POST[$field] = $value;
			}
				
			echo PL_Form::item($field, $arguments, 'POST');
		}
		
		wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );
		
		PL_Snippet_Template::prepare_template(
			array(
				'codes' => array( 'listing_slideshow' ),
				'p_codes' => array(
					'listing_slideshow' => 'Listing Slideshow'
				)
			)
		);
	}
	
	public function meta_box_save( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
		// Verify nonces for ineffective calls
		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_cpt_meta_box_nonce' ) ) return;
	
		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_post' ) ) return;
	
		foreach( $this->fields as $field => $values ) {
			if( isset( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, $_POST[$field] );
			} else if( $values['type'] === 'checkbox' && ! isset( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, false );
			}
		}
	}
	
	public function post_type_templating( $single ) {
		global $post;
		
		if( ! empty( $post ) && $post->post_type === 'pl_slideshow' ) {
			$args = '';
			$meta = get_post_custom( $post->ID );
				
			foreach( $meta as $key => $value ) {
				// ignore underscored private meta keys from WP
				if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) ) {
					$args .= "$key = '{$value[0]}' ";
				}
			}
				
			$shortcode = '[listing_slideshow ' . $args . ']';
				
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
				
			die();
		}
	}
}

new PL_Slideshow_CPT();