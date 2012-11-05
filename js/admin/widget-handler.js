widget_autosave = function() {
	
	var post_id = jQuery("#post_ID").val();
	
	//autosave();
	
	// var featured = jQuery("input[name^='pl_featured_listing_meta']").val();
	var featured = {};
	jQuery("input[name^='pl_featured_listing_meta']").map(function() {
			var element_name = jQuery(this).attr('name');
			var open_bracket = element_name.lastIndexOf('[');
			var close_bracket = element_name.lastIndexOf(']');
			var element_key = element_name.substring( open_bracket + 1, close_bracket );
			
			featured[element_key] = jQuery(this).val(); 
		}
	);

	var post_data = {
					'post_id': post_id,
	                'action': 'autosave_widget',
	                'post_type': jQuery('#pl_post_type :selected').val() || "",
	                'width': jQuery('#widget-meta-wrapper input#width').val() || "250",
	                'height': jQuery('#widget-meta-wrapper input#height').val() || "250",
	                'pl_featured_listing_meta': JSON.stringify(featured)
	};
	
	jQuery.ajax({
		data: post_data,
		// beforeSend: doAutoSave ? autosave_loading : null,
		type: "POST",
		url: ajaxurl,
		success: function( response ) {
			setTimeout(function() {
				var post_id = jQuery("#post_ID").val();
				jQuery('#preview-meta-widget').html("<iframe src='" + siteurl + "/?p=" + post_id +
						"&preview=true' width='" + post_data['width'] + "' height='" + post_data['height'] + "'></iframe>");
			}, 2000);
			// alert(response);
		}
	});
	
	
	// jQuery('#preview-meta-widget').html("<iframe src='" + siteurl + "/?p=" + post_data['post_ID'] + "'></iframe>");
}

function pl_update_iframe_preview() {
	var post_id = jQuery("#post_ID").val();
	jQuery('#preview-meta-widget').html("<iframe src='" + siteurl + "/?p=" + post_id + "'></iframe>");
}