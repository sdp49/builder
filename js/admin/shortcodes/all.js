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
		var selected_cpt = $('#pl_post_type_dropdown').val().substring('pl_post_type_'.length);
		var selected_sc = pls_get_shortcode_by_post_type(selected_cpt);
		var $tpl_select = $('select[name="pl_template_'+selected_sc+'"]');
		var selected = $tpl_select.find(':selected');
		var selected_tpl = $tpl_select.val();
		var selected_tpl_type = selected.parent().prop('label');
		
		if (selected_tpl_type=='Default') {
			$('#edit_sc_template_create').attr("href", pl_sc_template_url+'&type='+selected_sc).show();
			$('#edit_sc_template_edit').hide();
		}
		else {
			$('#edit_sc_template_create').hide();
			$('#edit_sc_template_edit').attr("href", pl_sc_template_url+'&type='+selected_sc+'&id='+selected_tpl).show();
		}
	}

	/**
	 * Any time we change a field on the shortcode edit page call this to save changes, which updates the preview window
	 */
	function widget_autosave() {

		if (!validate_title()) {
			return;
		}

		$('#preview_load_spinner').show();
		
		var post_id = $("#post_ID").val();
		var post_type = $('#pl_post_type').val() || "";
		var shortcode_type = pls_get_shortcode_by_post_type( post_type );
		var widget_class = $('#widget_class').val() || '';
		var featured = {};

		$("input[name^='pl_featured_listing_meta']").map(function() {
			var element_name = $(this).attr('name');
			var open_bracket = element_name.lastIndexOf('[');
			var close_bracket = element_name.lastIndexOf(']');
			var element_key = element_name.substring( open_bracket + 1, close_bracket );

			featured[element_key] = $(this).val();
		});
		
		// set a limit on max widget size
		var $width = $('#widget_meta_wrapper input#width');
		if ($width.val() > 1024 ) {
			$width.val('1024');
		}
		var $height = $('#widget_meta_wrapper input#height');
		if ($height.val() > 1024 ) {
			$height.val('1024');
		}

		var static_listings = {};
		static_listings.location = {};
		static_listings.metadata = {};

		// manage static listings form params
		if( post_type === 'static_listings' 
			|| post_type === 'search_listings'
			|| post_type === 'pl_static_listings' 
			|| post_type === 'pl_search_listings' ) {
			$('#pl_static_listing_block .form_group input, #pl_static_listing_block .form_group select').each(function() {
				// omit blank values and not filled ones
				var value = $(this).val();
				if( value !== undefined && value !== false && value !== '' ) {
					var id = this.id;

					if( id.indexOf('location-') !== -1 ) {
						// get the part after location
						if( this.value != 'false' ) {
							var field = id.substring( 9 );
							static_listings.location[field] = value;
						}
					} else if( id.indexOf('metadata-') !== -1 ) {
						// don't mark checkboxes as true in filters
						if( ( this.type == 'checkbox' && this.checked ) ) {
							// get the part after metadata
							var field = id.substring( 9 );
							static_listings.metadata[field] = value;
							// input checkbox is with value true in the search filters
						} else if( this.type != 'checkbox' && this.value != 'false' && this.value != "0" ) {
							var field = id.substring( 9 );
							static_listings.metadata[field] = value;
						}
					} else {
						static_listings[id] = value;
					}

				}
			});
		}

		// debugger;

		var radio_type = $("input[name='radio-type']:checked").val();
		var neighborhood_type = 'nb-id-select-' + radio_type;
		var neighborhood_value = $('#' + neighborhood_type).val();

		// the selector to fetch the template from
		var tpl_selector =  '#' + shortcode_type + '_template_block input.shortcode[value="' + shortcode_type + '"]';

		var post_data = {
				'post_id': post_id,
				'action': 'autosave_widget',
				'pl_post_type': post_type,
				'post_title': $('#title').val(),
				'pl_cpt_template': $(tpl_selector).parent().find('option:selected').val(),
				'pl_template_before_block': $('pl_template_before_block').val(),
				'pl_template_after_block': $('pl_template_after_block').val(),
				'width': $('#widget_meta_wrapper input#width').val() || "250",
				'height': $('#widget_meta_wrapper input#height').val() || "250",
				'pl_featured_listing_meta': JSON.stringify(featured),
				'radio-type': radio_type,
				'meta_box_nonce': $('#meta_box_nonce').val(),
				'listing_types': static_listings['listing_types'] || 'false',
	//			'zoning_types': static_listings['zoning_types'] || 'false',
	//			'purchase_types': static_listings['purchase_types'] || 'false',
				'location': JSON.stringify( static_listings.location ),
				'metadata': JSON.stringify( static_listings.metadata ),
				'hide_sort_by': $('#hide_sort_by').is(':checked'),
				'hide_sort_direction': $('#hide_sort_direction').is(':checked'),
				'hide_num_results': $('#hide_num_results').is(':checked'),
				'form_action_url': $('#form_action_url').val(),
				'widget_class': widget_class
		};

		post_data[neighborhood_type] = neighborhood_value;
		post_data[radio_type] = neighborhood_value;

		var num_results_shown = $('#num_results_shown').val();
		if( num_results_shown  !== '' ) {
			if( /^[0-9]+$/.test(num_results_shown)
					&& num_results_shown >= 0
					&& num_results_shown <= 50) {
				post_data['num_results_shown'] = num_results_shown;
			}
		}

		$.ajax({
			data: post_data,
			// beforeSend: doAutoSave ? autosave_loading : null,
			type: "POST",
			url: ajaxurl,
			success: function( response ) {
				setTimeout(function() {
					// breaks the overall layout
					// var frame_width = post_data['width'];
					var frame_width = '250';
					var frame_height = '250';
					var post_id = $("#post_ID").val();

					var widget_class = $('#widget_class').val() || '';
					if( widget_class !== '' ) {
						widget_class = 'class="' + widget_class + '"';
					}

					$('#preview-meta-widget').html("<iframe src='" + siteurl + "/?p=" + post_id + "&preview=true' width='" + frame_width + "px' height='" + frame_height + "px' " + widget_class + "></iframe>");
					$('#preview-meta-widget iframe').load( function() {
						$('#preview_load_spinner').hide();
					});
					// $('#preview-meta-widget').css('height', post_data['height']);
					$('#pl-review-link').show();
				}, 800);
			}
		});
	}
	
	/**
	 * Save for the template edit page
	 */
	function widget_template_autosave() {

		$('#preview_load_spinner').show();

		var post_type = $('#pl_post_type').val() || "";
		var shortcode_type = pls_get_shortcode_by_post_type(post_type);
		var snippets = $('textarea.snippet').serialize();
		var post_data = {
				'action': 'autosave_widget_template',
				'shortcode': shortcode_type,
				'title': $('#title').val(),
				'snippets': snippets,
		};

		$.ajax({
			data: post_data,
			// beforeSend: doAutoSave ? autosave_loading : null,
			type: "POST",
			url: ajaxurl,
			success: function( response ) {
				setTimeout(function() {
					// update the preview window
					var post_id = $("#post_ID").val();
					var post_type = $('#pl_post_type').val() || "";
					var post_data = {
							'post_id': post_id,
							'action': 'autosave_widget',
							'pl_post_type': post_type,
							'post_title': $('#title').val()+'-template-test',
							'pl_cpt_template': $('#title').val(),
							'pl_template_before_block': $('pl_template_before_block').val(),
							'pl_template_after_block': $('pl_template_after_block').val(),
							'width': "250",
							'height': "250",
							'meta_box_nonce': $('#meta_box_nonce').val(),
							'listing_types': 'false',
							'location': '',
							'metadata': '',
							'hide_sort_by': true,
							'hide_sort_direction': true,
							'hide_num_results': true,
							'form_action_url': $('#form_action_url').val(),
					};

					$.ajax({
						data: post_data,
						// beforeSend: doAutoSave ? autosave_loading : null,
						type: "POST",
						url: ajaxurl,
						success: function( response ) {
							setTimeout(function() {
								// breaks the overall layout
								// var frame_width = post_data['width'];
								var frame_width = '250';
								var frame_height = '250';
								var post_id = $("#post_ID").val();

								var widget_class = $('#widget_class').val() || '';
								if( widget_class !== '' ) {
									widget_class = 'class="' + widget_class + '"';
								}

								$('#preview-meta-widget').html("<iframe src='" + siteurl + "/?p=" + post_id + "&preview=true' width='" + frame_width + "px' height='" + frame_height + "px' " + widget_class + "></iframe>");
								$('#preview-meta-widget iframe').load( function() {
									$('#preview_load_spinner').hide();
								});
								// $('#preview-meta-widget').css('height', post_data['height']);
								$('#pl-review-link').show();
							}, 800);
						}
					});
				}, 800);
			}
		});
	}
	

	function pls_get_shortcode_by_post_type( post_type ) {
		switch( post_type ) {
		case 'pl_search_listings':		return 'search_listings';
		case 'pl_map':					return 'search_map';
		case 'pl_form':					return 'search_form';
		case 'pl_slideshow':			return 'listing_slideshow';
		case 'pl_static_listings':		return 'static_listings';
		default:						return post_type;
		}
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
	// Changing shortcode type
	////////////////////////////////////////
	$('#pl_post_type_dropdown').change(function() {

		var selected_cpt = $(this).val().substring('pl_post_type_'.length);
		if( selected_cpt == 'undefined' ) {
			// clicking "Select" shouldn't reflect the choice
			$('#choose_template').hide();
			$('#widget_meta_wrapper').hide();
			return;
		}
		update_template_links();
		
		$('#choose_template').show();
		$('#widget_meta_wrapper').show();

		// $('#post_types_list a').removeClass('selected_type');
		// $(this).addClass('selected_type');
		$('#pl_post_type').val(selected_cpt);

		// hide values not related to the post type and reveal the ones to be used
		$('#widget_meta_wrapper .pl_widget_block > section').each(function() {
			var section_class = $(this).attr('class');
			if( section_class !== undefined  ) {
				if( section_class.indexOf( selected_cpt ) !== -1  ) {
					$(this).show();
					// $(this).find('input').removeAttr('disabled');
					// $(this).find('select').removeAttr('disabled');
				} else {
					$(this).hide();
					// $(this).find('input, select').attr('disabled', true);
				}
			}
		});

		// fix inner sections for some CPTs
		if( selected_cpt == 'static_listings' || selected_cpt == 'pl_search_listings' ) {
			$('.form_group, .form_group section').show();
			$('#pl_static_listing_block #advanced').hide();
			$('#pl_static_listing_block #amenities').hide();
			$('#pl_static_listing_block #custom').hide();
			$('#general_widget_zoning_types').hide();
			$('#general_widget_purchase_types').hide();
		} else if( selected_cpt == 'pl_neighborhood' ) {
			$('.pl_neighborhood.pl_widget_block, .pl_neighborhood section').show();
		}

		// display template blocks
		$('.pl_template_block').each(function() {
			var selected_cpt = $('#pl_post_type').val();
			var block_id = $(this).attr('id');
			selected_cpt = selected_cpt.replace('pl_', '');

			if( block_id.indexOf( selected_cpt ) !== -1 ) {
				$(this).css('display', 'block');
			} else {
				$(this).css('display', 'none');
			}
		});

		// display/hide featured/static listings
		var featured_class = $('#pl_featured_listing_block').attr('class');
		var static_class = $('#pl_static_listing_block').attr('class');

		if( featured_class.indexOf( selected_cpt ) === -1 ) {
			$('#pl_featured_listing_block').hide();
		} else {
			$('#pl_featured_listing_block').show();
		}

		if( static_class.indexOf( selected_cpt ) === -1 ) {
			$('#pl_static_listing_block').hide();
		} else {
			$('#pl_static_listing_block').show();
		}

		$('#widget_meta_wrapper input, #widget_meta_wrapper select').css('background', '#ffffff');
		$('#widget_meta_wrapper input:disabled, #widget_meta_wrapper select:disabled').css('background', '#dddddd');
	});
	
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

		var iframe_content = $('#preview-meta-widget').html();
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

	// reset before the view, hide everything
	$('#widget_meta_wrapper section, #pl_featured_listing_block').hide();
	$('.pl_template_block section').show();
	
	var $selected_cpt = $('#edit-sc-choose-type select');
	if ($selected_cpt.length && $selected_cpt.val().substring('pl_post_type_'.length) != 'undefined' ) {
		$('#widget_meta_wrapper').show();
	}

	// call the custom autosave for every changed input and select in the shortcode edit view
	$('#pl_sc_edit input, #pl_sc_edit select').change(function() {
		widget_autosave();
	});

	wptitlehint();
	
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
	
	// Update preview when creating a new template
	$('.save_snippet').click(function() {
		$('#tpl_post_type').trigger('change');
	});

	function type_selected() {
		var selected_cpt = $('#tpl_post_type').val().substring('pl_post_type_'.length);
		$('#pl_post_type').val(selected_cpt);
		var selected_shortcode = pls_get_shortcode_by_post_type(selected_cpt);
		// update the shortcode hints
		if ($('#shortcodes').length) {
			var ref = $('#'+selected_shortcode+'_ref').html();
			$('#shortcodes').html(ref);
		}
	}
	
	$('#tpl_post_type').change(type_selected);

	// call the custom autosave for every changed input and select in the template edit view
	$('#pl_sc_tpl_edit').find('input, select, textarea').change(function() {
		widget_template_autosave();
	});
	
	$('#popup_existing_template').click(function(e){
		e.preventDefault();
		
	});
	
	// trigger an event to set up the preview pane on page load 
	$('#tpl_post_type').trigger('change');
});
