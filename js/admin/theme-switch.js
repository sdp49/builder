function ask_user_about_dummy_data () {
  jQuery("#theme_change_dummy_data_box").dialog("open");
}

function theme_switch_notifier () {
  jQuery("theme_change_dialog_box").dialog("open");
}

jQuery(window).load(function () { ask_user_about_dummy_data(); });

jQuery(document).ready(function($) {

  $("#theme_change_dummy_data_box").dialog({
    dialogClass: "theme_change_dummy_data_box",
    autoOpen: false,
    title: "Want dummy data?",
    resizable: false,
    height: 250,
    width: 500,
    modal: true,
    closeText: false,
    zIndex: 999999,
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
          $(".ui-dialog-buttonpane .green-btn").before('<div id="loading-spinner" style="z-index:9999999;margin:0 0 0 350px;"><div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div></div>');
          $.post(ajaxurl, {action: "add_dummy_data"}, function(data, textStatus, xhr) {
            $("#theme_change_dummy_data_box").dialog("option", "hide", "fade").dialog("close");
            theme_switch_notifier();
          }, "json");
        }
      }
    ]
  });
  
  $("#theme_change_dialog_box").dialog({
    dialogClass: "theme_change_dialog_box",
    title: "Theme Switched!",
    autoOpen: false,
    resizable: false,
    height: 250,
    width: 500,
    modal: true,
    zIndex: 999999,
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
  
});