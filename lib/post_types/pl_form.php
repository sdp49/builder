<?php

class PL_Form_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public  $fields = array(
				'width' => array( 'type' => 'text', 'label' => 'Width' ),
				'height' => array( 'type' => 'text', 'label' => 'Height' ),
				'context' => array( 'type' => 'text', 'label' => 'Context' ),
				'ajax' => array( 'type' => 'checkbox', 'label' => 'Disable AJAX' ),
				'formaction' => array( 'type' => 'text', 'label' => 'Form URL when AJAX is disabled' ),
				'modernizr' => array( 'type' => 'checkbox', 'label' => 'Drop Modernizr' ),
			);

	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Forms', 'pls' ),
						'singular_name' => __( 'pl_form', 'pls' ),
						'add_new_item' => __('Add New Form', 'pls'),
						'edit_item' => __('Edit Form', 'pls'),
						'new_item' => __('New Form', 'pls'),
						'all_items' => __('All Forms', 'pls'),
						'view_item' => __('View Forms', 'pls'),
						'search_items' => __('Search Forms', 'pls'),
						'not_found' =>  __('No forms found', 'pls'),
						'not_found_in_trash' => __('No forms found in Trash', 'pls')),
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
	
		register_post_type('pl_form', $args );
	}
	
	
	
	public  function meta_box() {
		add_meta_box( 'my-meta-box-id', 'Page Subtitle', array( $this, 'pl_forms_meta_box_cb' ), 'pl_form', 'normal', 'high' );
	}
	
	// add meta box for featured listings- adding custom fields
	public  function pl_forms_meta_box_cb( $post ) {
		$values = get_post_custom( $post->ID );
		
		// get link for iframe
		$permalink = '';
		if( isset( $_GET['post'] ) ) {
			$permalink = get_permalink($post->ID);
		}
		
		$width =  isset( $values['width'] ) && ! empty( $values['width'][0] ) ? $values['width'][0] : '600';
		$height = isset( $values['height'] ) && ! empty( $values['height'][0] ) ? $values['height'][0] : '600';
		$style = ' style="width: ' . $width . 'px; height: ' . $height . 'px" ';
		
		if( ! empty( $permalink ) ):
		$iframe = '<iframe src="' . $permalink . '"'. $style . '></iframe>';
		?>		<div id="iframe_code">
					<h2>Form Frame code</h2>
					<p>Use this code snippet inside of a page: <strong><?php echo esc_html( $iframe ); ?></strong></p>
					<em>By copying this code and pasting it into a page you display your view.</em>
				</div>
		<?php endif;
		
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
						'codes' => array( 'search_form' ),
						'p_codes' => array(
								'search_form' => 'Search Form'
						),
						'select_name' => 'pl_cpt_template',
						'value' => isset( $values['pl_cpt_template'] ) ? $values['pl_cpt_template'][0] : ''
				)
			);
	}
	
	public  function meta_box_save( $post_id ) {
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
		
		if( isset( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template']);
		}
	}

	public function post_type_templating( $single ) {
		global $post;
		
		if( ! empty( $post ) && $post->post_type === 'pl_form' ) {
			$args = '';
			$meta = get_post_custom( $post->ID );
				
			foreach( $meta as $key => $value ) {
				// ignore underscored private meta keys from WP
				if( $key === 'pl_cpt_template' ) {
					$args .= "context='{$value[0]}' ";
				}
				else if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) && ( $key !== 'context' ) ) {
					$args .= "$key = '{$value[0]}' ";
				}
				if( $key === 'modernizr' && $value[0] == 'true' ) {
					$drop_modernizr = true;
				}
			}
			
			$shortcode = '[search_form ' . $args . '] [search_listings]';
			
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
				
			die();
		}
	}
}

new PL_Form_CPT();