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
      
      <div class="row-fluid">
      	<div class="span12">
          <div id="pls-numbers">
            <a class="first" href="#">Previous</a>          
            <!-- <a class="active" href="#">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
            <span>...</span>
            <a href="#">9</a>
            <a href="#">10</a>            
            <a href="#">11</a> -->           
            <a class="last" href="#">Next</a>            
          </div><!--pls-numbers-->              
        </div>
      </div>
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

class PL_Admin_Card_Basic_Info extends PL_Admin_Card {

  public function __construct( $id, $args = array() ) {
    parent::__construct( $id, $args );
  }

  public function render () {
    ?>
      <div class="row-fluid">
        <div class="span12">
          <div id="pls-cards">
            <div class="pls-card pls-c01 pls-active">
              <div class="inp-slot">
                <label for="pls-site-title" class="pls-label">Enter Site Title</label>
                <input id="pls-site-title" type="text" class="w175">
              </div><!--inp-slot-->
              <div class="inp-slot">
                <label for="pls-site-slogan" class="pls-label">Enter a Slogan</label>
                <input id="pls-site-slogan" type="text" class="w175">
              </div><!--inp-slot-->  
              <div class="inp-slot">
                <label for="pls-email-address" class="pls-label">Enter an Email Address</label>
                <input id="pls-email-address" type="text" class="w175">
              </div><!--inp-slot-->
              <div class="inp-slot">
                <label for="pls-phone-number" class="pls-label">Enter a Phone Number</label>
                <input id="pls-phone-number" type="text" class="w175">
              </div><!--inp-slot-->
              <div class="bt-slot">
                <a href="#" class="button button-green">Next: Color &amp; Palette &amp; Styling</a>
                <a href="#" class="button button-blue">Skip</a>             
              </div><!--bt-slot-->
            </div><!--c01-->
            <div class="pls-card pls-c02"></div>
            <div class="pls-card pls-c03"></div>
            <div class="pls-card pls-c04"></div>
            <div class="pls-card pls-c05"></div>
          </div>  
        </div><!--span12-->      
      </div>
    <?php
  }
}

class PL_Admin_Card_MLS extends PL_Admin_Card {

  public function __construct( $id, $args = array() ) {
    parent::__construct( $id, $args );
  }

  public function render () {
    ?>
      <div class="row-fluid">
        <div class="span12">
          <div id="pls-cards">
            <div class="pls-card pls-c01"></div>
            <div class="pls-card pls-c02 pls-z51"></div>
            <div class="pls-card pls-c03 pls-active">
              <div class="inp-slot">
                <label for="pls-select-state" class="pls-label">Select State</label>
                <select id="pls-select-state" class="w85">
                  <option>NY</option>
                </select>
              </div><!--inp-slot-->
              <div class="inp-slot">
                <label for="pls-mls-provider" class="pls-label">Select MLS Provider</label>
                <select id="pls-mls-provider" class="w175">
                  <option>MLS Provider</option>
                </select>
              </div><!--inp-slot-->
              <div class="inp-slot">
                <label for="pls-office-name" class="pls-label">Office Name</label>
                <input id="pls-office-name" type="text" class="w175">
              </div><!--inp-slot-->    
              <div class="inp-slot">
                <label for="pls-agent-id" class="pls-label">Agent ID</label>
                <input id="pls-agent-id" type="text" class="w85">
              </div><!--inp-slot-->                                    
              <div class="bt-slot">
                <a href="#" class="button button-light-grey">Prev: Color Palette &amp; Styling</a>              
                <a href="#" class="button button-green">Next: Social Integration</a>
                <a href="#" class="button button-blue">Skip</a>             
              </div><!--bt-slot-->            
            </div>            
            <div class="pls-card pls-c04"></div>
            <div class="pls-card pls-c05"></div>
          </div>          
        </div><!--span12-->
      </div>
    <?php
  }
}

class PL_Admin_Card_Social extends PL_Admin_Card {

  public function __construct( $id, $args = array() ) {
    parent::__construct( $id, $args );
  }

  public function render () {
    ?>
      <div class="row-fluid">
        <div class="span12">
          <div id="pls-cards">
            <div class="pls-card pls-c01"></div>
            <div class="pls-card pls-c02 pls-z51"></div>
            <div class="pls-card pls-c03 pls-z52"></div>
            <div class="pls-card pls-c04 pls-active">
              <div class="inp-slot">
                <label class="pls-label">Login to your favorite social networks.</label>
                <div class="pls-social">
                  <a href="#" id="fb"></a>
                  <a href="#" id="go"></a>
                  <a href="#" id="tw"></a>
                  <a href="#" id="yh"></a>
                </div><!--social-->
              </div><!--inp-slot-->
              <div class="bt-slot">
                <a href="#" class="button button-light-grey">Prev: MLS Integration</a>              
                <a href="#" class="button button-green">Next: Demo Data</a>
                <a href="#" class="button button-blue">Skip</a>             
              </div><!--bt-slot-->            
            </div>
            <div class="pls-card pls-c05"></div>
          </div>
        </div><!--span12-->
      </div>
    <?php
  }
}

class PL_Admin_Card_Demo_Data extends PL_Admin_Card {

  public function __construct( $id, $args = array() ) {
    parent::__construct( $id, $args );
  }

  public function render () {
    ?>
      <div class="row-fluid">
        <div class="span12">
          <div id="pls-cards">
            <div class="pls-card pls-c01 row-fluid"></div>
            <div class="pls-card pls-c02 row-fluid pls-z51"></div>
            <div class="pls-card pls-c03 row-fluid pls-z52"></div>
            <div class="pls-card pls-c04 row-fluid pls-z53"></div>
            <div class="pls-card pls-c05 row-fluid pls-active">
              <div class="inp-slot pls-rocker">
                <label class="pls-label">Would you like demo data?</label>
                <a class="left off" href="#">Turn Off</a>
                <a class="right active" href="#">Turn On</a>
              </div><!--pls-rocker-->
              <div class="bt-slot">
                <a href="#" class="button button-light-grey">Prev: Social Integration</a>              
                <a href="#" class="button button-green">All Set. Start Editing</a>          
              </div><!--bt-slot-->  
            </div>
          </div>            
        </div><!--span12-->
      </div>
    <?php
  }
}

?>