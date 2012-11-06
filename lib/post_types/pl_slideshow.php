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
		
		$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? unserialize($values['pl_featured_listing_meta'][0]) : '';
		$pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? '' : $pl_featured_listing_meta['featured-listings-type'];
		
		
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
					<h2>Slideshow Frame code</h2>
					<p>Use this code snippet inside of a page: <strong><?php echo esc_html( $iframe ); ?></strong></p>
					<em>By copying this code and pasting it into a page you display your view.</em>
				</div>
		<?php endif; ?>	
				<h2>Pick a Listing</h2>
				<div id="pl-fl-meta">
					<div style="width: 400px; min-height: 200px">
						<div id="pl_featured_listing_block">
						<?php 
							include PLS_OPTRM_DIR . '/views/featured-listings.php';
							// Enqueue all required stylings and scripts
							wp_enqueue_style('featured-listings', OPTIONS_FRAMEWORK_DIRECTORY.'css/featured-listings.css');
							
							wp_register_script( 'datatable', trailingslashit( PLS_JS_URL ) . 'libs/datatables/jquery.dataTables.js' , array( 'jquery'), NULL, true );
							wp_enqueue_script('datatable'); 
							wp_enqueue_script('jquery-ui-core');
							wp_enqueue_style('jquery-ui-dialog', OPTIONS_FRAMEWORK_DIRECTORY.'css/jquery-ui-1.8.22.custom.css');
							wp_enqueue_script('jquery-ui-dialog');
							wp_enqueue_script('options-custom', OPTIONS_FRAMEWORK_DIRECTORY.'js/options-custom.js', array('jquery'));
							wp_enqueue_script('featured-listing', OPTIONS_FRAMEWORK_DIRECTORY.'js/featured-listing.js', array('jquery'));
					
							// Generate the popup dialog with featured			
							echo pls_generate_featured_listings_ui(array(
												'name' => 'Featured Meta',
												'desc' => '',
												'id' => 'featured-listings-type',
												'type' => 'featured_listing'
												) ,$pl_featured_meta_value
												, 'pl_featured_listing_meta');
						?>
						</div><!-- end of #pl_featured_listing_block -->
						<div id="pl_static_listing_block" style="display: none;">
							<?php echo PL_Form::generate_form(
										PL_Config::PL_API_LISTINGS('get', 'args'),
										array('method' => "POST", 
												'title' => true,
												'wrap_form' => false, 
										 		'echo_form' => false, 
												'include_submit' => false, 
												'id' => 'pls_admin_my_listings')); ?>
						</div><!-- end of #pl_static_listing_block -->
					</div>
				<div>
		
		<?php 
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
				),
				'select_name' => 'pl_cpt_template',
				'value' => isset( $values['pl_cpt_template'] ) ? $values['pl_cpt_template'][0] : ''
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
		
		if( isset( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template']);
		}
		
		if( isset( $_POST['pl_featured_listing_meta'] ) ) {
			update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
		}
	}
	
	public static function post_type_templating( $single, $skipdb = false ) {
		global $post;
		
		unset( $_GET['skipdb'] );
		$meta = $_GET;
		
		if( ! empty( $post ) && $post->post_type === 'pl_slideshow' ) {
			$args = '';
			// verify if skipdb param is passed
			if( ! $skipdb ) {
				$meta_custom = get_post_custom( $post->ID );
				$meta = array_merge( $meta_custom, $meta );
			}
			
			if( isset( $meta['pl_static_listings_option'] ) ) { unset( $meta['pl_static_listings_option'] ); }

			foreach( $meta as $key => $value ) {
				// if featured listings, pass to slideshow args	
				if( $key === 'pl_featured_listing_meta' && ! empty( $value ) ) {
					$args .= "post_meta_key = 'pl_featured_listing_meta' ";
				}
				// ignore underscored private meta keys from WP
				else if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) ) {
					if( is_array( $value ) ) {
						// handle meta values as arrays
						$args .= "$key = '{$value[0]}' ";
					} else {
						// handle _GET vars as strings
						$args .= "$key = '{$value}' ";
					}
				}
			}
			
			$args .= "post_id = '{$post->ID}'";
				
			$shortcode = '[listing_slideshow ' . $args . ']';
				
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
				
			die();
		}
		
		if( isset( $_POST['pl_featured_listing_meta'] ) ) {
			update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
		}
	}
}

new PL_Slideshow_CPT();