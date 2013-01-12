<?php

/*
 * Custom implementations of PL_Admin_Card
 */


class PL_Admin_Card_Theme_Select extends PL_Admin_Card {
	// Instance Vars

	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function render_content () {
 		// Get list of supported themes...
    global $PL_ADMIN_THEMES;
    
    ?>
      <div class="pls-switcher">
		    <div class="row-fluid">
          <div class="span12">
            <div class="row-fluid">
              <div class="span7">
                <label for="pls-theme-select" class="pls-label">Please select a theme to get started:</label>
                <select id="pls-theme-select" class="w100">
                  <?php foreach ($PL_ADMIN_THEMES as $group => $themes): ?>
                    <optgroup label="<?php echo $group; ?>">
                      <?php foreach ($themes as $name => $template): ?>
                        <option value="<?php echo $stylesheet ?>" <?php selected( wp_get_theme()->Template, $template ); ?>><?php echo $name; ?></option>
                      <?php endforeach; ?>
                    </optgroup>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="span5">
              	<div class="bt-slot">
						  		<a href="#" class="button button-green">Select Theme &amp; Choose a Skin</a>                    
              	</div>
              </div>
            </div>
          </div>
        </div>
		  </div>

		  <div class="row-fluid">
        <?php $themeObj = wp_get_theme(); ?>
        <div class="span4">
          <img class="w100" src="<?php echo esc_url( $themeObj->get_screenshot() ); ?>" alt="Theme Image">
        </div>
        <div class="span8">
          <h2>Theme Description</h2>
          <p><?php echo $themeObj->display('Description'); ?></p>
          
          <h2>Features</h2>
          <ul id="featureslist">
            <li>
              <div class="featureicon"><a class="ico-responsive" href="#"></a></div>
              Responsive Web Design
            </li>
            <li>
              <div class="featureicon"><a class="ico-responsive" href="#"></a></div>
              Map Search Integrations
            </li>  
            <li>
              <div class="featureicon"><a class="ico-responsive" href="#"></a></div>
              Featured Listings Slider
            </li>                   
          </ul>
        </div>
      </div>
      
      <!-- <div class="row-fluid">
      	<div class="span12">
          <div id="pls-numbers">
            <a class="first" href="#">Previous</a>          
            <a class="active" href="#">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
            <span>...</span>
            <a href="#">9</a>
            <a href="#">10</a>            
            <a href="#">11</a>            
            <a class="last" href="#">Next</a>            
          </div><!-pls-numbers->              
        </div>
      </div> -->
 		<?php
 	}
}

class PL_Admin_Card_Theme_Skin extends PL_Admin_Card {
	
	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function render_content () {
 		// Build list of current theme's available skins...
		$skins = array_merge( array('---' => 'none', 'Default' => 'default'), PL_Theme_Helper::get_theme_skins() );
 		
 		?>
    	<div class="row-fluid">
        <div class="span6">
          <label for="pls-skin-select" class="pls-label">Select a color palette for your website.</label>
          <select id="pls-skin-select" class="w100">
            <?php foreach ( $skins as $name => $skinID ): ?>
              <option value="<?php echo esc_attr( $skinID ); ?>"><?php echo esc_html( $name ); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="span6 ml10">
          <div class="bt-slot">
            <a href="#" class="button button-light-grey">Return to Theme Selection</a>
            <a href="#" class="button button-green">Select Skin &amp; Apply Changes</a>              
          </div>
        </div>
      </div>
 		<?php
 	}
}

class PL_Admin_Card_CSS_Editor extends PL_Admin_Card {

	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function render () {
 		?>

 		<?php
 	}
}

class PL_Admin_Card_Menu_Editor extends PL_Admin_Card {

	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function render () {
 		?>
 		<?php
 	}
}

/* Cards used in the "Onboarding" Flow */

class PL_Admin_Card_Info extends PL_Admin_Card {

  public function __construct( $id, $args = array() ) {
    parent::__construct( $id, $args );
  }

  public function render () {
    ?>
    <?php
  }
}

class PL_Admin_Card_MLS extends PL_Admin_Card {

  public function __construct( $id, $args = array() ) {
    parent::__construct( $id, $args );
  }

  public function render () {
    ?>
    <?php
  }
}

class PL_Admin_Card_Social extends PL_Admin_Card {

  public function __construct( $id, $args = array() ) {
    parent::__construct( $id, $args );
  }

  public function render () {
    ?>
    <?php
  }
}

class PL_Admin_Card_Demo extends PL_Admin_Card {

  public function __construct( $id, $args = array() ) {
    parent::__construct( $id, $args );
  }

  public function render () {
    ?>
    <?php
  }
}

?>