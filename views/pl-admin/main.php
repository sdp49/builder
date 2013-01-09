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

// Clear out any enqueued scripts and styles before adding those for PL Admin...
global $wp_styles, $wp_scripts;
$wp_styles->queue = array();
$wp_scripts->queue = array();

// ob_start();
//   var_dump($wp_styles->queue);
//   var_dump($wp_scripts->queue);
// error_log(ob_get_clean());

// Load styles & scripts
do_action( 'pl_admin_enqueue_scripts' );

// Get current user info...
$current_user = wp_get_current_user();
// ob_start();
//   var_dump($current_user);
// error_log(ob_get_clean());
?>

<!DOCTYPE html>

<html>

  <head>
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  	<?php
  		// WP call to load scripts enqueued for the head section...
  		print_head_scripts();

  		// WP call to load styles enqueued for the head section...
	  	wp_print_styles();
  	?>
    <title>Placester Admin Panel</title>
  </head>

  <body>
  	<!-- Header Admin Bar -->
	<section id="pls-header">
	  <div id="pls-top">
	    <div id="pls-meta">
		  <h1><a id="pls-logo" href="https://placester.com"></a></h1>
		  <a class="pls-toggle" href="#">Customizer</a>
		  <ul id="admin-dropdown" class="pls-dropdown">
	        <li><a href="<?php echo esc_url( PL_Admin_Util::getAdminURI() ); ?>">Admin<span class="a-down"></span></a>
	          <?php echo PL_Admin_Util::getAnchorList('admin'); ?>
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
	          <a href="#" class="pls-link">Welcome Home <?php echo esc_html( $current_user->user_firstname ); ?>, Premium User!<span class="a-down"></span></a>
	            <?php echo PL_Admin_Util::getAnchorList('user'); ?>
	        </li>
	      </ul>
	      <a href="<?php echo esc_url( wp_logout_url( site_url() ) ); ?>" class="pls-logout">Logout</a>
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
	          <?php echo PL_Admin_Util::constructButtons(); ?>                            
	        </div><!--pls-buttons-->
	      </div><!--pls-inner-top-->
	      <div id="pls-inner-bot">
	    	  <?php echo PL_Admin_Util::getBreadcrumbs(); ?>
          <a id="refresh-content" href="#" class="button button-light-grey pls-refresh-ico"><span></span></a>
          <div class="loader">
            <img src="wp-content/plugins/placester/images/ajax-loader.gif" alt="">
          </div>
	      </div><!--pls-inner-bot-->
	    </div><!--pls-inner-->
	  </div><!--pls-header-bot-->
	</section>

	<!-- Side Bar -->
	<section id="pls-aside" class="pls-undocked">
    <a href="#" class="h-handle"><span></span></a>
    <?php
	  	$navList = array('utilities', 'settings'); 
	  	echo PL_Admin_Util::renderNavs($navList); 
	  ?>
	</section>

	<!-- Side Bar Pane Container -->
  <section id="pls-pane" class="pls-small pls-min">
    <?php // PL_Admin_Util::renderPane(); ?>
  </section>

	<!-- Site Content Container -->
	<iframe id="main-iframe" src="<?php echo PL_Admin_Util::getContentURI(); ?>"></iframe>

  </body>

 </html> 