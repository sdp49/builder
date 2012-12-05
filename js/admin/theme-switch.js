jQuery(document).ready(function($) {

  tell_user_theme_changed_with_consequences();

  function tell_user_theme_changed_with_consequences () {
    console.log("telling user function firing");
    var dialog_box = '<div style="display:none;" id="theme_change_dialog_box">AHHHH!<br/><br/><br/><br/>AHAHHAH</div>';
    $("body").prepend(dialog_box);
    
    $("#theme_change_dialog_box").dialog();
  }
  
  
});