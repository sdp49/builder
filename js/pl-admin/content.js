/*
 * JS for content iframe/container
 */

jQuery(document).ready(function($) {

  $('#main-iframe').load(function() {
    // Call handler to alert the parent that loading is finished...
    pl_admin_global.contentLoaded();

    // Define the iframe document variable...
    var iframe = this;
    var iframeDOM = $(this).contents().contents();

    // Append the escape arg to any links clicked from the iframe...
    iframeDOM.find('a').on('click', function (event) {
  	  event.preventDefault();

  	  // Get original href of the link clicked...
  	  var aHref = this.href;

  	  // Depending on whether there are existing query args, append accordingly...
  	  escapeArg = ( aHref.indexOf('?') !== -1 ? '&' : '?' ) + 'content=true';

  	  var newHref = aHref + escapeArg;
  	  // console.log(newHref);
  	  // console.log(iframe);
  	  iframe.src = newHref;
    });
  });

});