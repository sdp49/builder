<?php

/***************************************************/
/* Messing around with the Theme Customizer API... */
/***************************************************/

add_action ('admin_menu', 'themedemo_admin');
function themedemo_admin() 
{
    // add the Customize link to the admin menu
    add_theme_page( 'Customize', 'Customize', 'edit_theme_options', 'customize.php' );
}

add_action( 'customize_register', 'placester_customize_register' );
function placester_customize_register( $wp_customize ) 
{
	$onboard = ( isset($_GET['onboard']) && strtolower($_GET['onboard']) == 'true' ) ? true : false;
	
	define_custom_controls();

	PL_Customizer::register_option_components( $wp_customize, $onboard );
	PL_Customizer::register_pl_components( $wp_customize );
}

// Can't nest class definitions in PHP, so these have to be placed in a global function...
function define_custom_controls() 
{	
	class PL_Customize_TextArea_Control extends WP_Customize_Control 
    {
        public $type = 'textarea';

        public function render_content() {
          ?>
            <label>
              <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
              <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
          <?php
        }
    }

   class PL_Customize_Typography_Control extends WP_Customize_Control
   {
   		public $type = 'typography';

   		public function render_content() {
   		  ?>
   		  	<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

   			<!-- Font Size -->
			<select class="of-typography of-typography-size" <?php $this->link('size'); ?> >
			
			  <?php for ($i = 9; $i < 71; $i++): 
				$size = $i . 'px'; ?>
				<option value="<?php echo esc_attr( $size ); ?>" <?php selected( $this->value('size'), $size ); ?>><?php echo $size; ?></option>
			  <?php endfor; ?>
			</select>
		
			<!-- Font Face -->
			<select class="of-typography of-typography-face" <?php $this->link('face'); ?> >

			<?php $faces = of_recognized_font_faces(); // Global function defined in Blueprint ?>

			  <?php foreach ( $faces as $key => $face ): ?>
			 	<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $this->value('face'), $key ); ?>><?php echo $face; ?></option>
			  <?php endforeach; ?>		
			</select>

			<!-- Font Style -->
			<select class="of-typography of-typography-style" <?php $this->link('style'); ?> >

			<?php $styles = of_recognized_font_styles(); // Global function defined in Blueprint ?>

			  <?php foreach ( $styles as $key => $style ): ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $this->value('style'), $keys ); ?>><?php echo $style; ?></option>
			  <?php endforeach; ?>
			</select>

			<!-- Font Color -->
			<!-- 
			<div id="colorpicker">
			  <div id="<?php //echo esc_attr( $this->id ); ?>_color_picker" class="another_colorpicker">
			    <div style="<?php //echo esc_attr( 'background-color:' . $this->value('color') ); ?>"></div>
			  </div>
			</div>  
 			-->
			<input type="text" class="of-color of-typography of-typography-color" value="<?php echo esc_attr( $this->value('color') ); ?>" id="<?php echo esc_attr( $this->id ); ?>_color" <?php $this->link('color'); ?> />
		  <?php
   		}
   }

   class PL_Customize_Integration_Control extends WP_Customize_Control
   {
   		public $type = 'integration';

   		public function render() {
   			PL_Router::load_builder_partial('integration-form.php', array('no_form' => true));
   			?>
   			  <!-- <div id="customize_integration_submit" style="width: 50px; height: 30px; background: grey;">Submit</div> -->

   			  <div class="row">
		        <input type="button" id="customize_integration_submit" class="button-primary" value="Submit" />
		      </div>
   			<?php

   			// Needed to subscribe a user...
   			PL_Router::load_builder_partial('free-trial.php');
   		}

   		public function render_content() {
   			// Do nothing...
   		}
   }

   class PL_Customize_Load_Theme_Opts_Control extends WP_Customize_Control 
   {
   		public $type = 'load_opt_defaults';

   		public function render() {
   		  ?>
   			<h3 id="optionsframework-submit-top" >
				<!-- Build default dropdown... -->
				<div id="default_opts">
				  <span class="customize-title-span">Use Default Theme Options: </span>
				  <select id="def_theme_opts">
				  <?php foreach (PLS_Options_Manager::$def_theme_opts_list as $name) : ?>
				  	<option value="<?php echo $name?>"><?php echo $name; ?></option>
				  <?php endforeach; ?>
				  </select>
				  <input type="button" id="btn_def_opts" class="top-button button-primary" value="Load" style="margin: 0px" />
				</div>
			</h3>
		  <?php
   		}

   		public function render_content() {
   			// Do Nothing...
   		}
   }

}

class PL_Customizer 
{
	static $onboard_sections = array('General', 'User Info');

	static $def_setting_opts = array(
			                          'default'   => '',
			                          'type'      => 'option',
			                          'transport' => 'refresh' 
			                        );

	private function get_setting_opts( $args = array() )
	{
		$merged_opts = wp_parse_args($args, self::$def_setting_opts);
		return $merged_opts;
	}

	private function get_control_opts( $id, $attrs, $sect_id, $is_custom = false )
	{
		$args = array(
                        'settings' => $id,
                        'label'    => $attrs['name'],
                        'section'  => $sect_id
                     );

		if ( !$is_custom ) {
			$args['type'] = $attrs['type'];
		}

		return $args;
	}

