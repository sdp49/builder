/**
 * Used by the admin idx builder pages
 */

jQuery(document).ready(function($){
	

	function addActiveFilter(id, value) {
		var label = $('#'+id+' label').html();
		var idparts = id.split('-');
		var name = '';
		for (var i=0; i<idparts.length; i++) {
			if (i<1) {
				continue;
			}
			else if (i==1) {
				name += 'filters';
			}
			else {
				name += '['+idparts[i]+']';
			}
		}
		var $input, dispvalue;
		if (($input = $('#'+id).find('select')) && $input.length) {
			if (typeof value !== 'undefined') {
				var option = $input.find('option[value="'+value+'"]');
				if (option.length != 0) {
					dispvalue = option.html();
				}
				else {
					dispvalue = value;
				}
			}
			else {
				value = $input.val();
				if ($.isArray(value)) {
					value.forEach(function(svalue){
						addActiveFilter(id, svalue);
					});
					return;
				}
				dispvalue = $input.find(':selected').html();
			}
		}
		else {
			$input = $('#'+id).find('input');
			if (typeof value !== 'undefined') {
				dispvalue = value;
			}
			else {
				if ($input.attr('type')=='checkbox' && !($input.attr('checked'))) return;
				value = $input.val();
				dispvalue = $input.val();
			}
		}
		var $filters = $('#pl_filter_picker .active_filters');
		if (!$input.data('multi')) {
			$filters.find('.'+id).remove();
		}
		$filters.find('.'+id).each(function(){
			if ($(this).find(' input[value="'+value+'"]').length) $(this).remove();
		});
		var $block = $('<div class="active_filter '+id+'"></div>').hide();
		var added = false;
        $filters.children().each(function(){
        	var elabel = $(this).find('label').html();
            if ((elabel==label && $(this).find('input').val()>value) || elabel > label) {
            	$block.insertBefore($(this)).fadeIn(800);
                added = true;
                return false;
            }
        });
        if(!added) $block.appendTo($filters).fadeIn(800);
		$block.append('<input type="hidden" name="'+name+'[]" value="'+value+'" />');
		$block.append('<label>'+label+'</label><span class="filter_value">'+dispvalue+'</span>');
		$block.append('<a href="#" class="button-secondary remove_filter">Remove</a>').find('.remove_filter').click(function(e){
			e.preventDefault();
			$block.remove();
			sc_update_preview();
			_changesMade = true;
		});
	}

	// set up filter selector
	$('#pl_filter_picker .filter_select').change(function(){
		$('#pl_filter_picker .pl_filters section').hide();
		$('#pl_filter_picker .pl_filters #' + this.value).show();
	});
	$('#pl_filter_picker .filter_select').each(function(){
		$('#pl_filter_picker .pl_filters #' + this.value).show();
	});
	$('#pl_filter_picker .add_filter').click(function(e){
		e.preventDefault();
		var id = $('#pl_filter_picker select[name="filter"]').val();

		addActiveFilter(id);
		_changesMade = true;
	});
	// date pickers
	$('#pl_filter_picker .form_item_date').each(function(){
		$(this).datepicker();
	});
	// show filter picker after template
	$('.pl_tmplt_select').click(function(e){
		e.preventDefault();
		var id = $(this).attr('data-tmplt_id');
		window.scrollTo(0, 0);
		$('input[name="options[context]"]').val(id);
		$('#pl_tmplt_picker').hide();
		$('#pl_filter_picker').show();
	});
	

});
