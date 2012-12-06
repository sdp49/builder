jQuery(document).ready(function($) {

  console.log("changing theme");

  theme_switch_notifier();
  
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