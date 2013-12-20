jQuery(document).ready(function($) {

    /* 
     * Bindings for UI that generates the list of saved searches in the user's client profile... 
     */

    $('.pl_ss-remove-search').live('click', function (event) {
        event.preventDefault();
        
        var data = {
            action: 'delete_user_saved_search',
            search_hash: $(this).attr('href')
        };

        $.post(info.ajaxurl, data, function (response, textStatus, xhr) {
            // console.log(response);
            if (response && response.success === true) {
                $('.pl_saved-search--single#' + data.search_hash).remove();
            }
        }, 'json');
    });

    function toggleNotification(flag, elem) {
        var data = {
            action: 'toggle_search_notification',
            search_hash: $(elem).attr('href'),
            toggle_flag: flag
        };
        
        $.post(info.ajaxurl, data, function (response, textStatus, xhr) {
            // console.log(response);
            if (response && response.success === true) {
                if (flag) {
                    $(elem).attr('class', 'pl_ss-disable-notification');
                    $(elem).text('Disable Email Notification');
                }
                else {
                    $(elem).attr('class', 'pl_ss-enable-notification');
                    $(elem).text('Enable Email Notification');
                }
            }
        }, 'json');
    }

    $('.pl_ss-enable-notification').live('click', function (event) {
        event.preventDefault();

        // Enable an e-mail notification for the given saved search...
        toggleNotification(true, this);
    });

    $('.pl_ss-disable-notification').live('click', function (event) {
        event.preventDefault();

        // Disable an e-mail notification for the given saved search...
        toggleNotification(false, this);
    });

});