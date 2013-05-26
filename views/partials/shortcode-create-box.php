<?php
/**
 * Displays main shortcode edit meta box used in the shortcode edit view
 */

$is_post_new = true;
if( ! empty( $_GET['post'] ) ) {
  $is_post_new = false;
}

// get all CPT custom field values
$values = get_post_custom( $post->ID );

// read the post type
$pl_post_type = isset( $values['pl_post_type'] ) ? $values['pl_post_type'][0] : '';

$pl_shortcode_types = PL_General_Widget_CPT::$post_types; 
$pl_shortcode_fields = PL_General_Widget_CPT::$fields;

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
<div id="pl-controls-metabox-id" class="postbox ">
  
  <h3>Create Shortcode</h3>
  
  <div id="edit-sc-metabox-inner" class="inside">  

    <!-- Type and Template -->
    <div class="meta_section">

      <!-- Type -->
      <section id="edit-sc-choose-type" class="post_types_list_wrapper row-fluid">
        
        <div class="span2">
          <label class="section-label" for="pl_post_type_dropdown">Type:</label>
        </div>

        <div class="span9">

          <select id="pl_post_type_dropdown" name="pl_post_type_dropdown" class="">
            
            <option id="pl_post_type_undefined" value="pl_post_type_undefined">Select</option>
            
            <?php 
            $num_of_post_types = count( $pl_shortcode_types );
            $i = 0;
      
            foreach( $pl_shortcode_types as $post_type => $label ):
              $i++;
              $link_class = ($post_type == $pl_post_type) ? 'selected_type' : '';
              $selected = ( !empty($link_class) ) ? 'selected="selected"' : '';
              ?>
              <option id="pl_post_type_<?php echo $post_type; ?>" class="<?php echo $link_class; ?>" value="pl_post_type_<?php echo $post_type; ?>" <?php echo $selected; ?>>
                <?php echo $label; ?>
              </option>
              <?php
            endforeach;
            ?>
          </select>

        </div>
      
      </section><!-- /.post_types_list_wrapper -->

      <!-- Template / Layout -->
      <section id="edit-sc-choose-template" class="row-fluid">
        <div class="span2">
          <label class="section-label" for="pl_template">Template:</label>
        </div>
        <div class="span6">
          <?php foreach( PL_General_Widget_CPT::$codes as $code => $label ): ?>
            <div class="pl_template_block" id="<?php echo $code;?>_template_block" style="display: none;">
              <?php
              PL_Router::load_builder_partial('shortcode-template-list.php', array(
                    'codes' => array( $code ),
                    'p_codes' => array(
                      $code => $label
                    ),
                    'select_name' => 'pl_template_' . $code,
                    'class' => '',
                    'value' => $values['pl_cpt_template'][0],
                )
              );
              ?>
            </div>
            <?php add_action( 'pl_template_extra_styles', array( $this, 'update_template_block_styles' ) );?>
          <?php endforeach;?>
        </div>
        <div class="offset1 span3">
          <a href="<?php echo admin_url('admin.php?page=placester_shortcodes_template_edit')?>" id="create-new-template-link">(create new)</a>
        </div>
      </section><!-- /edit-sc-choose-template -->

    </div><!-- /#post_types_list -->


    <!-- Options / Filters -->
    <div id="widget-meta-wrapper" style="display: none;">
      <?php
      // read width/height and slideshow values
      $width =  isset( $values['width'] ) && ! empty( $values['width'][0] ) ? $values['width'][0] : '250';
      $_POST['width'] = $width;
      $height = isset( $values['height'] ) && ! empty( $values['height'][0] ) ? $values['height'][0] : '250';
      $_POST['height'] = $height;
      $animationSpeed = isset( $values['animationSpeed'] ) && ! empty( $values['animationSpeed'][0] ) ? $values['animationSpeed'][0] : '800';
      $_POST['animationSpeed'] = $animationSpeed;
      $widget_class = isset( $values['widget_class'] ) && ! empty( $values['widget_class'][0] ) ? 'class="'  . $values['widget_class'][0] . '"' : '';
      
      $style = ' style="width: ' . $width . 'px;height: ' . $height . 'px"';
      
      // for post edits, prepare the frame related variables (iframe and script)
      if( ! empty( $permalink ) ) {
        $iframe = '<iframe src="' . $permalink . '"'. $style . $widget_class .'></iframe>';
        $iframe_controller = '<script id="plwidget-' . $post->ID . '" src="' . PL_PARENT_URL . 'js/fetch-widget.js?id=' . $_GET['post'] . '"'  . $style . ' ' . $widget_class . '></script>';
      } 
      ?>

      <div class="pl_widget_block">
        
        <div class="pl_map pl_form pl_search_listings pl_slideshow pl_neighborhood featured_listings static_listings">
          <h3>Options:</h3>
        </div>
        <?php
        // get meta values from custom fields
        // fill POST array for the forms (required after new widget is created)
        foreach( $pl_shortcode_fields as $field => $arguments ) {
          $value = isset( $values[$field] ) ? $values[$field][0] : '';
      
          if( !empty( $value ) && empty( $_POST[$field] ) ) {
            $_POST[$field] = $value;
          }
      
          echo PL_Form::item($field, $arguments, 'POST', false, 'general_widget_');
        }
        ?>
      </div><!-- /.pl_widget_block -->

      <section class="featured_listings">
        <h2>Pick a Listing</h2>
      </section>

      <div id="pl-fl-meta">
        <div>
          <div id="pl_featured_listing_block" class="featured_listings pl_slideshow">
            <?php
              include PLS_OPTRM_DIR . '/views/featured-listings.php';
          
              // Generate the popup dialog with featured      
              echo pls_generate_featured_listings_ui(array(
                        'name' => 'Featured Meta',
                        'desc' => '',
                        'id' => 'featured-listings-type',
                        'type' => 'featured_listing'
                      ) ,
                      $pl_featured_meta_value,
                      'pl_featured_listing_meta');
                   
            ?>
          </div><!-- end of #pl_featured_listing_block -->
          <section id="pl_static_listing_block" class="static_listings pl_search_listings">
            <?php
              $static_list_form = PL_Form::generate_form(
                    PL_Config::PL_API_LISTINGS('get', 'args'),
                    array('method' => "POST", 
                        'title' => true,
                        'wrap_form' => false, 
                         'echo_form' => false, 
                        'include_submit' => false, 
                        'id' => 'pls_admin_my_listings'),
                    'general_widget_');
  
              echo $static_list_form;
             ?>
          </section><!-- end of #pl_static_listing_block -->
        </div>
      </div>
      <input type="hidden" name="pl_post_type" id="pl_post_type" value="pl_map" />
      <?php $atts = array();
      
      // get radio values for neighborhood
      $radio_def = isset( $values['radio-type'] ) ? $values['radio-type'][0] : 'state';
      $select_id = 'nb-select-' . $radio_def;
      $select_def = isset( $values[ $select_id ] ) ? $values[ $select_id ][0] : '0';
      ?>
      <script type="text/javascript">
        var previewPlaceholderHtml = '<img id="preview_load_spinner" src="<?php echo PL_PARENT_URL . 'images/preview_load_spin.gif'; ?>" alt="Widget options are Loading..." width="30px" height="30px" style="position: absolute; top: 100px; left: 100px" />';
        
        jQuery(document).ready(function($) {
          // manage neighborhood
          $('#<?php echo $radio_def; ?>').attr('checked', true);
          $('#nb-taxonomy-<?php echo $radio_def; ?>').css('display', 'block');
          $('#nb-id-select-<?php echo $radio_def; ?>').val(<?php echo $select_def; ?>);
      
          // populate slug box for the edit screen
          <?php if( ! $is_post_new ) { ?>
            $('#edit-slug-box').after('<div class="iframe-link"><strong>Embed Code:</strong> <?php echo esc_html( $iframe_controller ); ?></div><div class="shortcode-link"></div>');
            $('#pl_post_type_dropdown').trigger('change');
          <?php }  ?>
          
          // $('#pl_post_type_dropdown').trigger('change');
          $('#preview_load_spinner').remove();
          $('#preview-meta-widget').html('<?php echo isset($iframe) ? $iframe : '' ?>');
        });
      </script>  
        
      <?php wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );?>
    
      <section id="pl_location_tax" class="pl_neighborhood">
      <?php $taxonomies = PL_Taxonomy_Helper::get_taxonomies();?>
      <?php foreach ($taxonomies as $slug => $label): ?>
        <section>
          <input type="radio" id="<?php echo $slug ?>" name="radio-type" value="<?php echo $slug ?>">
          <label for="<?php echo $slug ?>"><?php echo $label ?></label>
        </section>
      <?php endforeach ?>  
      </section>
    
      <section class="pl_widget_block pl_neighborhood">
      <?php $taxonomies = PL_Taxonomy_Helper::$location_taxonomies;?>
      <?php foreach( $taxonomies as $slug => $label ): ?>
        <?php $terms = PL_Taxonomy_Helper::get_taxonomy_items( $slug ); ?>
        <div id="nb-taxonomy-<?php echo $slug;?>" class="nb-taxonomy" style="display: none;">
          <select id="nb-id-select-<?php echo $slug;?>" name="nb-select-<?php echo $slug;?>">
          <?php foreach( $terms as $term ): ?>
            <option value="<?php echo $term['term_id']?>"><?php echo $term['name'] ?></option>
          <?php endforeach;?>
          </select>
        </div>
      <?php endforeach;?>
      </section>
      
      <div class="clear"></div>
    
    </div> <!-- /#widget-meta-wrapper -->
  </div><!-- /.inside -->
</div><!-- /.postbox -->