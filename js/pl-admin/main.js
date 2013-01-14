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

  // Define important single-instance elements referenced throughout the script...
  var iframe = $('#main-iframe');
  var loader = $('#pls-inner-bot .loader');
  var pane = $('#pls-pane');

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

    // Check to see if new pane size is defined -- use default value if not...
    newPaneSize = ( (typeof newPaneSize === 'undefined') ? paneSizes[0] : newPaneSize );

    // Remove any exiting pane sizing classes...
    for ( var i = 0; i < paneSizes.length; ++i ) {
      pane.removeClass(paneSizes[i]);
    }

    // Add back paneAttr as new pane size class...
    pane.addClass(newPaneSize); 
  }

  function displayCardGroup (cardGrpID) {
    // Construct card group selector...
    var cardGrpSelector = '#card-group-' + cardGrpID;

    // Make sure pane is visible...
    pane.show();

    // Hide any other card groups
    pane.find('.card-group').hide();

    // Update pane size (if needed), then show the one passed...
    updatePaneSize(cardGrpSelector, true);
    $(cardGrpSelector).show();
  }

  function displayCard (cardGrpID, cardID) {
    // Construct DOM selector of the card to display...
    var cardSelector = '#' + cardGrpID + ' .card-body #' + cardID;
    var cardElem = pane.find(cardSelector);

    // Update card number in card-group nav...
    var cardNum = cardElem.attr('card-num');
    pane.find('#' + cardGrpID + ' .card-nav .curr-card-num').text(cardNum);

    // Hide all other active cards...
    cardElem.siblings('.active').removeClass('active');

    // Update pane size (if needed), then show this card...
    updatePaneSize(cardSelector, false);
    cardElem.addClass('active');
  }

