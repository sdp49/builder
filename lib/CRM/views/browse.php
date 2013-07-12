<?php
// Ensure the var containing info about the active CRM is a valid array...
if (!is_array($crm_info)) { return; }

// Make CRM vars more accessible...
extract($crm_info);

// Get an instance of the CRM's class library...
$crm_obj = new $class();

// Retrieve this CRM's contact fields' labels for rendering the grid to display them...
$field_labels = $crm_obj->contactFieldLabels();

// HTML element ID of the grid's table element...
$table_id = "contacts_grid";

// PL_Form::generate_form(PL_Config::PL_API_LISTINGS('get', 'args'),array('method' => "POST", 'title' => true, 'include_submit' => false, 'id' => 'pls_admin_my_listings'));

?>

<div class="crm-browse-box">
	<div class="header-wrapper">
		<h2>Your site's CRM is integrated with <?php echo $display_name; ?></h2>
		<a href="#" class="deactivate-button button-secondary">Choose a different CRM</a>
	</div>

	<!-- <div class="browse-logo">
		<img src="<?php echo $logo_img; ?>" />
	</div> -->

	<div class="">
		<h3>Render Search Form here...</h3>
	</div>

	<div class="grid-container" style="width: 99%">
	  	<table id="<?php echo $table_id; ?>" class="widefat post" cellspacing="0">
	   		<thead>
	      		<tr>
	      			<?php foreach ($field_labels as $label): ?>
	        			<th><span><?php echo $label; ?></span></th>
					<?php endforeach; ?>	        			
	      		</tr>
	    	</thead>
	    	<tbody></tbody>
		    <tfoot>
		      	<tr>
		      		<?php for ($i = 0; $i < count($field_labels); $i++): ?>
			        	<th></th>
					<?php endfor; ?>			        	
		      	</tr>
		    </tfoot>
	  	</table>
	</div>
</div>