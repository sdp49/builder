/**
 * Used by the admin shortcode settings pages
 */

jQuery(document).ready(function($){

	/**
	 * Put hint text into a text input
	 */
	function wptitlehint(id) {
		id = id || 'title';

		var title = $('#' + id), titleprompt = $('#' + id + '-prompt-text');

		if ( title.val() == '' )
			titleprompt.removeClass('screen-reader-text');

		titleprompt.click(function(){
			$(this).addClass('screen-reader-text');
			title.focus();
		});

		title.blur(function(){
			if ( this.value == '' )
				titleprompt.removeClass('screen-reader-text');
		}).focus(function(){
			titleprompt.addClass('screen-reader-text');
		}).keydown(function(e){
			titleprompt.addClass('screen-reader-text');
			$(this).unbind(e);
		});
	}
	
	function validate_title(field_id) {
		if (!field_id) {
			field_id = '#title';
		}
        var $title = $(field_id);
        if ($title.val() == '') {
            var prompt = $title.attr('title');
            if (prompt) {
                alert(prompt);
            }
            else {
                alert('Please enter a title first.');
            }
            return false;
        }
		return true;
	}

	function update_template_links() {
		var shortcode_type = $('#pl_sc_shortcode_type').val();
		var shortcode = $('#pl_sc_edit input[name="shortcode"]').val();
		var tpl_select = $('#'+shortcode+'_template_block select');
		if (tpl_select) {
			var selected = tpl_select.find(':selected');
			var selected_tpl = tpl_select.val();
			var selected_tpl_type = selected.parent().prop('label');
			
			if (selected_tpl_type=='Default') {
				$('#edit_sc_template_create').attr("href", pl_sc_template_url+'&type='+shortcode_type).show();
				$('#edit_sc_template_edit').hide();
			}
			else {
				$('#edit_sc_template_create').hide();
				$('#edit_sc_template_edit').attr("href", pl_sc_template_url+'&type='+shortcode_type+'&title='+selected_tpl).show();
			}
		}
	}

	/**
	 * Any time we change a field on the shortcode edit page call this to save changes, which updates the preview window
	 */
	function widget_autosave() {

		if (!validate_title()) {
			return;
		}

		$('#pl_sc_edit .preview_load_spinner').show();
		$('#pl-review-link').hide();
		
		// set a limit on max widget size
		var $width = $('#widget_meta_wrapper input#width');
		if ($width.val() > 1024 ) {
			$width.val('1024');
		}
		var $height = $('#widget_meta_wrapper input#height');
		if ($height.val() > 1024 ) {
			$height.val('1024');
		}

		var post_data = $('#pl_sc_edit form').serializeArray();

		$.ajax({
			data: post_data,
			// beforeSend: doAutoSave ? autosave_loading : null,
			type: "POST",
			url: ajaxurl,
			success: function( response ) {
				// setup the preview window
				$('#preview_meta_widget iframe').load( function() {
					$('#pl_sc_edit .preview_load_spinner').hide();
					$('#pl-review-link').show();
				});
				// update the embed/shortcode box
				$('#sc_slug_box').show();
				if (response.embedcode) {
					$('#sc_slug_box .iframe_link').show().html('<strong>Embed Code:</strong>'+response.shortcode);
				}
				else {
					$('#sc_slug_box .iframe_link').hide();
				}
				if (response.shortcode) {
					$('#sc_slug_box .shortcode_link').show().html('<strong>Shortcode:</strong>'+response.shortcode);
				} 
				else {
					$('#sc_slug_box .shortcode_link').hide();
				}
			}
		});
	}
	
	function radioClicks() {
		var radio_value = this.value;

		$('.nb-taxonomy').each(function() {
			if( radio_value !== 'undefined') {
				if( this.id.indexOf(radio_value, this.id.length - radio_value.length) !== -1 ) {
					$(this).css('display', 'block');
				} else {
					$(this).css('display', 'none');
				}
			}
		});
	}


	////////////////////////////////////////
	//Toggle Before/After Widget Textarea views
	////////////////////////////////////////

	$('.toggle').click(function(event) {
		event.preventDefault();
		var n = $(this).attr('id').lastIndexOf('_toggle'), $target;
		if (n && ($target = $('#'+$(this).attr('id').substr(0,n)))) {
			$target.toggle();
		}
	});

	if ( $('#before_widget').val() ) {
		$('#before_widget_wrapper').show();
	};

	if ( $('#after_widget').val() ) {
		$('#after_widget_wrapper').show();
	};


	$('#pl_location_tax input:radio').click(radioClicks);

	if($('#metadata-max_avail_on_picker').length) {
		$('#metadata-max_avail_on_picker').datepicker();
		$('#metadata-min_avail_on_picker').datepicker();
	}

	
	////////////////////////////////////////
	// Shortcode editor
	////////////////////////////////////////
	
	// Changing shortcode type - update display options
	function sc_shortcode_selected() {
		var shortcode_type = $('#pl_sc_shortcode_type').val();
		var shortcode = $('#pl_sc_shortcode_type').find('option:selected').attr('id').substr('pl_sc_shortcode_'.length);
		if( shortcode_type == 'undefined' ) {
			// clicking "Select" shouldn't reflect the choice
			$('#choose_template').hide();
			$('#widget_meta_wrapper').hide();
			return;
		}
		$('#pl_sc_edit input[name="shortcode"]').val(shortcode);
		update_template_links();

		// display template blocks
		$('#pl_sc_edit .pl_template_block').each(function() {
			$(this).css('display', ($(this).hasClass(shortcode_type) ? 'block' : 'none'));
		});
		
		// hide meta blocks not related to the post type and reveal the ones to be used
		$('#pl_sc_edit .pl_widget_block').each(function() {
			$(this).css('display', ($(this).hasClass(shortcode_type) ? 'block' : 'none'));
		});
		
		$('#choose_template').show();
		$('#widget_meta_wrapper').show();
	}
	
	$('#pl_sc_shortcode_type').change(sc_shortcode_selected);
	
	$('#pl_sc_edit .snippet_list').change(function(){
		update_template_links();
	});

	// hide advanced values for static listings area
	$('#pl_static_listing_block #advanced').css('display', 'none');
	$('#pl_static_listing_block #amenities').css('display', 'none');
	$('#pl_static_listing_block #custom').css('display', 'none');
	$('<a href="#basic" id="pl_show_advanced" style="line-height: 50px;">Show Advanced filters</a>').insertBefore('#pl_static_listing_block #advanced');
	$('<a href="#basic" id="pl_hide_advanced" style="line-height: 50px; display: none;">Hide Advanced filters</a>').insertAfter('#pl_static_listing_block #custom');
	$('#pl_show_advanced').click(function() {
		$(this).hide();
		$('#pl_static_listing_block #advanced').css('display', 'block');
		$('#pl_static_listing_block #amenities').css('display', 'block');
		$('#pl_static_listing_block #custom').css('display', 'block');
		$('#pl_hide_advanced').show();
	});
	$('#pl_hide_advanced').click(function() {
		$(this).hide();
		$('#pl_static_listing_block #advanced').css('display', 'none');
		$('#pl_static_listing_block #amenities').css('display', 'none');
		$('#pl_static_listing_block #custom').css('display', 'none');
		$('#pl_show_advanced').show();
	});
	$('#save-featured-listings').click(function() {
		setTimeout( widget_autosave, 1000 );
	});

	// popup preview dialog
	$('#pl-review-link').click(function(e) {
		e.preventDefault();

		var iframe_content = $('#preview_meta_widget').html();
		var options_width = $('#widget_meta_wrapper input#width').val() || 750;
		var options_height = $('#widget_meta_wrapper input#height').val() || 500;

		$('#pl-review-popup').html( iframe_content );
		$('#pl-review-popup iframe').css('width', options_width + 'px');
		$('#pl-review-popup iframe').css('height', options_height + 'px');

		$('#pl-review-popup').dialog({
			width: 800,
			height: 600
		});
	});

	// setup view based on current shortcode type, etc
	wptitlehint();
	$('#pl_sc_shortcode_type').trigger('change');

	// call the custom autosave for every changed input and select in the shortcode edit view
	$('#pl_sc_edit input, #pl_sc_edit select').change(function() {
		widget_autosave();
	});


	try{
		//$('#title').focus();
		// force a title in shortcode edit page
		// TODO not working on safari
		$('#pl_sc_edit').find('input,select,button').not('#title').click(function(e){
			if (!validate_title()) {
				e.preventDefault();
				$('#title').focus();
				return;
			}
		});
		type_selected();
	}catch(e){}


	////////////////////////////////////////
	// Template editor
	////////////////////////////////////////
	
	/**
	 * When the shortcode type is changed update hints, etc
	 */
	function tpl_type_selected() {
		var shortcode_type = $('#pl_sc_tpl_post_type').val();
		// update the shortcode hints
		$('#subshortcodes .shortcode_block').hide();
		$('#subshortcodes .shortcode_block.'+shortcode_type).show();
		var shortcode = $('#pl_sc_tpl_post_type option:selected').attr('id').substr('pl_sc_tpl_shortcode_'.length);
		$('#pl_sc_tpl_edit input[name="shortcode"]').val(shortcode);
	}
	
	/**
	 * Push edits on the template edit page so we can update the preview
	 */
	function tpl_update_preview() {
		$('#pl_sc_tpl_edit .preview_load_spinner').show();
		
		var shortcode = $('#pl_sc_tpl_edit input[name="shortcode"]').val();
		var post_data = {
				'before_widget': $('#before_widget').val(),
				'after_widget': $('#after_widget').val(),
				'snippet_body': $('#snippet_body').val(),
				'widget_css': $('#widget_css').val(),
				'width': 250,
				'height': 250,
				'meta_box_nonce': $('#meta_box_nonce').val(),
				'listing_types': 'false',
				'location': '',
				'metadata': '',
				'hide_sort_by': true,
				'hide_sort_direction': true,
				'hide_num_results': true,
		};
		var args = encodeURIComponent(JSON.stringify(post_data));

		$('#preview_meta_widget').html('<iframe src="'+ajaxurl+'?action=pl_widget_preview&shortcode='+shortcode+'&args='+args+'" width="250px" height="250px"></iframe>');
		$('#preview_meta_widget iframe').load( function() {
			$('#pl_sc_tpl_edit .preview_load_spinner').hide();
		});
		$('#pl_sc_tpl_edit #pl-review-link').show();
	}
	

	$('#pl_sc_tpl_post_type').change(tpl_type_selected);

	// call the custom autosave for every changed input and select in the template edit view
	$('#pl_sc_tpl_edit').find('input, select, textarea').change(function() {
		tpl_update_preview();
	});
	
	// Update preview when creating a new template
	$('.save_snippet').click(function() {
		$('#pl_sc_tpl_post_type').trigger('change');
	});

	$('#popup_existing_template').click(function(e){
		e.preventDefault();
	});
	
	// trigger an event to set up the preview pane on page load 
	$('#pl_sc_tpl_post_type').trigger('change');
});
