jQuery(document).ready(function($) {

    $('#pl_lead_register_form').submit(function(e) {
        e.preventDefault();

        $this = $(this);
        nonce = $(this).find('#register_nonce_field').val();
        username = $(this).find('#user_email').val();
        email = $(this).find('#user_email').val();
        password = $(this).find('#user_password').val();
        confirm = $(this).find('#user_confirm').val();
        name = $(this).find('#user_fname').val();
        phone = $(this).find('#user_phone').val();

        data = {
            action: 'pl_register_lead',
            username: username,
            email: email,
            nonce: nonce,
            password: password,
            confirm: confirm,
            name: name,
            phone: phone
        };

        $.post(info.ajaxurl, data, function(response) {
            if (response) {             
                $('#form_message_box').html(response);
                $('#form_message_box').fadeIn('fast');
            } else {
                $('#form_message_box').html('You have been successfully signed up. This page will refresh momentarily.');
                $('#form_message_box').fadeIn('fast');
                setTimeout(function () {
                    window.location.href = window.location.href;
                }, 700);
                return true;
            }
        });

    });

    $('form#pl_login_form input[type="submit"]').on('mousedown', function() {

      var this_form = $('form#pl_login_form');
      
      // get fields that are required from form and execture validator()
      var inputs = $(this_form).find("input[required]").validator({
          messageClass: 'login-form-validator-error', 
          offset: [10,0],
          message: "<div><span></span></div>",
          position: 'top center'
        });
      
      // check required field's validity
      inputs.data("validator").checkValidity();
      
    });

    // initialize validator and add the custom form submission logic
    $("form#pl_login_form").bind('submit',function(e) {

      // prevent default form submission logic
      // e.preventDefault();
      var form = $(this);
       
      if ($('.invalid', this).length) {
        return false;
      };

       username = $(form).find('#user_login').val();
       password = $(form).find('#user_pass').val();
       
       return login_user (username, password);
    });
    

    // $('#pl_login_form').bind('submit',function(e) {
    //     $this = $(this);
    //     username = $(this).find('#user_login').val();
    //     password = $(this).find('#user_pass').val();
    // 
    //     return login_user(username, password);
    // });
    
    if(typeof $.fancybox == 'function') {
        $(".pl_register_lead_link").fancybox({
            'hideOnContentClick': false,
            'scrolling' : true,
            onClosed : function () {
              $(".login-form-validator-error").remove();
            }
        });

        $(".pl_login_link").fancybox({
            'hideOnContentClick': false,
            'scrolling' : true,
            onClosed : function () {
              $(".login-form-validator-error").remove();
            }
            
        });

        $(document).ajaxStop(function() {
            favorites_link_signup();
        });
    }
    

    favorites_link_signup();

    function favorites_link_signup () {
        if(typeof $.fancybox == 'function') {
            $('.pl_register_lead_favorites_link').fancybox({
              'hideOnContentClick': false,
              'scrolling' : true
            }); 
        }
    }
    
    function login_user (username, password) {
         
       data = {
           action: 'pl_login',
           username: username,
           password: password
       };

       var success = false;

       $.ajax({
           url: info.ajaxurl, 
           data: data, 
           async: false,
           type: "POST",
           success: function(response) {
             console.log(response);
               // If request successfull empty the form
               if ( response == '"You have successfully logged in."' ) {
                 
                 // remove error messages
                 $('.login-form-validator-error').remove();
                 
                 // Remove form
                 $("#pl_login_form_inner_wrapper").slideUp();
                 
                 // Show success message
                 setTimeout(function() {
                   $("#pl_login_form .success").show('fast');
                 },500);
                 
                 success = true;
               } else {
                 // Error Handling
                 var errors = jQuery.parseJSON(response);

                 $('form#pl_login_form').validator();
                 $('form#pl_login_form input').data("validator").invalidate({'user_login':errors.user_login,'user_pass':errors.user_pass});
               }
           }
       });

       // allow page redirect of page on success
       if ( ! success ) {
          return false;
        } else {
          return true;
        }
    }
});