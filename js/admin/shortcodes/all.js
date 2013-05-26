/**
 * Used by the admin shortcode settings pages
 */

jQuery(document).ready(function($){

	wptitlehint = function(id) {
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
	wptitlehint();
	
	function validate_title() {
        var $title = $('input#title');
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

	/**
	 * Any time we change a field call this to save changes, which updates the preview window
	 */
	function widget_autosave() {

		if (!validate_title()) {
			return;
		}

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

		var static_listings = {};
		static_listings.location = {};
		static_listings.metadata = {};

		// manage static listings form params
		if( post_type === 'static_listings' || post_type === 'search_listings'
			|| post_type === 'pl_static_listings' || post_type === 'pl_search_listings' ) {
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
				'width': $('#widget-meta-wrapper input#width').val() || "250",
				'height': $('#widget-meta-wrapper input#height').val() || "250",
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
					// $('#preview-meta-widget').css('height', post_data['height']);
					$('#pl-review-link').show();
				}, 800);
			}
		});
	};

	function pls_get_shortcode_by_post_type( post_type ) {
		switch( post_type ) {
		case 'pl_search_listings':		return 'search_listings';
		case 'pl_map':					return 'search_map';
		case 'pl_form':					return 'search_form';
		case 'pl_slideshow':			return 'listing_slideshow';
		case 'pl_static_listings':		return 'static_listings';

		default:
			return post_type;
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
	//Activate Chosen
	////////////////////////////////////////

	$("select.chosen").chosen({no_results_text: "No results matched"});


	////////////////////////////////////////
	//Toggle Before/After Widget Textarea views
	////////////////////////////////////////

	$('#toggle-before-widget').click(function() {
		event.preventDefault();
		if ($('#before-widget').hasClass('is-visible')) {
			$('#before-widget').removeClass('is-visible');
		} else {
			$('#before-widget').addClass('is-visible');
		};
	});

	$('#toggle-after-widget').click(function() {
		event.preventDefault();
		if ($('#after-widget').hasClass('is-visible')) {
			$('#after-widget').removeClass('is-visible');
		} else {
			$('#after-widget').addClass('is-visible');
		};
	});

	if ( $('#before-widget-textarea').val() ) {
		$('#before-widget-wrapper').addClass('is-visible');
	};

	if ( $('#after-widget-textarea').val() ) {
		$('#after-widget-wrapper').addClass('is-visible');
	};


	////////////////////////////////////////
	//Add/Remove Options/Filters Key/Value Pairs
	////////////////////////////////////////

	// Add option/filter
	$('#add-new-option').click(function() {

		var k = $('#sc-add-option-key select').val();
		var v = $('#sc-add-option-value .active').val();

		$('#added-filters').append(
				'<div class="row-fluid added-filters-single added-kv-pair">' +
				'<div class="span6 unified-row">' + k + '</div>' +
				'<div class="span4 unified-row">' + v + '</div>' +
				'<input type="hidden" name="' + k + '" value="' + v + '">' +
				'<div class="span1"><button class="btn btn-danger delete-option">x</button></div>' +
				'</div>'
		);

	});

	// Remove option/filter
	$('.delete-option').click(function() {
		$(this).parent().parent().remove();
	});


	////////////////////////////////////////
	//Textarea -> CodeMirror
	////////////////////////////////////////

	if (window.CodeMirror!==undefined && $.isFunction(CodeMirror)) {
		var el = document.getElementById("html-textarea");
		if (el) {
			CodeMirror.fromTextArea(el, {
				mode: 'text/html',
				lineNumbers: true,
				viewportMargin: Infinity,
				styleActiveLine: true,
				autoCloseBrackets: true,
				autoCloseTags: true,
				placeholder: "Put your HTML with sub-shortcodes code here...",
				highlightSelectionMatches: true
			});
		}

		el = document.getElementById("css-textarea");
		if (el) {
			CodeMirror.fromTextArea(el, {
				mode: 'text/css',
				lineNumbers: true,
				viewportMargin: Infinity,
				styleActiveLine: true,
				autoCloseBrackets: true,
				autoCloseTags: true,
				placeholder: "Put your CSS code here...",
				highlightSelectionMatches: true
			});
		}

		el = document.getElementById("before-widget-textarea");
		if (el) {
			CodeMirror.fromTextArea(el, {
				mode: 'text/html',
				lineNumbers: true,
				viewportMargin: Infinity,
				styleActiveLine: true,
				autoCloseBrackets: true,
				autoCloseTags: true,
				placeholder: "Put HTML here that will appear before your shortcode....",
				highlightSelectionMatches: true
			});
		}

		el = document.getElementById("after-widget-textarea");
		if (el) {
			CodeMirror.fromTextArea(el, {
				mode: 'text/html',
				lineNumbers: true,
				viewportMargin: Infinity,
				styleActiveLine: true,
				autoCloseBrackets: true,
				autoCloseTags: true,
				placeholder: "Put HTML here that will appear after your shortcode....",
				highlightSelectionMatches: true
			});
		}
	};

	$('#pl_location_tax input:radio').click(radioClicks);

	if($('#metadata-max_avail_on_picker').length) {
		$('#metadata-max_avail_on_picker').datepicker();
		$('#metadata-min_avail_on_picker').datepicker();
	}

	// click a new post type as a widget type
	$('#edit-sc-choose-type select').change(function() {

		//var selected_cpt = $(this).attr('id').substring('pl_post_type_'.length);
		var selected_cpt = $(this).parent().find(':selected').val().substring('pl_post_type_'.length);
		if( selected_cpt == 'undefined' ) {
			// clicking "Select" shouldn't reflect the choice
			$('#widget-meta-wrapper').hide();
			return;
		}
		$('#widget-meta-wrapper').show();

		// $('#post_types_list a').removeClass('selected_type');
		// $(this).addClass('selected_type');
		$('#pl_post_type').val(selected_cpt);

		// hide values not related to the post type and reveal the ones to be used
		$('#widget-meta-wrapper .pl_widget_block > section, #pl_location_tax').each(function() {
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

		$('#preview-meta-widget').html(previewPlaceholderHtml);

		$('#widget-meta-wrapper input, #widget-meta-wrapper select').css('background', '#ffffff');
		$('#widget-meta-wrapper input:disabled, #widget-meta-wrapper select:disabled').css('background', '#dddddd');
	});

	// call the custom autosave for every changed input and select
	$('#pl_shortcode_edit input, #pl_shortcode_edit select').change(function() {
		widget_autosave();
	});
	$('#save-featured-listings').click(function() {
		setTimeout( widget_autosave, 1000 );
	});

	$('#pl-review-link').click(function(e) {
		e.preventDefault();

		var iframe_content = $('#preview-meta-widget').html();
		var options_width = $('#widget-meta-wrapper input#width').val() || 750;
		var options_height = $('#widget-meta-wrapper input#height').val() || 500;

		$('#pl-review-popup').html( iframe_content );
		$('#pl-review-popup iframe').css('width', options_width + 'px');
		$('#pl-review-popup iframe').css('height', options_height + 'px');

		$('#pl-review-popup').dialog({
			width: 800,
			height: 600
		});
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

	// reset before the view, hide everything
	$('#widget-meta-wrapper section, #pl_featured_listing_block').hide();
	$('.pl_template_block section').show();
	$('#widget-meta-wrapper').show();

	// Update preview when creating a new template
	$('.save_snippet').click(function() {
		$('#pl_post_type_dropdown').trigger('change');
	});

	$('#pl-previewer-metabox-id .handlediv').click(function() {
		if ( $('#pl-previewer-metabox-id').hasClass('closed') ){
			$('#pl-previewer-metabox-id').css('min-height', '350px');
		} else {
			$('#pl-previewer-metabox-id').css('min-height', '0');
		}
	});

	try{
//		$('#title').focus();
		$('.pl-sc-wrap').find('input,select,button').not('#title').click(function(e){
			if (!validate_title()) {
				e.preventDefault();
				$('#title').focus();
			}
		});
	}catch(e){}
});
