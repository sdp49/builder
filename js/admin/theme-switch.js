jQuery(document).ready(function($) {

  ask_user_about_dummy_data();
  
  var dummy_dialog_data_box;
  
  function ask_user_about_dummy_data () {
      var dummy_data_box = '<div style="display:none;" id="theme_change_dummy_data_box">';
      dummy_data_box += "<p>You've switched themes. It's possible this theme has different default pages and menus as the last theme.</p>";
      dummy_data_box += '<h3>Would you like us to set those up for you?</h3>';
      dummy_data_box += '<p>(none of your posts/pages/menus will be deleted)</p>';
      dummy_data_box += '</div>';

      $("body").prepend(dummy_data_box);

      dummy_dialog_data_box = $("#theme_change_dummy_data_box").dialog({
        dialogClass: "theme_change_dummy_data_box",
        title: "Want dummy data?",
        // resizable: false,
        height: 250,
        width: 500,
        modal: true,
        closeText: false,
        buttons: [
          {
            text: "No",
            class: "gray-btn",
            click: function() {
              $(this).dialog("close");
            }
          },
          {
            text: "Yes",
            class: "green-btn right-btn",
            click: function() {
              $(".ui-dialog-buttonpane .green-btn").before('<div id="loading-spinner" style="margin:0 0 0 350px;"><div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div></div>');
              $.post(ajaxurl, {action: "add_dummy_data"}, function(data, textStatus, xhr) {
                $(dummy_dialog_data_box).dialog("option", "hide", "fade").dialog("close");
                theme_switch_notifier();
              }, "json");
            }
          }
        ]
      });
    }


  function theme_switch_notifier () {

    var dialog_box = '<div style="display:none;" id="theme_change_dialog_box">';
    dialog_box += "<p>You've switched themes. It's possible this theme has different default pages and menus as the last theme so we've set those up for you.</p>";
    dialog_box += '<p>The buttons below are shortcuts if you want to change menus or pages.</p>';
    dialog_box += '</div>';

    $("body").prepend(dialog_box);

    $("#theme_change_dialog_box").dialog({
      title: "Theme Switched!",
      resizable: false,
      height: 250,
      width: 500,
      modal: true,
      buttons: [
        {
          text: "Change Menus",
          class: "gray-btn",
          click: function() {
            $(this).dialog("close");
            window.open( window.location.origin + "/wp-admin/nav-menus.php" );
          }
        },
        {
          text: "Change Pages",
          class: "gray-btn",
          click: function() {
            $(this).dialog("close");
            window.open( window.location.origin + "/wp-admin/edit.php?post_type=page" );
          }
        },
        {
          text: "Thanks! All set.",
          class: "green-btn right-btn",
          click: function() {
            $(this).dialog("close");
            window.open( window.location.origin + "/wp-admin/nav-menus.php" );
          }
        }
        
      ]
    });
  }
  
});