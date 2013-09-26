jQuery(document).ready(function($) {

    $('.pls_save_search').fancybox({
        "hideOnContentClick": false,
        "scrolling": true,
        onStart: function () { append_search_terms_to_saved_search_form(); },
        onClosed: function () { $('.login-form-validator-error').remove(); }
    });

    $('#pl_submit').on('click', function (event) {
        //prevent the submit 
        event.preventDefault()
        
        var data = {};
        data.action = 'add_saved_search_to_user';
        data.link_to_search = document.URL;
        data.name_of_saved_search = $('#user_search_name').val();
        data.search_form_key_values = get_search_form_key_values();

        $.post(info.ajaxurl, data, function (response, textStatus, xhr) {
            // console.log(response);
            if (response == 'true') {
                // Close dialog
                $.fancybox.close();

                show_saved_search_success_message();
            } 
            else {
                // Failed, show the error messages...
            }
        });
      
    });

    // Show
    function show_saved_search_success_message () {
        $('#pls_successful_saved_search').show();
        setTimeout(function () { $('#pls_successful_saved_search').fadeOut(); }, 3000);
    }


    // Method to retrieve all the keys and values of the search form on the page
    //
    // NOTE: These key value pairs are used to "save" the search in the DB so that it can be re-applied later
    function get_search_form_key_values () {
        //
        var search_form_key_values = {};

        //find the value of all the search elements so that we can save them.
        $('.pls_search_form_listings').find('input, select, textarea').each(function() {
            var control = $(this);
            var nameAttr = control.attr('name');
            var isName = typeof(nameAttr) !== "undefined" && nameAttr !== false;

            if ( isName && control.val() !== "" && control.val() != "0" ) {
                search_form_key_values[nameAttr] = control.val();
            } 
        });

        return search_form_key_values;
    }


    function append_search_terms_to_saved_search_form () {
        var search_form_key_values = get_search_form_key_values();
        // Remove any form values that don't need to be displayed to the user like "submit"
        var cleaned_form_key_values = purge_unneeded_form_data( search_form_key_values );

        //remove any li items in the ul left over from an old search
        $('#saved_search_values ul').empty();

        for (var key in cleaned_form_key_values) {
            //
            var form_attribute_value = cleaned_form_key_values[key];

            //form keys come as the value of their "name" (eg location[locality] ). 
            //form_key_translations is a simple lookup table 
            //to translate them into human readable form.
            if (form_key_translations.hasOwnProperty(key)) {
                key = form_key_translations[key];
            }

            var html = "<li><span>" + key + "</span>: " + form_attribute_value + "</li>";
            $('#saved_search_values ul').append(html);
        }
    }

    // Removes form data that doesn't need to be displayed to the user
    function purge_unneeded_form_data (form_data) {
        var cleaned_form_key_values = {};

        for (var key in form_data) {
            if ( key !== "action" && key !== "submit" && key !== "location[address_match]" ) {
                cleaned_form_key_values[key] = form_data[key];
            }
        }

        return cleaned_form_key_values;
    }

    // An array that translates search form keys into human readable form
    var form_key_translations = {
        "location[locality]": "City",
        "location[postal]": "Zip",
        "location[address]": "Street",
        "location[neighborhood]": "Street",
        "location[region]": "State",
        "property_type" : "Property Type",
        "purchase_types[]" : "Available for",
        "metadata[min_price]" : "Min Price",
        "metadata[min_sqft]" : "Min Sqft",
        "metadata[min_beds]" : "Min Beds",
        "metadata[min_baths]" : "Min Baths",
        "metadata[min_price]" : "Min Price",
        "metadata[max_price]" : "Max Price"
    }

    /* 
     * Bindings for UI that generates the list of saved searches in the user's client profile... 
     */

    $('.pls_remove_search').live('click', function (event) {
        event.preventDefault();
        
        // So we can keep the HTML object context for use in the success call back
        var that = this;
        var data = {};
        data.action = 'delete_user_saved_search'
        data.saved_search_option_key = $(this).attr('ref');

        // console.log(data);

        $.post(info.ajaxurl, data, function(response, textStatus, xhr) {
            // Optional stuff to do after success
            // console.log(response);
            if (response == 1) {
                $('.saved_search_block#' + data.saved_search_option_key).remove();
            } 
            else {
                // show error message
            }
        });
        
    });

    $('#pls_view_search').on('click', function (event) {
        event.preventDefault();
        
        // Act on the event
        $.post(info.ajaxurl, {param1: 'value1'}, function (data, textStatus, xhr) {
            //optional stuff to do after success
        });
    });

});