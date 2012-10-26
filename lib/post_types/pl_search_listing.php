<?php

class PL_Search_Listing_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public  $fields = array(
			'width' => array( 'type' => 'text', 'label' => 'Width' ),
			'height' => array( 'type' => 'text', 'label' => 'Height' ),
// 			'field1' => array( 'type' => 'text', 'label' => 'Field 1' ),
// 			'field2' => array( 'type' => 'select', 'label' => 'Field 2', 'options' => array( 'one' => 'one', 'two' => 'two' ) ),
// 			'field3' => array( 'type' => 'checkbox', 'label' => 'Field 3' ),
	);

	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Search Listings', 'pls' ),
						'singular_name' => __( 'search_listing', 'pls' ),
						'add_new_item' => __('Add New Search Listing', 'pls'),
						'edit_item' => __('Edit Search Listing', 'pls'),
						'new_item' => __('New Search Listing', 'pls'),
						'all_items' => __('All Search Listings', 'pls'),
						'view_item' => __('View Search Listings', 'pls'),
						'search_items' => __('Search Search Listings', 'pls'),
						'not_found' =>  __('No search listings found', 'pls'),
						'not_found_in_trash' => __('No search listings found in Trash', 'pls')),
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
	
		register_post_type('pl_search_listing', $args );
	}
	
	
	public function meta_box() {
		add_meta_box( 'my-meta-box-id', 'Page Subtitle', array( $this, 'pl_search_listings_meta_box_cb' ), 'pl_search_listing', 'normal', 'high' );
	}
	
	// add meta box for featured listings- adding custom fields
	public function pl_search_listings_meta_box_cb( $post ) {
		$values = get_post_custom( $post->ID );
		
		// get link for iframe
		$permalink = '';
		if( isset( $_GET['post'] ) ) {
			$permalink = get_permalink($post->ID);
		}
		
		$pl_static_listings_option = isset( $values['pl_static_listings_option'] ) ? unserialize($values['pl_static_listings_option'][0]) : '';
		if( is_array( $pl_static_listings_option ) ) {
			foreach( $pl_static_listings_option as $key => $value ) {
				$_POST[$key] = $value;
			}
		}
		
		$width =  isset( $values['width'] ) && ! empty( $values['width'][0] ) ? $values['width'][0] : '300';
		$height = isset( $values['height'] ) && ! empty( $values['height'][0] ) ? $values['height'][0] : '300';
		$style = ' style="width: ' . $width . 'px; height: ' . $height . 'px" ';
		
		if( ! empty( $permalink ) ):
		$iframe = '<iframe src="' . $permalink . '"'. $style . '></iframe>';
		
		?>		<div id="iframe_code">
					<h2>Search Listing Frame code</h2>
					<p>Use this code snippet inside of a page: <strong><?php echo esc_html( $iframe ); ?></strong></p>
					<em>By copying this code and pasting it into a page you display your view.</em>
				</div>
				
		<?php endif; ?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#pl_static_listing_block #advanced').css('display', 'none');
			$('#pl_static_listing_block #amenities').css('display', 'none');
			$('#pl_static_listing_block #custom').css('display', 'none');
			$('<a href="#basic" id="pl_show_advanced" style="line-height: 50px;">Show Advanced filters</a>').insertBefore('#pl_static_listing_block #advanced');

			$('#pl_show_advanced').click(function() {
				$(this).css('display', 'none');
				$('#pl_static_listing_block #advanced').css('display', 'block');
				$('#pl_static_listing_block #amenities').css('display', 'block');
				$('#pl_static_listing_block #custom').css('display', 'block');
			});
		});
		</script>
			<div id="pl_static_listing_block">
				<?php echo PL_Form::generate_form(
							PL_Config::PL_API_LISTINGS('get', 'args'),
							array('method' => "POST", 
									'title' => true,
									'wrap_form' => false, 
							 		'echo_form' => false, 
									'include_submit' => false, 
									'id' => 'pls_admin_my_listings')); ?>
				</div><!-- end of #pl_static_listing_block -->
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
				'codes' => array( 'search_listings' ),
				'p_codes' => array(
					'search_listings' => 'Search Listings'
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
			if( !empty( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, $_POST[$field] );
			}
		}
		
		$static_listings_option = array();
		
		// Save search form fields if not empty
		if( ! empty( $_POST['listing_types'] ) && 'false' !== $_POST['listing_types'] ) { $static_listings_option['listing_types'] = $_POST['listing_types']; }
		if( ! empty( $_POST['zoning_types'] ) &&  'false' !== $_POST['zoning_types'] ) { $static_listings_option['zoning_types'] = $_POST['zoning_types']; }
		if( ! empty( $_POST['purchase_types'] ) && 'false' !== $_POST['purchase_types'] ) { $static_listings_option['purchase_types'] = $_POST['purchase_types']; }
		
		if( isset( $_POST['location'] ) && is_array( $_POST['location'] ) ) {
			foreach( $_POST['location'] as $key => $value ) {
				if( ! empty( $value ) ) {
					$static_listings_option['location'][$key] = $value;
				}
			}
		}
		
		if( isset( $_POST['metadata'] ) && is_array( $_POST['metadata'] ) ) {
			foreach( $_POST['metadata'] as $key => $value ) {
				if( ! empty( $value ) ) {
					$static_listings_option['metadata'][$key] = $value;
				}
			}
		}
		
		update_post_meta( $post_id, 'pl_static_listings_option', $static_listings_option );
		
		if( isset( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template']);
		}
	}
	
	public function post_type_templating( $single ) {
		global $post;
		
		if( ! empty( $post ) && $post->post_type === 'pl_search_listing' ) {
			$args = '';
			$meta = get_post_custom( $post->ID );
		
			foreach( $meta as $key => $value ) {
				if( $key === 'pl_cpt_template' ) {
					$args .= "context='{$value[0]}' ";
				}
// 				// ignore underscored private meta keys from WP
// 				else if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) && ( $key !== 'context' ) ) {
// 					$args .= "$key = '{$value[0]}' ";
// 				}
// 				if( $key === 'modernizr' && $value[0] == 'true' ) {
// 					$drop_modernizr = true;
// 				}
			}

			$shortcode = '[search_listings ' . $args . ']';
			
			// prepare filters
			if( isset( $meta['pl_static_listings_option'] ) ) {
				$filters = unserialize( $meta['pl_static_listings_option'][0] ); 				

				if( is_array( $filters) ) {
					foreach( $filters as $top_key => $top_value ) {
						if( is_array( $top_value ) ) {
							foreach( $top_value as $key => $value ) {
								$shortcode .= ' [pl_filter group="' . $top_key. '" filter="' . $key . '" value="' . $value . '"] ';
							}
						} else {
							$shortcode .= ' [pl_filter filter="' . $top_key . '" value="'. $top_value . '"] ';
						}
					}
				}
			}
			
			$shortcode .= '[/search_listings]';
			
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
		
			die();
		}
	}
}

new PL_Search_Listing_CPT();