<?php

add_action( 'init', 'pl_register_featured_listing_post_type' );


function pl_register_featured_listing_post_type() {
	$args = array(
				'labels' => array(
					'name' => __( 'Featured Listings' ),
					'singular_name' => __( 'featured_listing' )),
					'public' => true,
					'publicly_queryable' => false,
					'show_ui' => true,
					'query_var' => true,
					'capability_type' => 'post',
					'hierarchical' => false,
					'menu_position' => null,
					'supports' => array('title'),
					'taxonomies' => array('category', 'post_tag')
			);
	
	register_post_type('featured_listing', $args );
}

add_action( 'add_meta_boxes', 'pl_featured_listings_meta_box' );

function pl_featured_listings_meta_box() {
	add_meta_box( 'my-meta-box-id', 'Page Subtitle', 'pl_featured_listings_meta_box_cb', 'featured_listing', 'normal', 'high' );
}

// add meta box for featured listings- adding custom fields
function pl_featured_listings_meta_box_cb( $post ) {
	$values = get_post_custom( $post->ID );
	$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? unserialize($values['pl_featured_listing_meta'][0]) : '';
	$pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? '' : $pl_featured_listing_meta['featured-listings-type'];

	$single_listing = isset( $values['pl_fl_meta_box_single_listing'] ) ? esc_attr( $values['pl_fl_meta_box_single_listing'][0] ) : '';
	wp_nonce_field( 'pl_fl_meta_box_nonce', 'meta_box_nonce' );
	?>
	<div id="pl-fl-meta">
		<div style="width: 400px; min-height: 200px">
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
			
// 				echo PLS_Featured_Listing_Option::init(array(
// 									'name' => 'Featured Meta',
// 									'desc' => '',
// 									'id' => 'featured-listings-type',
// 									'type' => 'featured_listing'
// 									) ,$pl_featured_meta_value
// 									, 'pl_featured_listing_meta');

			?>
		</div>
	</div>
<?php
}

add_action( 'save_post', 'pl_featured_listings_meta_box_save' );
function pl_featured_listings_meta_box_save( $post_id ) {
	// Avoid autosaves
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// Verify nonces for ineffective calls
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_fl_meta_box_nonce' ) ) return;
	
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	// Verify if the time field is set
	if( isset( $_POST['pl_featured_listing_meta'] ) ) {
		update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
	}
		
}
