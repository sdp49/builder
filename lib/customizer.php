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
	define_custom_controls();
	PL_Customizer::register_option_components( $wp_customize );
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
   			<!-- Font Size -->
			<select class="of-typography of-typography-size"  >
			
			  <?php for ($i = 9; $i < 71; $i++): 
				$size = $i . 'px'; ?>
				<option value="<?php echo esc_attr( $size ); ?>" <?php selected( $typography_stored['size'], $size, false ); ?>><?php echo esc_html( $size ); ?></option>
			  <?php endfor; ?>
			</select>
		
			<!-- Font Face -->
			<select class="of-typography of-typography-face"  >

			<?php $faces = of_recognized_font_faces(); ?>

			  <?php foreach ( $faces as $key => $face ): ?>
			 	<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['face'], $key, false ) . '>' . esc_html( $face ) . '</option>
			  <?php endforeach; ?>		
			</select>

			<!-- Font Style -->
			<select class="of-typography of-typography-style"  >

			<?php $styles = of_recognized_font_styles(); ?>

			  <?php foreach ( $styles as $key => $style ): ?>
				<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>'. $style .'</option>
			  <?php endforeach; ?>
			</select>

			<!-- Font Color -->
			<div id="' . esc_attr( $value['id'] ) . '_color_picker" class="colorSelector">
			  <div style="' . esc_attr( 'background-color:' . $typography_stored['color'] ) . '"></div>
			</div>
			<input type="text" class="of-color of-typography of-typography-color" id="<?php echo esc_attr( $value['id'] . '_color' ); ?>" value="<?php echo esc_attr( $typography_stored['color'] ); ?>" />
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

}

class PL_Customizer 
{
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

	private function get_control_opts( $id, $style, $sect_id, $is_custom = false )
	{
		$args = array(
                        'settings' => $id,
                        'label'    => $style['name'],
                        'section'  => $sect_id
                     );

		if ( !$is_custom ) {
			$args['type'] = $style['type'];
		}

		return $args;
	}

	public function register_option_components( $wp_customize ) 
	{
		// A simple check to ensure function was called properly...
		if ( !isset($wp_customize) ) { return; }

		$theme_opts_id = $wp_customize->get_stylesheet();
	    // error_log('Theme options ID: ' . $theme_opts_id);

	    $section_priority = 3;
	    $last_section_id = '';

	    foreach (PLS_Style::$styles as $style) 
	    {
	    	// Take care of defining some common vars used by almost every case...
	    	if ( isset($style['id']) ) {
	    		$setting_id = "{$theme_opts_id}[{$style['id']}]";
	    		$control_id = "{$style['id']}_ctrl";
	    	}

	        switch ( $style['type'] ) 
	        {
	            case 'heading':
	                $args_section = array(
			                                'title'    => __($style['name'],''),
			                                'description' => $style['name'],
			                                'priority' => $section_priority,
			                             ); 

	                $section_id = strtolower(str_replace(' ', '_', $style['name'])) . '_pls_options';
	                $wp_customize->add_section( $section_id, $args_section );

	                $last_section_id = $section_id;
	                ++$section_priority;

	                // Add dummy control so that section will appear...
	                $wp_customize->add_setting( 'dummy_setting', array() );
	                $wp_customize->add_control( "dummy_ctrl_{$section_id}", array('settings' => 'dummy_setting', 'section' => $section_id, 'type' => 'none') );
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

	            // case 'typography':


	            default:
	                break;
	        } 
	    }

	}

	public function register_pl_components( $wp_customize ) 
	{
		if ( PL_Option_Helper::api_key() ) {

			/* Integration form... */
			$int_section_id = 'integration_pl';
			$int_ctrl_id = 'integration_ctrl';

			// Section
			$args_section = array(
	                                'title'    => __('MLS Integration', ''),
	                                'description' => 'MLS Integration',
	                                'priority' => 1,
	                             ); 
	        
	        $wp_customize->add_section( $int_section_id, $args_section );

	        // Need to add a dummy section so that 
	        $wp_customize->add_setting( 'dummy_setting', array() );

	        // Control
	        $wp_customize->add_control( new PL_Customize_Integration_Control($wp_customize, $int_ctrl_id, array('settings' => 'dummy_setting', 'section' => $int_section_id, 'type' => 'none')) );
		}
	}

}
?>