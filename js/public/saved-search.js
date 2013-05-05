jQuery(document).ready(function($) {

  $("#pl_saved_search_register_form").dialog({
      modal: true,
      draggable: false,
      resizable: false,
      dialogClass: 'lead-capture-wrapper',
      width: 450,
      autoOpen:false,
      open: function(event, ui){
          dialog_opened = true;
      },
      close: function (event, ui) {
        jQuery(".contact-form-validator-error").remove();
        
        var this_form = jQuery(element_id);
        
        // If form has invalide fields...
        if (jQuery(this_form).find("input[name='form_submitted']").val() == 0) {
          // If form value for forcing user back when they cancel the lead capture form
          if (jQuery(this_form).find("input[name='back_on_lc_cancel']").val() == 1) {
            // send them back to whatever page they came from
            window.history.back();
          };
        }
      }
  });

    //show the saved search dialog on click of saved 
    //search button
    $('.pls_save_search').on('click', function () {
      append_search_terms_to_saved_search_form();
      $('#pl_saved_search_register_form').dialog('open');
    });

    $('#pl_submit').on('click', function (event) {
        //prevent the submit 
        event.preventDefault()
        
        var data = {};

        data.action = 'save_search';
        data.email = $('#user_search_email').val();
        data.name_of_saved_search = $('#user_search_name').val();
        data.search_form_key_values = get_search_form_key_values();

        $.post(info.ajaxurl, data, function(response, textStatus, xhr) {
          console.log(response);

          if (response === 1) {
            //success.
            //close dialog
            //show some success message
          } else {
            //failed, show the error messages
          };
        });
        

    });


    //method to retrieve all the keys and values of the search form on the page.
    //these key value pairs are used to "save" to search in the db so that it can be
    //re-applied later.
    function get_search_form_key_values () {

      var search_form_key_values = {};

      //find the value of all the search elements so that we can save them.
      $('.pls_search_form_listings').find('input, select, textarea').each(function() {
        var control = $(this);
        var nameAttr = control.attr('name');
        var isName = typeof(nameAttr) !== 'undefined' && nameAttr !== false;

        if(isName && control.val() !== '' && control.val() != '0' ) {
           search_form_key_values[nameAttr] = control.val();
        } 
      });

      return search_form_key_values;
    }


    function append_search_terms_to_saved_search_form () {
      var search_form_key_values = get_search_form_key_values();
      //remove any form values that don't need to be displayed to the user
      //like "submit"
      var cleaned_form_key_values = purge_unneeded_form_data( search_form_key_values );

      //remove any li items in the ul left over from an old search
      $('#saved_search_values ul').empty();

      for ( var key in cleaned_form_key_values ) {

        var form_attribute_value = cleaned_form_key_values[key];

        //form keys come as the value of their "name" (eg location[locality] ). 
        //form_key_translations is a simple lookup table 
        //to translate them into human readable form.
        if (form_key_translations.hasOwnProperty(key)) {
          key = form_key_translations[key];
        };

        var html = "<li><span>" + key + "</span>: " + form_attribute_value + "</li>"
        $('#saved_search_values ul').append(html)
      }
      

    }

    //removes form data that doesn't need to be 
    //displayed to the user. 
    function purge_unneeded_form_data ( form_data ) {
      
      var cleaned_form_key_values = {};

      for ( var key in form_data ) {
        if ( key !== 'action' && key !== 'submit' && key !== 'location[address_match]' ) {
          cleaned_form_key_values[key] = form_data[key];
        }
      }

      return cleaned_form_key_values;
    }

    //An array that translates search form keys into 
    //human readable form.
    var form_key_translations = {
      "location[locality]": "City"
    }

});





