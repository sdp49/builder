<?php
/**
 * Displays meta box used in the shortcode template edit view
 */

$pl_shortcode_types = PL_General_Widget_CPT::$post_types; 
$pl_shortcode_codes = PL_General_Widget_CPT::$codes;

$current_type = '';
?>

<div id="pl-controls-metabox-id"
  class="postbox ">
  <h3>Create Shortcode Template</h3>

  <div id="edit-template-metabox-inner" class="inside shortcode_container">

    <!-- Template Name -->
    <section id="edit-template-choose-name" class="row-fluid">

      <div class="span2">
        <label for="edit-template-name" class="section-label">Template Name:</label>
      </div>

      <div class="span10">
        <input type="text" id="title" class="snippet_name new_snippet_name" title="<?php _e('Please enter a name for this shortcode template.')?>" />
      </div>

    </section>
    <!-- /#edit-template-choose-name -->

    <!-- Template Name -->
    <section id="edit-template-choose-template" class="row-fluid">

      <div class="span2">
        <label for="edit-template-name" class="section-label">Template Type:</label>
      </div>

      <div class="span10">
        <select id="template-type" class="chosen">
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

    </section>
    <!-- /#edit-template-choose-template -->

    <hr class="clearfix" />

    <!-- Template Contents -->
    <section id="edit-template-contents" class="row-fluid">

      <!-- Template HTML/CSS -->
      <div id="edit-html-css" class="span8 area_snippet">

        <!-- Use existing template lightbox -->
        <a href="#">Use existing template as a base for this new template</a>

        <!-- Add HTML -->
        <label for="html-textarea">HTML</label>
        <textarea id="html-textarea"></textarea>

        <!-- Add CSS -->
        <label for="css-textarea">CSS</label>
        <textarea id="css-textarea"></textarea>

        <!-- Add Content Before Widget -->
        <a href="#" id="toggle-before-widget" class="clearfix">Add content before the widget</a>
        <div id="before-widget-wrapper">
          <textarea id="before-widget-textarea"></textarea>
        </div>
        
        <!-- Add Content After Widget -->
        <a href="#" id="toggle-after-widget" class="clearfix">Add content after the widget</a>
        <div id="after-widget-wrapper">
          <textarea id="after-widget-textarea"></textarea>
        </div>

        <!-- Save Button -->
        <input type="button" tabindex="2" value="Create" class="button-primary save_snippet" />

      </div>

      <!-- Search Sub-Shortcodes -->
      <div id="subshortcodes" class="span4">

        <label for="search-subshortcodes">Sub-Shortcodes</label> 
        <input type="text" placeholder="search sub-shortcodes" />
        <select multiple>
        </select>
      </div>
      
    </section>
    <!-- /#edit-template-html-css -->

  </div>
  <!-- /edit-template-metabox-inner -->

</div>
