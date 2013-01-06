<?php
/**
 * Main Placester Admin Template
 *
 * This is the main template for the Placester custom admin panel, spawning and organizing
 * the various containers/sections and their components, along with any hooks to register
 * styles and scripts.
 */
?>

<?php

// Catch variables related to request/template...

// Load styles & scripts
do_action( 'pl_admin_enqueue_scripts' );

?>

<html>

  <head>
  	<title></title>
  	<?php
  		// WP call to load scripts enqueued for the head section...
  		print_head_scripts();

  		// If the current theme's stylesheet is enqueued, remove it... (assuming a certain handle)
  		$theme_style_handle = wp_get_theme()->Template . '-style';
  		error_log($theme_style_handle);
  		wp_dequeue_style($theme_style_handle);

  		global $wp_styles, $wp_scripts;
  		ob_start();
  		  // var_dump($wp_styles);
  		  // var_dump($wp_scripts);
  		error_log(ob_get_clean());

  		// WP call to load styles enqueued for the footer section and/or register too late for the head...
  	  	print_admin_styles();
  	?>

  	<!-- Temporary Solution... -->
  	<script type="text/javascript">
  	  pl_admin_global = {};
  	</script>
  </head>

  <body>
  	<!-- Header Admin Bar -->
	<section id="pls-header">
	  <div id="pls-top">
	    <div id="pls-meta">
		  <h1><a id="pls-logo" href="#"></a></h1>
		  <a class="pls-toggle" href="#">Customizer</a>
		  <ul id="admin-dropdown" class="pls-dropdown">
	        <li><a href="#">Admin<span class="a-down"></span></a>
	          <ul>
	            <li><a href="#">Real Estate Support Academy</a></li>
	            <li><a href="#">Getting Started Guide</a></li>
	            <li><a href="#">Free 15 Day Trial</a></li>
	          </ul>
	        </li>
	      </ul>
	      <div id="pls-switch">
	        <a href="#" class="left active"><span class="ico-agent">Agent</span></a>
	        <a href="#" class="right off"><span class="ico-developer">Developer</span></a>
	      </div><!--pls-switch-->
	    </div><!--pls-meta-->
	    <div id="pls-user">
	      <ul id="user-dropdown" class="pls-dropdown">
	        <li>
	          <img alt="" src="">
	          <a href="#" class="pls-link">Welcome Home Matt, Premium User!<span class="a-down"></span></a>
	          <ul>
	            <li><a href="#">Real Estate Support Academy</a></li>
	            <li><a href="#">Getting Started Guide</a></li>
	            <li><a href="#">Free 15 Day Trial</a></li>
	          </ul>
	        </li>
	      </ul>
	      <a href="#" class="pls-logout">Logout</a>
	    </div><!--pls-user-->
	  </div><!--pls-top-->
	    
	  <div id="pls-header-bot">
	    <div id="pls-inner">
	  	  <div id="pls-inner-top">
	        <div id="pls-search">        
	          <form>
	            <label for="pls-main-search">Go:</label>
	            <input id="pls-main-search" type="text" class="inactive">
	          </form>
	        </div><!--pls-search-->
	        <div id="pls-buttons">
	          <?php // Construct buttons ?>
	          <!-- <a class="button deactive" href="#">Create</a>
	          <a class="button deactive" href="#">Edit</a>
	          <a class="button deactive" href="#">Upgrade</a>
	          <a class="button deactive" href="#">Leads</a>
	          <a class="button deactive" href="#">Help</a>  -->                             
	        </div><!--pls-buttons-->
	      </div><!--pls-inner-top-->
	      <div id="pls-inner-bot">
	    	<?php echo PL_Admin_Util::getBreadcrumbs(); ?>
	      </div><!--pls-inner-bot-->
	    </div><!--pls-inner-->
	  </div><!--pls-header-bot-->
	</section>

	<!-- Side Bar -->
	<section id="pls-aside" class="pls-undocked">
	  <?php
	  	$navList = array('utilities', 'settings'); 
	  	// PL_Admin_Util::renderNavs($navList); 
	  ?>
	</section>

	<!-- Side Bar Pane Container -->
	<section id="pls-pane" class="pls-small pls-min">
      <div id="pls-inner">
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span12">
              <div class="pls-head">
                <h1>1/5: Site Title &amp; Slogan</h1>
              
                <!--TOOLTIP START-->
                  <div class="pls-tooltip">
                    <p>Social Integration</p>
                  </div>
                <!--TOOLTIP END-->
  
                <div class="pls-right">
                  <a href="#" class="bullet on"></a>
                  <a href="#" class="bullet off"></a>
                  <a href="#" class="bullet off"></a>
                  <a href="#" class="bullet off"></a>
                  <a href="#" class="bullet off"></a>                                 
                </div><!--pls-right-->      
              </div><!--pls-head-->        	
            </div><!--span12-->
          </div><!--row-fluid-->
        
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
          </div><!--row-fluid-->
        </div><!--container-fluid-->
      </div>
	</section>

	<!-- Site Content Container -->
	<iframe id="main-iframe" src="<?php echo PL_Admin_Util::getContentURI(); ?>"></iframe>

  </body>

 </html> 