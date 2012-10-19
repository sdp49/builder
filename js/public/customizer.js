/*
 * Global Definitions
 */

// Usually defined by WordPress, but not in the customizer...
var ajaxurl = 'http://onboard.placester.local/wp-admin/admin-ajax.php';

// This global variable must be defined in order to conditionally prevent iframes from being
// automatically "busted" when in the hosted environment... (see hosted-modifications plugin)
var customizer_global = {
	refreshing: false,
	previewLoaded: function () {
		// alert('Preview finished loading...');
		// jQuery('#customize-preview').removeClass('preview-load-indicator');
		jQuery('#customize-preview').fadeTo(1000, 1);
		jQuery('#preview_load_spinner').fadeTo(700, 0);

		// Set to let other components know that refresh has been completed...
		this.refreshing = false;
	}
};

// The main form/sidebar is initially hidden so that the mangled-mess that exists before
// the DOM manipulation is completed is NOT shown to the user...
window.onload = function () {
	jQuery('#customize-controls').css('display', 'block');

	// If there's a theme arg in the query string, user just switched themes so make
	// sure to have the theme selection pane appear upon page load...
	if ( window.location.href.indexOf('theme=') != -1 ) {
		jQuery('li#theme').trigger('click');
	}  
}

jQuery(document).ready(function($) {

 /*
  * Add custom javascript here that applies/affects the customizer as a whole. 
  * (Configured to execute on any load of customize.php)
  */

	// Hide the "You are Previewing" div + header & footer--no hook to prevent these from
	// rendering, so this is the only way to hide w/out altering core...
	$('#customize-info').remove();
	$('#customize-header-actions').remove();
	$('#customize-footer-actions').remove();

	$('div.wp-full-overlay').attr('id', 'full-overlay');
	$('div.wp-full-overlay-sidebar-content').removeClass('wp-full-overlay-sidebar-content').attr('id', 'sidebar');
	$('#customize-theme-controls').first().attr('id', 'menu-nav');
	$('#menu-nav > ul').first().attr('id', 'navlist');

	// $('<section id="pane"></section>').appendTo('#menu-nav');
	$('#menu-nav').after('<section id="pane"></section>');

	var controlDivs = $('.control-container').detach();
	controlDivs.appendTo('#pane');

	$('#customize-controls').append('<input type="submit" name="save" id="save" style="display:none">');

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
  * Trigger preview re-load + display loading overlay for input changes...
  */

	function setPreviewLoading() {
		if ( !customizer_global.refreshing ) {
		  	$('#customize-preview').fadeTo(800, 0.3);
			$('#preview_load_spinner').fadeTo(700, 1);

			customizer_global.refreshing = true;
		}  
	}

	refPrev = function refreshPreview() {
		var ctrl = $('#customize-control-pls-google-analytics_ctrl input[type=text]');
		var newVal = ctrl.val() ? '' : '3';
		
		// We need to change the control value AND trigger the keyup even in succession...
		ctrl.val(newVal);
		ctrl.trigger('keyup');
		
		setPreviewLoading();
	}

	$('#customize-control-pls-google-analytics_ctrl input[type=text]').on('keyup', function (event) {
		setPreviewLoading();
	});

	$('select.of-typography, #theme_choices').on('change', function (event) {
		setPreviewLoading();
	});


 /*
  * Handles switching themes in the preview iframe...
  */

	$('#theme_choices').live('change', function (event) {
		// console.log($(this).val());
		var curr_href = window.location.href;
		var new_href = $(this).val()

		// Check to see if the current URL contains a flag for onboarding--if so, replicate it in the new href...
		if ( curr_href.indexOf('onboard=true') != -1 ) {
			new_href += '&onboard=true';
		}  

		window.location.href = new_href;
	});

	// Logic to determine whether to hide or show pagination buttons based on change...
	function paginationHideShow(oldIdx, newIdx, maxIdx) {
		var prev = $('#pagination a.prev');
		var next = $('#pagination a.next');
		
		// Handle previous...
		if ( oldIdx == 0) { prev.css('visibility', 'visible'); } 
		else if ( newIdx == 0 ) { prev.css('visibility', 'hidden'); }
		else { /* No action necessary...*/ }

		// Handle next...
		if ( oldIdx == maxIdx ) { next.css('visibility', 'visible'); }
		else if ( newIdx == maxIdx ) { next.css('visibility', 'hidden'); }
		else { /* No action necessary...*/ }				
	}

	// On initial page load, hide/show the pagination buttons accordingly...
	var newInd = $('#theme_choices').get(0).selectedIndex; // Current index is "new" index when initially setting this...
	var maxInd = ( $('#theme_choices').get(0).options.length - 1 );
	paginationHideShow( -1, newInd, maxInd ); // "old" index is set to -1 so it's value won't cause any changes...

	// Handles "Previous" and "Next" pagination buttons...
	$('#pagination a').on('click', function (event) {
		var type = $(this).attr('class');
		var selectElem = $('#theme_choices').get(0);
		var maxIndex = (selectElem.options.length - 1);
		var currIndex = selectElem.selectedIndex;
		var newIndex;

		// Handle each type accordingly
		if ( type === 'prev' ) {
			newIndex = (currIndex - 1);
		}
		else if ( type == 'next' ) {
			newIndex = (currIndex + 1);
		}
		else {
			console.log('Pagination button of type "' + type + '"not handled');
			return;
		}

		// Validate new index
		if ( newIndex < 0 || newIndex > maxIndex ) { 
			console.log('Index out of bounds...reverting'); 
			return;
		}

		// Call logic to hide and/or show pagination buttons based on old & new index...
		paginationHideShow(currIndex, newIndex, maxIndex);

		// Set selected theme to new index... 
		selectElem.selectedIndex = newIndex;
		$('#theme_choices').trigger('change');
	});

	// Ensures that saving a new theme in the customizer does NOT cause a redirect...
	if (_wpCustomizeSettings) {
		var boolSuccess = delete _wpCustomizeSettings.url.activated;
		// console.log('redirect deleted: ' + boolSuccess);
	}


 /*
  * Bind onboarding menu actions...
  */

	$('#navlist .no-pane').on('click', function (event) {
		$('#pane').css('display', 'none');
		$('.control-container').css('display', 'none');

		// Remove active class from any existing elements...
		var activeLi = $('#navlist li.active');
		if ( activeLi.length > 0 ) {
			activeLi.each( function() { $(this).toggleClass('active'); } );
		}
	});

	$('#navlist li:not(.no-pane)').on('click', function (event) {
		// If activated menu section is clicked, do nothing...
		if ( $(this).hasClass('active') ) { return; }

		// Remove active class from any existing elements...
		var activeLi = $('#navlist li.active');
		if ( activeLi.length > 0 ) {
			activeLi.each( function() { $(this).toggleClass('active'); } );
		}

		// Set the current menu item to 'active'
		$(this).toggleClass('active');

		// Make sure pane is visible, then hide any visible control-container(s)...
		$('#pane').css('display', 'block');
		$('.control-container').css('display', 'none');
		
		// Construct the associated control-container's id and show it...
		var containerId = '#' + $(this).attr('id') + '_content';
		$(containerId).css('display', 'block');
	});

	$('#confirm').on('click', function (event) {
		event.preventDefault;
		$('#save').trigger('click');
		// console.log('Finished saving...');
		setTimeout( function () { window.location.href = window.location.origin; }, 1200 ); 
	});


 /*
  * Handle creating listings + making blog posts...
  */	

	function toggleInvalid (item, invalid) {
        if (invalid) {
		  	item.addClass('invalid');
		  	item.prev().addClass('invalid');
		}
		else {
			item.removeClass('invalid');
			item.prev().removeClass('invalid');
		}
	}

  	$('#submit_blogpost').on('click', function (event) {
  		var title = $('#blogpost_title');
  		var content = $('#blogpost_content');

  		if ( !title.val() || !content.val()  ) {
  			$('#blogpost_message').show();

  			if ( !title.val() ) { toggleInvalid(title, true); }
			if ( !content.val() ) { toggleInvalid(content, true); }  			

  			return;
  		}
  		else {
  			$('#blogpost_message').hide();

  			toggleInvalid(title, false);
  			toggleInvalid(content, false);
  		}

  		var data = {
    	  	action: 'publish_post',
	        title: title.val(),
	        content: content.val()
	    };

	    // console.log(data);
	    // return;

	    $.post(ajaxurl, data, function (response) {
	        if ( response && response.new_post_id ) {
	        	alert('Post created successfully!');
	            console.log(response.new_post_id);

	            // Reset blog post form fields...
	            title.val('');
	            content.val('');
	        }
	    },'json');
  	});

  	$('#submit_listing').on('click', function (event) {
		// $('#loading_overlay').show();

       	// Hide all previous validation issues
       	$('#listing_message').hide();

       	// Prep form values for submission
        var form_values = {}
        form_values['action'] = 'add_listing';
        
        // Get each of the form values, set key/values in array based off name attribute
        $.each($('#create_listing :input').serializeArray(), function (i, field) {
    		form_values[field.name] = field.value;
        });
       

        // console.log(form_values); 
        // return;
        
        $.post(ajaxurl, form_values, function (response) {
			// $('#loading_overlay').hide();
			if (response && response['validations']) {
				var item_messages = [];

				for (var key in response['validations']) 
				{
					var item = response['validations'][key];

					if (typeof item == 'object') {
						for (var k in item) {
							if (typeof item[k] == 'string') {
								var message = '<p class="red">' + response['human_names'][key] + ' ' + item[k] + '</p>';
							} else {
								var message = '<p class="red">' + response['human_names'][k] + ' ' + item[k].join(',') + '</p>';
							}
							// $("#" + key + '-' + k).prepend(message);
							item_messages.push(message);
						}
					} 
					else {
						var message = '<p class="red">'+item[key].join(',') + '</p>';
						// $("#" + key).prepend(message);
						item_messages.push(message);
					}
				} 

				// Populate and show error messages...
				$('#listing_message').html( '<h3>' + response['message'] + '</h3>' + item_messages.join(' ') );
				$('#listing_message').show();
			} 
			else if (response && response['id']) {
				alert('Listing successfully created!');
				// $('#manage_listing_message').html('<div id="message" class="updated below-h2"><p>Listing successfully created!</p></div>');
			}
		}, 'json');
    });
});	


