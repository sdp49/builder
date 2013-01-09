<?php

/*
 * Custom implementations of PL_Admin_Section
 */


/*
 * Custom implementations of PL_Admin_Card
 */


class PL_Admin_Theme_Select extends PL_Admin_Card {
	// Instance Vars

	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function render () {
 		?>
 		  <div class="container-fluid">
          <div class="pls-switcher">
			<div class="row-fluid">
                <div class="span12">
                  <div class="row-fluid">
                    <div class="span7">
                      <label for="pls-theme-select" class="pls-label">Please select a theme to get started:</label>
                      <select id="pls-theme-select" class="w100">
                      	<option>Default</option>
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
            <div class="span4">
              <img class="w100" src="img/img-01.jpg" alt="">
            </div>
            <div class="span8">
              <h2>Theme Description</h2>
              <p>Ut nec sem metus, at placerat sapien. Vivamus erat leo, tincidunt eu ultricies quis, pellentesque at turpis. Orbi tellus nunc, condimentum eget rhoncus venenatis, dignissim non ligula. Sed nec tortor ipsum. Morbi tellus nunc, condimentum eget rhoncus venenatis, dignissim non ligula.</p>
              
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
                  Map Search Integrations
                </li>  
                <li>
                  <div class="featureicon"><a class="ico-responsive" href="#"></a></div>
                  Featured Listings Slider
                </li>       
                <li>
                  <div class="featureicon"><a class="ico-responsive" href="#"></a></div>
                  Responsive Web Design
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
                <a class="active" href="#">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <span>...</span>
                <a href="#">9</a>
                <a href="#">10</a>            
                <a href="#">11</a>            
                <a class="last" href="#">Next</a>            
              </div><!--pls=numbers-->              
            </div>
          </div>
        </div>
 		<?php
 	}
}

class PL_Admin_Theme_Skin extends PL_Admin_Card {
	
	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function render () {
 		// Build list of current theme's available skins...
		$skins = array_merge( array('---' => 'none', 'Default' => 'default'), PL_Css_Helper::get_theme_skins() );
 		
 		?>
 		  <div class="container-fluid <?php echo ('card-' . $this->id); ?>">
 		  	<div class="row-fluid">
              <div class="span6">
                <label for="pls-skin-select" class="pls-label">Select a color palette for your website.</label>
                <select id="pls-skin-select" class="w100">
                  <option>Default</option>
                </select>
              </div>
            
              <div class="span6 ml10">
                <div class="bt-slot">
                  <a href="#" class="button button-light-grey">Return to Theme Selection</a>
                  <a href="#" class="button button-green">Select Skin &amp; Apply Changes</a>              
                </div>
              </div>
            </div>
		  </div>
 		<?php
 	}
}

class PL_Admin_CSS_Editor extends PL_Admin_Card {

	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function render () {
 		?>
 		<?php
 	}
}

class PL_Admin_Menu_Editor extends PL_Admin_Card {

	public function __construct( $id, $args = array() ) {
 		parent::__construct( $id, $args );
 	}

 	public function render () {
 		?>
 		<?php
 	}
}



?>