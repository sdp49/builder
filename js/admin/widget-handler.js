widget_autosave = function() {
	
	var post_id = jQuery("#post_ID").val();
	
	autosave();
	
	setTimeout('pl_update_iframe_preview', 2000);
	
	function pl_update_iframe_preview() {
		jQuery('#preview-meta-widget').html("<iframe src='" + siteurl + "/?p=" + post_id + "'></iframe>");
	}
	
	// jQuery('#preview-meta-widget').html("<iframe src='" + siteurl + "/?p=" + post_data['post_ID'] + "'></iframe>");
}