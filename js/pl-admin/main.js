/*
 * Global Definitions
 */

// AJAX Endpoint
origin = ( window.location.origin ) ? window.location.origin : ( window.location.protocol + "//" + window.location.host );
ajaxurl = origin + '/wp-admin/admin-ajax.php';

// This global variable must be defined in order to conditionally prevent iframes from being
// automatically "busted" when in the hosted environment... (see hosted-modifications plugin)
pl_admin_global = {
  contentLoadedHandlers: [], // Fill with functions you want executed iframe loads...
  contentLoaded: function () {
    var handlers = this.contentLoadedHandlers;
    for ( var i = 0; i < handlers.length; ++i ) {
      // Call each handler...
      handlers[i]();
    }
  }
};

/*
 * Main JS
 */

jQuery(document).ready(function($) {

  /*
   * Define Vars & Functions
   */

  // Define iframe
  var iframe = $('#main-iframe');

  // Trigger content iFrame refresh
  function refreshContent (boolFromServer) {
    var id = iframe.attr('id');
    window.frames[id].location.reload(boolFromServer);
  } 

  // Alter breadcrumbs to reflect a change in the iframe content...
  // function alterBreadCrumbs (newURL) {
  //  var crumbs = 
  // }

  /*
   * Bind/Register Events
   */

  // Intercept page section clicks and alter content iframe accordingly...
  $('#utilitiesNav li a').on('click', function (event) {
    event.preventDefault();

    // Construct and set new content url...
    var url = this.href + '?content=true';
    // console.log(url);
    iframe.attr('src', url);

    // alterBreadCrumbs(this.href);
  });

  // First register a function to make the spinner disappear...
  pl_admin_global.contentLoadedHandlers.push(function () {
    $('#pls-inner-bot .loader').hide();
  });

  // Bind content refresh button...
  $('#refresh-content').on('click', function (event) {
    event.preventDefault();

    // Display loading spinner...
    $('#pls-inner-bot .loader').show();

    refreshContent(true);
  });

  // Bind "Ubiquitous" (i.e., bottom left) sections...
  $('#settingsNav a').on('click', function (event) {
    event.preventDefault();

    // Show related card-group in pane...
    var cardGrpID = '#card-group-' + $(this).attr('class');
    console.log(cardGrpID);
    $(cardGrpID).show()

  });

});

