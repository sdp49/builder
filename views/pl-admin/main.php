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

// Load styles & scripts
do_action( 'pl_admin_enqueue_scripts',  );



?>

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
          <img alt="" src="img/user.png">
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
    	<ul id="pls-breadcrumbs" class="disabled">
      	  <li><a href="#">LiveEastie.com<span class="a-down"></span></a></li>
          <li>/</li>           
       	  <li><a href="#">Home</a></li>
          <li>/</li>            
       	  <li><a href="#">Listings</a></li>
          <li>/</li>            
       	  <li><a href="#">01 Prescott Street</a></li>                                    
        </ul>
      </div><!--pls-inner-bot-->
    </div><!--pls-inner-->
  </div><!--pls-header-bot-->
</section>