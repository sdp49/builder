<?php


class PL_Neighborhood_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public $fields = array(
// 			'nb_type' => array( 'type' => 'radio', 'label' => 'Neighborhood type', 'options' => array( 
// 												'city' => 'city',
// 												'state' => 'state',
// 												'neighborhood' => 'neighborhood',
// 												'zip' => 'zip' 
// 										) 
// 								),
	);

	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Neighborhoods', 'pls' ),
						'singular_name' => __( 'neighborhood', 'pls' ),
						'add_new_item' => __('Add New Neighborhood', 'pls'),
						'edit_item' => __('Edit Neighborhood', 'pls'),
						'new_item' => __('New Neighborhood', 'pls'),
						'all_items' => __('All Neighborhoods', 'pls'),
						'view_item' => __('View Neighborhoods', 'pls'),
						'search_items' => __('Search Neighborhoods', 'pls'),
						'not_found' =>  __('No neighborhoods found', 'pls'),
						'not_found_in_trash' => __('No neighborhoods found in Trash', 'pls')),
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
	
		register_post_type('pl_neighborhood', $args );
	}
	
	
	public function meta_box() {
		add_meta_box( 'my-meta-box-id', 'Page Subtitle', array( $this, 'pl_neighborhoods_meta_box_cb'), 'pl_neighborhood', 'normal', 'high' );
	}
	
	// add meta box for featured listings- adding custom fields
	public function pl_neighborhoods_meta_box_cb( $post ) {
		$values = get_post_custom( $post->ID );
		
		// get radio values
		$radio_def = isset( $values['radio-type'] ) ? $values['radio-type'][0] : 'state';
		$select_id = 'nb-select-' . $radio_def;
		$select_def = isset( $values[ $select_id ] ) ? $values[ $select_id ][0] : '0';
		
// 		var_dump($values); die();
// 		var_dump($radio_def);
// 		var_dump($select_def);
		
		// get meta values from custom fields
		foreach( $this->fields as $field => $arguments ) {
			$value = isset( $values[$field] ) ? $values[$field][0] : '';
		
			if( !empty( $value ) && empty( $_POST[$field] ) ) {
				$_POST[$field] = $value;
			}
				
			echo PL_Form::item($field, $arguments, 'POST');
		}
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#<?php echo $radio_def; ?>').attr('checked', true);
		$('#nb-taxonomy-<?php echo $radio_def; ?>').css('display', 'block');
		$('#nb-id-select-<?php echo $radio_def; ?>').val(<?php echo $select_def; ?>);

		$('#pl_location_tax input:radio').on('click', radioClicks);

		function radioClicks() {
			var radio_value = this.value;

			$('.nb-taxonomy').each(function() {
				if( this.id.indexOf(radio_value, this.id.length - radio_value.length) !== -1 ) {
					$(this).css('display', 'block');
				} else {
					$(this).css('display', 'none');
				}
			});
		}

	});
	</script>	
		
	<?php 	
		wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );
		
		echo '<div id="pl_location_tax">';
		echo PL_Taxonomy_Helper::taxonomies_as_checkboxes(); 
		echo '</div>';
		
		$taxonomies = PL_Taxonomy_Helper::$location_taxonomies;
		
		foreach( $taxonomies as $slug => $label ) {
			$terms = PL_Taxonomy_Helper::get_taxonomy_items( $slug );
			
			echo "<div id='nb-taxonomy-$slug' class='nb-taxonomy' style='display: none;'>";
				echo "<select id='nb-id-select-$slug' name='nb-select-$slug'>";
					foreach( $terms as $term ) {
						echo "<option value='" . $term['term_id'] . "'>" . $term['name'] . "</option>";
					}
				echo "</select>";
			echo "</div>";
		}
		
		//echo PL_Taxonomy_Helper::get_taxonomy_items('state') );
		
		PL_Snippet_Template::prepare_template(
			array(
				'codes' => array( 'pl_neighborhood' ),
					'p_codes' => array(
					'pl_neighborhood' => 'Neighborhood'
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
		
		// persist radio box and dropdown
		if( isset( $_POST['type'] ) ) {
			$radio_type = $_POST['type'];
			$select_type = 'nb-select-' . $radio_type;
			if( isset( $_POST[$select_type] ) ) {
				// persist radio box storage based on what is saved
				update_post_meta( $post_id, 'radio-type', $_POST['type'] );
				update_post_meta( $post_id, $select_type, $_POST[ $select_type ] );
			}		
		}	
		
		if( isset( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template']);
		}
	}
	
	public function post_type_templating( $single ) {
		
	}
}

new PL_Neighborhood_CPT();