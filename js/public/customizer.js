/*
 * Global Definitions
 */

// Usually defined by WordPress, but not in the customizer...
var ajaxurl = 'http://onboard.placester.local/wp-admin/admin-ajax.php';

// This global variable must be defined in order to conditionally prevent iframes from being
// automatically "busted" when in the hosted environment... (see hosted-modifications plugin)
var customizer_global = {
	previewLoaded: function () {
		alert('Preview finished loading...');
	}
};


jQuery(document).ready(function($) {

	/*
	 * Add custom javascript here that applies/affects the customizer as a whole. 
	 * (Configured to execute on any load of customize.php)
	 */

	 /*
	  * Applies to loading default theme options "pallets"
	  */
	 $('#btn_def_opts').live('click', function (event) {
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

	/*
	 * Handles switching themes in the preview iframe...
	 */
	$('#switch_theme_main #theme_choices').live('change', function (event) {
		// console.log($(this).val());
		window.location.href = $(this).val();
	});

	// Ensures that saving a new theme in the customizer does NOT cause a redirect...
	if (_wpCustomizeSettings) {
		var boolSuccess = delete _wpCustomizeSettings.url.activated;
		// console.log('redirect deleted: ' + boolSuccess);
	}

	/*
	 * Catch-all for input changes...
	 */
	$('input').on('keyup', function (event) { 
		// alert('input changed');
	});

});	