/*
 * Bind/Register Events
 */

  // Register a function to make the spinner disappear every time the content iframe is reloaded...
  pl_admin_global.contentLoadedHandlers.push(function () {
    loader.hide();
  });

  // Bind content refresh button...
  $('#refresh-content').on('click', function (event) {
    event.preventDefault();

    // Display loading spinner...
    loader.show();
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
  pane.find('.pls-close').on('click', function (event) {
    event.preventDefault();
    pane.hide();
  });

  
  // --------------- //
  // Cards (Generic) //
  // --------------- //

  // Card Navigation
  pane.find('.pls-right .bullet').on('click', function (event) {
    event.preventDefault();

    // If already selected, don't bother...
    if ( $(this).hasClass('on') ) { return; }

    // Turn all other bullets off..
    $(this).siblings('.bullet').removeClass('on');
    $(this).siblings('.bullet').addClass('off');
    
    // Turn this one on...
    $(this).removeClass('off');
    $(this).addClass('on');

    var cardGrpID = $(this).parentsUntil('#' + pane.attr('id'), '.card-group').attr('id');
    displayCard(cardGrpID, $(this).attr('href'));
  });


  // -------------- //
  // Cards (Custom) //
  // -------------- //

  /* Theme Functionality */

  // Elements that are referred to frequently... (defined up here for easy DOM structure changes)
  var themeSelect = $('#pls-theme-select');
  var themeSubmit = $('#pls-theme-submit');
  var themeDesc = $'#pls-theme-desc');
  var themeImg = $('#pls-theme-img');
  var themePaginate = $('#pls-numbers');

  // Logic to determine whether to hide or show pagination buttons based on change...
  function paginationHideShow (oldIdx, newIdx, maxIdx) {
    var prev = themePaginate.find('.first');
    var next = themePaginate.find('.last');
    
    // Handle previous...
    if ( oldIdx == 0) { prev.css('visibility', 'visible'); } 
    else if ( newIdx == 0 ) { prev.css('visibility', 'hidden'); }
    else { /* No action necessary...*/ }

    // Handle next...
    if ( oldIdx == maxIdx ) { next.css('visibility', 'visible'); }
    else if ( newIdx == maxIdx ) { next.css('visibility', 'hidden'); }
    else { /* No action necessary...*/ }        
  }

  function initPagination () {
    if ( themeSelect.length > 0 ) {
      var newInd = themeSelect.get(0).selectedIndex; // Current index is "new" index when initially setting this...
      var maxInd = ( themeSelect.get(0).options.length - 1 );
      paginationHideShow( -1, newInd, maxInd ); // "old" index is set to -1 so it's value won't cause any changes...
    }
  }

  function activateTheme () {
    // Show spinner to indicate theme activation is in progress...
    loader.show();

    var submitElem = $('#' + submitID);
    submitElem.attr('disabled', 'disabled');
    submitElem.addClass('bt-disabled');

    var data = { action: 'change_theme', new_theme: $('#' + selectID).val() };
    $.post(ajaxurl, data, function (response) {
      if ( response && response.success ) {
          // Reload content iframe to display new theme...
          refreshContent(true);
      }
      else {
        loader.hide();

        submitElem.removeAttr('disabled');
        submitElem.removeClass('bt-disabled');            
      }
    },'json');
  }

  function valPremTheme (container) {
    // Show spinner to indicate theme premium theme validation is in progress...
    loader.show();

    // Set success and failure callbacks...
    var success_callback = function () { activateTheme(); }
    var failure_callback = function () {
      // Construct error message...
      var msg = '<h3>Sorry, your account isn\'t eligible to use Premium themes.</h3>';
        msg += '<h3>Please <a href="https://placester.com/subscription">Upgrade Your Account</a> or call us with any questions at (800) 728-8391.</h3>';

      container.prepend('<div id="message" class="error">' + msg + '</div>');
    }

    // Check user's subscription status and act accordingly...
    $.post(ajaxurl, {action: 'subscriptions'}, function (response) {
      // console.log(response);
      if (response && response.plan && response.plan == 'pro') {
        success_callback();
      } 
      else if (response && response.eligible_for_trial) {
        // console.log('prompt free trial');
        prompt_free_trial('Start your 15 day Free Trial to Activate a Premium Theme', success_callback, failure_callback);
      } 
      else {
        failure_callback();
      };
    },'json');  
  }

  // On initial page load, hide/show the pagination buttons accordingly...
  initPagination();

  $('#theme_choices').on('change', function (event) {
    // Remove any latent error messages if they exist...
    $('#theme_content ul.control-list').find('#message.error').remove();

    // If theme selected is set to current one, set the submit button to disabled, otherwise enable it
    var submitElem = $('#submit_theme');
    if ( _wpCustomizeSettings && _wpCustomizeSettings.theme.stylesheet == $(this).val() ) {
      submitElem.attr('disabled', 'disabled');
      submitElem.addClass('bt-disabled');
    }
    else {
    // Might not be necessary--done to handle all cases properly
      submitElem.removeAttr('disabled');
      submitElem.removeClass('bt-disabled');
    }

    loader.show();
    data = { action: 'load_theme_info', theme: $(this).val() };

    $.post(ajaxurl, data, function (response) {
      if ( response ) {
        // Alter theme_info elem with new info...
        var infoElem = $('#theme_info');
        infoElem.find('#pls-theme-img').attr('src', response.screenshot);
        infoElem.find('#pls-theme-desc').html(response.description)
    
        // Reset pagination button(s) to match newly selected theme...
        $('#pagination a').css('visibility', 'visible');
        initPagination();
      }
      loader.hide();
    },'json');

  });

  $('#submit_theme').on('click', function (event) {
    var container = $('#theme_content ul.control-list');

    // Remove any latent error messages if they exist...
    container.find('#message.error').remove();

    // Check if user is trying to activate a Premium theme, and act accordingly...
    var type = $('option:selected').parent().attr('label');
    if ( type === 'Premium' ) { 
      valPremTheme(container); 
    }
    else {
      activateTheme();
    }
  });

  // Handles "Previous" and "Next" pagination buttons...
  $('#pagination a').on('click', function (event) {
    event.preventDefault();

    var type = $(this).attr('class');
    var selectElem = $('#theme_choices').get(0);
    var maxIndex = (selectElem.options.length - 1);
    var currIndex = selectElem.selectedIndex;
    var newIndex;

    // Handle each type accordingly
    if ( type === 'prev' ) {
      newIndex = (currIndex - 1);
    }
    else if ( type == 'next' ) {
      newIndex = (currIndex + 1);
    }
    else {
      console.log('Pagination button of type "' + type + '"not handled');
      return;
    }

    // Validate new index
    if ( newIndex < 0 || newIndex > maxIndex ) { 
      console.log('Index out of bounds...reverting'); 
      return;
    }

    // Set selected theme to new index... 
    selectElem.selectedIndex = newIndex;
    $('#theme_choices').trigger('change');
  });

});

