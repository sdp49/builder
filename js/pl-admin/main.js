/*
 * Global Definitions
 */

// Usually defined by WordPress, but not in the customizer...
var ajaxurl = ( window.location.origin ) ? window.location.origin : ( window.location.protocol + "//" + window.location.host );
ajaxurl += '/wp-admin/admin-ajax.php';

// This global variable must be defined in order to conditionally prevent iframes from being
// automatically "busted" when in the hosted environment... (see hosted-modifications plugin)
pl_admin_global = {};

/*
 * Main JS
 */

jQuery(document).ready(function($) {

  $('#main-iframe').load(function() {
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

  	  console.log(newHref);
  	  console.log(iframe);

  	  iframe.src = newHref;
    });
  });

});

