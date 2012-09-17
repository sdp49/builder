<?php $mls_list = PL_Integration_Helper::mls_list(); ?>

<form id="pls_integration_form">

  	<div class="row">
	  <div class="info">
		<h3>MLS Name</h3>
		<p>Pick which MLS provides your RETS data.</p>
	  </div>
	  <div class="elements">
	  	<p>
		  <strong>Email us at <a href="mailto:support@placester.com">support@placester.com</a> if you don't see your MLS listed.</strong>
		</p>

		<select id="mls_id" name="mls_id">
		  <option value=""> --- </option>
		  <?php foreach ($mls_list as $mls_group => $mls_arr): ?>
		    <optgroup label="<?php echo $mls_group; ?>">
		      <?php foreach ($mls_arr as $mls_pair): ?>
		      	<option value="<?php echo $mls_pair[1]; ?>"><?php echo $mls_pair[0]; ?></option>
		      <?php endforeach; ?>
		    </optgroup>
		  <?php endforeach; ?>
		</select>
	  </div>	
	</div>
  
	<div class="row">
	  <div class="info">
	    <h3>Agent ID</h3>
	    <p>Your Agent ID on the RETS server.</p>
	  </div>
	  <div class="elements">
	    <input id="feed_agent_id" name="feed_agent_id" size="30" type="text" />
	  </div>
	</div>

	<div class="row">
	  <div class="info">
	    <h3 class="invalid">Phone Number</h3>
		<p>Your phone number will help us provide prompt support to get your integration setup.</p>
	  </div>
	  <div class="elements">
		<input id="phone" name="phone" type="text" class="invalid">
	  </div>
    </div>

    <div class="row">
      <input type="submit" class="button-primary" />
    </div>
  
 </form>