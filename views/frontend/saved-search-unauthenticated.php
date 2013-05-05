 <div style="display:none;">

 	<div id="pl_saved_search_register_form">
 		
          <form method="post" action="#<?php echo $role; ?>" name="pl_saved_search_register_form" class="lead-capture-wrapper" autocomplete="off">

	          <div style="display:none" class="success">You have been successfully signed up. This page will refresh momentarily.</div>

	          <div id="pl_saved_search_register_form_inner_wrapper">

		          	<p class="reg_form_pass">
		              <label for="user_password">Name of the Search</label>
		              <input type="text" tabindex="26" size="20" required="required" class="input" id="user_search_name" name="user_search_name" data-message="Name your search" placeholder="Name your Search">
		            </p>

		            <p class="reg_form_pass">
		              <label for="user_password">Your Email Address</label>
		              <input type="text" tabindex="26" size="20" required="required" class="input" id="user_search_email" name="user_search_name" data-message="Email Address" placeholder="Email Address">
		            </p>

		            <p class="reg_form_submit">
		              <input type="submit" tabindex="28" class="submit button" value="Register" id="pl_submit" name="pl_register">
		            </p>

		            <div id="saved_search_value_wrapper">
		            	<h3>Your Saved Search</h3>
		            	<div id="saved_search_values">
		            		<ul>
		            		</ul>
		            	</div>
		            </div>
		            

	          </div>		
	      </form>
      </div>
</div>

 <?php

 /*

				<?php pls_do_atomic( 'register_form_before_title' ); ?>
	            
	            <h2>Sign Up</h2>

	            <?php pls_do_atomic( 'register_form_before_email' ); ?>
	            
	            <p class="reg_form_email">
	              <label for="user_email">Email</label>
	              <input type="text" tabindex="25" size="20" required="required" class="input" id="reg_user_email" name="user_email" data-message="A valid email is needed." placeholder="Email">
	            </p>
	            
	            <?php pls_do_atomic( 'register_form_before_password' ); ?>
	            
	            <p class="reg_form_pass">
	              <label for="user_password">Password</label>
	              <input type="password" tabindex="26" size="20" required="required" class="input" id="reg_user_password" name="user_password" data-message="Please enter a password." placeholder="Password">
	            </p>
	            
	            <?php pls_do_atomic( 'register_form_before_confirm_password' ); ?>
	            
	            <p class="reg_form_confirm_pass">
	              <label for="user_confirm">Confirm Password</label>
	              <input type="password" tabindex="27" size="20" required="required" class="input" id="reg_user_confirm" name="user_confirm" data-message="Please confirm your password." placeholder="Confirm Password">
	            </p>
	            
	            <?php pls_do_atomic( 'register_form_before_submit' ); ?>
	            
	            <p class="reg_form_submit">
	              <input type="submit" tabindex="28" class="submit button" value="Register" id="pl_register" name="pl_register">
	            </p>
	            <?php echo wp_nonce_field( 'placester_true_registration', 'register_nonce_field' ); ?>
	            <input type="hidden" tabindex="29" id="register_form_submit_button" name="_wp_http_referer" value="/listings/">
	            
	            <?php pls_do_atomic( 'register_form_after_submit' ); ?>

 */