/*
 * Global Definitions
 */

// AJAX Endpoint
origin = ( window.location.origin ) ? window.location.origin : ( window.location.protocol + "//" + window.location.host );
ajaxurl = origin + '/wp-admin/admin-ajax.php';

// This global variable must be defined in order to conditionally prevent iframes from being
// automatically "busted" when in the hosted environment... (see hosted-modifications plugin)
pl_admin_global = {};

/*
 * Main JS
 */

jQuery(document).ready(function($) {

  // Define iframe
  var iframe = $('#main-iframe');

  // Intercept page section clicks and alter content iframe accordingly...
  $('#utilitiesNav li a').on('click', function (event) {
    event.preventDefault();

    // Construct and set new content url...
    var url = this.href + '?content=true';
    // console.log(url);
    iframe.attr('src', url);

    // alterBreadCrumbs(this.href);
  });

  // Alter breadcrumbs to reflect a change in the iframe content...
  function alterBreadCrumbs (newURL) {
  		
  }


});

