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

  function displayCard (cardGrpID, cardID) {
    // Construct DOM selector of the card to display...
    var cardSelector = '#pls-pane #' + cardGrpID + ' .card-body #' + cardID;
    console.log(cardSelector);
    
    // Hide all other cards...
    $(cardSelector).siblings().hide();

    // Show this card...
    $(cardSelector).show();
  }

/*
 * Bind/Register Events
 */

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

  
  // --------------- //
  // Navs & Sections //
  // --------------- //

  // Intercept page section clicks and alter content iframe accordingly...
  $('#utilitiesNav li a').on('click', function (event) {
    event.preventDefault();

    // Construct and set new content url...
    var url = this.href + '?content=true';
    // console.log(url);
    iframe.attr('src', url);

    // alterBreadCrumbs(this.href);
  });

  // Bind "Ubiquitous" (i.e., bottom left) sections...
  $('#settingsNav a').on('click', function (event) {
    event.preventDefault();

    // Show related card-group in pane...
    var cardGrpID = '#card-group-' + $(this).attr('class');
    // console.log(cardGrpID);
    $('#pls-pane').show();
    $(cardGrpID).show();
  });

  // Close pane...
  $('#pls-pane .pls-close').on('click', function (event) {
    event.preventDefault();
    $('#pls-pane').hide();
  });

  
  // --------------- //
  // Cards (Generic) //
  // --------------- //

  // Card Navigation
  $('#pls-pane .pls-right .bullet').on('click', function (event) {
    event.preventDefault();

    // If already selected, don't bother...
    if ( $(this).hasClass('on') ) { return; }

    // Turn all other bullets off..
    $(this).siblings('.bullet').removeClass('on');
    $(this).siblings('.bullet').addClass('off');
    
    // Turn this one on...
    $(this).removeClass('off');
    $(this).addClass('on');

    var cardGrpID = $(this).parentsUntil('#pls-pane', '.card-group').attr('id');
    displayCard(cardGrpID, this.href);
  });

});

