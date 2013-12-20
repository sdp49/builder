jQuery(document).ready(function($) {

    /* 
     * Bindings for UI that generates the list of saved searches in the user's client profile... 
     */

    $('.pl_ss-remove-search').live('click', function (event) {
        event.preventDefault();
        
        var data = {
            action: 'delete_user_saved_search',
            search_hash: $(this).attr('id')
        };
        console.log(data);

        $.post(info.ajaxurl, data, function (response, textStatus, xhr) {
            console.log(response);
            if (response && response.success === true) {
                $('.saved_search_block#' + data.search_hash).remove();
            }
        }, 'json');
    });

    function toggleNotification(flag) {
        var data = {
            action: 'toggle_search_notification',
            toggle_flag: flag
        };

        var that = this;
        $.post(info.ajaxurl, data, function (response, textStatus, xhr) {
            // console.log(response);
            if (response && response.success === true) {
                if (data.toggle_flag) {
                    $(that).attr('class', 'pls_disable_notification');
                    $(that).text('Disable Email Notification');
                }
                else {
                    $(that).attr('class', 'pls_enable_notification');
                    $(that).text('Enable Email Notification');
                }
            }
        }, 'json');
    }

    $('.pl_ss-enable-notification').live('click', function (event) {
        event.preventDefault();

        // Enable an e-mail notification for the given saved search...
        toggleNotification(true);
    });

    $('.pl_ss-disable-notification').live('click', function (event) {
        event.preventDefault();

        // Disable an e-mail notification for the given saved search...
        toggleNotification(false);
    });

});