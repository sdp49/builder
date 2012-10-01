/*
 * Global Definitions
 */

// Usually defined by WordPress, but not in the customizer...
var ajaxurl = 'http://onboard.placester.local/wp-admin/admin-ajax.php';

jQuery(document).ready(function($) {

	/*
	 * Add custom javascript here that applies/affects the customizer as a whole. 
	 * (Configured to execute on any load of customize.php)
	 */

	 $('#btn_def_opts').live('click', function(event) {
		event.preventDefault();

		if (!confirm('Are you sure you want to overwrite your existing Theme Options?'))
		{ return; }

		var data = { action: 'import_default_options',
					 name: $('#def_theme_opts option:selected').val() }
		// console.log(data);
		
		$.post(ajaxurl, data, function(response) {
		  if (response) {
		  	// console.log(response);
		  }
		  
	      // Refresh theme options to reflect newly imported settings...
	      window.location.reload(true);  
		});
	});
	

	$('#customize-controls').live('change', function (event) {
		console.log(this);
	}); 
});