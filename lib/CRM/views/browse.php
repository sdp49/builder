<?php
// Ensure the var containing info about the active CRM is a valid array...
if (!is_array($crm_info)) { return; }

// Make CRM vars more accessible...
extract($crm_info);

// Get an instance of the CRM's class library...
$crm_obj = new $class();

// Retrieve this CRM's contact fields' labels for rendering the grid to display them...
$field_labels = array_values($crm_obj->getContactFieldsMeta());

// HTML element ID of the grid's table element...
$table_id = "contacts_grid";
?>

<div class="crm-browse-box">
	<div class="">
		<a href="#" class="deactivate-button">Choose a different CRM</a>
	</div>

	<div class="browse-logo">
		<img src="<?php echo $logo_img; ?>" />
	</div>

	<div>
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