	public function register_option_components( $wp_customize, $onboard = false ) 
	{
		// A simple check to ensure function was called properly...
		if ( !isset($wp_customize) ) { return; }

		$theme_opts_id = $wp_customize->get_stylesheet();
	    // error_log('Theme options ID: ' . $theme_opts_id);

	    $section_priority = 3;
	    $last_section_id = '';
	    $include_section = true;

	    foreach (PLS_Style::$styles as $style) 
	    {
	    	if ($onboard && !$include_section) {
	    		continue;
	    	}

	    	// Take care of defining some common vars used by almost every case...
	    	if ( isset($style['id']) ) {
	    		$setting_id = "{$theme_opts_id}[{$style['id']}]";
	    		$control_id = "{$style['id']}_ctrl";
	    	}

	        switch ( $style['type'] ) 
	        {
	            case 'heading':
	                if ($onboard) {
	                	$include_section = array_search($style['name'], self::$onboard_sections);
	                	if (!$include_section) { continue; }
	            	}

	                $args_section = array(
			                                'title'    => __($style['name'],''),
			                                'description' => $style['name'],
			                                'priority' => $section_priority,
			                             ); 

	                $section_id = strtolower(str_replace(' ', '_', $style['name'])) . '_pls_options';
	                $wp_customize->add_section( $section_id, $args_section );

	                // Add dummy control so that section will appear...
	                $wp_customize->add_setting( 'dummy_setting', array() );
	                $wp_customize->add_control( "dummy_ctrl_{$section_id}", array('settings' => 'dummy_setting', 'section' => $section_id, 'type' => 'none') );

	                $last_section_id = $section_id;
	                ++$section_priority;
	                break;

	            // Handle the standard (i.e., 'built-in') controls...
	            case 'text':
	            case 'checkbox':
	            	$wp_customize->add_setting( $setting_id, self::get_setting_opts() );
	                
	                $args_control = self::get_control_opts( $setting_id, $style, $last_section_id );
	                $wp_customize->add_control( $control_id, $args_control);
	                break;

	            case 'textarea':
		            $wp_customize->add_setting( $setting_id, self::get_setting_opts() );

	                $args_control = self::get_control_opts( $setting_id, $style, $last_section_id, true );
	                $wp_customize->add_control( new PL_Customize_TextArea_Control($wp_customize, $control_id, $args_control) );
	                break;

	            case 'typography':
	            	$typo_setting_keys = array('size', 'face', 'style', 'color');
	            	$typo_setting_ids = array();
	            	
	            	foreach ($typo_setting_keys as $key) {
	            		$wp_customize->add_setting( "{$setting_id}[{$key}]", self::get_setting_opts() );
	            		$typo_setting_ids[$key] = "{$setting_id}[{$key}]";
	            	}
	            	// 
	            	$args_control = self::get_control_opts( $typo_setting_ids, $style, $last_section_id, true );
	                $wp_customize->add_control( new PL_Customize_Typography_Control($wp_customize, $control_id, $args_control) );

	            	// if ($style['id'] == 'h1_title') {
	            	// 	$ctrl = new PL_Customize_Typography_Control($wp_customize, $control_id, $args_control);

	            	// 	foreach ( $ctrl->settings as $key => $setting) {
	            	// 		$temp_id = $setting->id;
	            	// 		error_log("{$key} => {$temp_id} \n");
	            	// 	}
	            	// }
	            	break;

	            default:
	                break;
	        } 
	    }

	}

	public function register_pl_components( $wp_customize ) 
	{
		// Dummy setting must be associated with non-options sections in order for them to appear...
	    $dummy_setting_id = 'dummy_setting';
	    $wp_customize->add_setting( 'dummy_setting', array() );

		/* 
		 * MLS Integration Section
		 */
		if ( PL_Option_Helper::api_key() ) {
			$int_section_id = 'integration_pl';
			$int_args_section = array(
	                                'title'    => __('MLS Integration', ''),
	                                'description' => 'MLS Integration',
	                                'priority' => 1,
	                             ); 
	        
	        $wp_customize->add_section( $int_section_id, $int_args_section );

	        // Control
	        $int_ctrl_id = 'integration_ctrl';
	        $int_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $int_section_id, 'type' => 'none');
	        $wp_customize->add_control( new PL_Customize_Integration_Control($wp_customize, $int_ctrl_id, $int_args_ctrl) );
		}

		/* 
		 * Plug-in Settings Section 
		 */
		$set_section_id = 'settings_pl';
		$set_args_section = array(
	                            'title'    => __('Settings', ''),
	                            'description' => 'Settings',
	                            'priority' => 2,
	                         ); 
	    
	    $wp_customize->add_section( $set_section_id, $set_args_section );

	    // Demo Data Control
	    $demo_setting_id = 'pls_demo_data_flag';
	    $wp_customize->add_setting( $demo_setting_id, self::get_setting_opts() );
		
		$demo_ctrl_id = 'demo_data_ctrl';                
	    $demo_args_control = self::get_control_opts( $demo_setting_id, array('name'=>'Use Demo Data for listings', 'type'=>'checkbox'), $set_section_id );
	    $wp_customize->add_control( $demo_ctrl_id, $demo_args_control);

	    // Load Theme Options Control
	    $load_opts_ctrl_id = 'load_opts_ctrl';
	    $load_opts_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $set_section_id, 'type' => 'none');
	    $wp_customize->add_control( new PL_Customize_Load_Theme_Opts_Control($wp_customize, $load_opts_ctrl_id, $load_opts_args_ctrl) );

	}
	    
}
?>