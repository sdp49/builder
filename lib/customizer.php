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
	PL_Customizer::register_components( $wp_customize );
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
				$size = $i . 'px';
			  ?>	
				// Check if null
				if(!isset($typography_stored['size'])) {
					$typography_stored['size'] = '';
				}
				
				<option value="<?php echo esc_attr( $size ); ?>" <?php selected( $typography_stored['size'], $size, false ); ?>><?php echo esc_html( $size ); ?></option>
			  <?php endfor; ?>
			</select>
		
			<!-- Font Face -->
			<select class="of-typography of-typography-face"  >
			
			$faces = of_recognized_font_faces();

			// Check if null
			if(!isset($typography_stored['face'])) {
				$typography_stored['face'] = '';
			}

			foreach ( $faces as $key => $face ) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['face'], $key, false ) . '>' . esc_html( $face ) . '</option>';
			}			
			
			$output .= '</select>';	

			<!-- Font Style -->
			<select class="of-typography of-typography-style"  >

			// Check if null
			if(!isset($typography_stored['style'])) {
				$typography_stored['style'] = '';
			}

			$styles = of_recognized_font_styles();
			foreach ( $styles as $key => $style ) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>'. $style .'</option>';
			}
			$output .= '</select>';

			<!-- Font Color -->
			$output .= '<div id="' . esc_attr( $value['id'] ) . '_color_picker" class="colorSelector"><div style="' . esc_attr( 'background-color:' . $typography_stored['color'] ) . '"></div></div>';
			$output .= '<input class="of-color of-typography of-typography-color" name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" type="text" value="' . esc_attr( $typography_stored['color'] ) . '" />';
		  <?php
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

	public function register_components( $wp_customize ) 
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

}
?>