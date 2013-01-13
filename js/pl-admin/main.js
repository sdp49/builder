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


jQuery(document).ready(function($) {

/*
 * Define Vars & Functions
 */

  // Define iframe
  var iframe = $('#main-iframe');

  // Define pane classes
  var paneSizes = ['pls-small', 'pls-medium', 'pls-tall', 'pls-full'];

  // Trigger content iFrame refresh
  function refreshContent (boolFromServer) {
    var id = iframe.attr('id');
    window.frames[id].location.reload(boolFromServer);
  } 

  // Alter breadcrumbs to reflect a change in the iframe content...
  // function alterBreadCrumbs (newURL) {
  //  var crumbs = 
  // }

  function updatePaneSize (selector, isGrp) {
    // If selector is for group, find selector of active card...
    var cardElem = ( isGrp ? $(selector).find('.active') : $(selector) );
    var newPaneSize = cardElem.attr('pane');

    console.log(selector);
    console.log(cardElem);
    console.log(newPaneSize);

    // Check to see if new pane size is defined -- use default value if not...
    newPaneSize = ( (typeof newPaneSize === 'undefined') ? paneSizes[0] : newPaneSize );

    var paneElem = $('#pls-pane');
    // Remove any exiting pane sizing classes...
    for ( var i = 0; i < paneSizes.length; ++i ) {
      paneElem.removeClass(paneSizes[i]);
    }

    // Add back paneAttr as new pane size class...
    paneElem.addClass(newPaneSize); 
  }

  function displayCardGroup (cardGrpID) {
    // Construct card group selector...
    var cardGrpSelector = '#card-group-' + cardGrpID;

    // Make sure pane is visible...
    $('#pls-pane').show();

    // Hide any other card groups
    $('#pls-pane .card-group').hide();

    // Update pane size (if needed), then show the one passed...
    updatePaneSize(cardGrpSelector, true);
    $(cardGrpSelector).show();
  }

  function displayCard (cardGrpID, cardID) {
    // Construct DOM selector of the card to display...
    var cardSelector = '#pls-pane #' + cardGrpID + ' .card-body #' + cardID;
    
    // Hide all other active cards...
    $(cardSelector).siblings('.active').removeClass('active');

    // Update pane size (if needed), then show this card...
    updatePaneSize(cardSelector, false);
    $(cardSelector).addClass('active');
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
    displayCardGroup($(this).attr('class'));
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
    displayCard(cardGrpID, $(this).attr('href'));
  });

});

