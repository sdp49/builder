<?php

class PL_Map_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public  $fields = array(
			'type' => array( 'type' => 'select', 'label' => 'Map Type', 'options' => array( 
																	'listings' => 'listings',
																	 'lifestyle' => 'lifestyle',
																	'lifestyle_poligon' => 'lifestyle_poligon' ) ),
			'width' => array( 'type' => 'text', 'label' => 'Width' ),
			'height' => array( 'type' => 'text', 'label' => 'Height' ),
	);

	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Maps', 'pls' ),
						'singular_name' => __( 'pl_map', 'pls' ),
						'add_new_item' => __('Add New Map', 'pls'),
						'edit_item' => __('Edit Map', 'pls'),
						'new_item' => __('New Map', 'pls'),
						'all_items' => __('All Maps', 'pls'),
						'view_item' => __('View Maps', 'pls'),
						'search_items' => __('Search Maps', 'pls'),
						'not_found' =>  __('No maps found', 'pls'),
						'not_found_in_trash' => __('No maps found in Trash', 'pls')),
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
	
		register_post_type('pl_map', $args );
	}
	
	
	public  function meta_box() {
		add_meta_box( 'my-meta-box-id', 'Maps', array( $this, 'pl_maps_meta_box_cb'), 'pl_map', 'normal', 'high' );
	}
	
	// add meta box for featured listings- adding custom fields
	public  function pl_maps_meta_box_cb( $post ) {
		$values = get_post_custom( $post->ID );
		
		$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? unserialize($values['pl_featured_listing_meta'][0]) : '';
		$pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? '' : $pl_featured_listing_meta['featured-listings-type'];
		

		// get link for iframe
		$permalink = '';
		if( isset( $_GET['post'] ) ) {
			$permalink = get_permalink($post->ID);
		}
		
		$width =  isset( $values['width'] ) && ! empty( $values['width'][0] ) ? $values['width'][0] : '300';
		$height = isset( $values['height'] ) && ! empty( $values['height'][0] ) ? $values['height'][0] : '300';
		$style = ' style="width: ' . $width . 'px; height: ' . $height . 'px" ';
		
		if( ! empty( $permalink ) ):
		$iframe = '<iframe src="' . $permalink . '"'. $style . '></iframe>';
		?>		<div id="iframe_code">
					<h2>Map Frame code</h2>
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
		
		<?php $atts = array();
		
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
				'codes' => array( 'search_map' ),
				'p_codes' => array(
					'search_map' => 'Search Map'
				)
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
			}
		}
		
		if( isset( $_POST['pl_featured_listing_meta'] ) ) {
			update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
		}
	}
	
	public static function post_type_templating( $single ) {
		global $post;
		
		if( ! empty( $post ) && $post->post_type === 'pl_map' ) {
			$args = '';
			$meta = get_post_custom( $post->ID );
			
			foreach( $meta as $key => $value ) {
				// ignore underscored private meta keys from WP
				if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) ) {
					if( 'pl_static_listings_option' !== $key  && 'pl_featured_listing_meta' !== $key) {
						$args .= "$key = '{$value[0]}' ";
					}
				}
			}
			$args .= ' map_id="' . $post->ID . '"';
			
			$shortcode = '[search_map ' . $args . ']'; 
			
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
					
			die();
		}
	}
}

new PL_Map_CPT();