// get current script
var scripts = document.getElementsByTagName( 'script' );
var thisScriptTag = scripts[ scripts.length - 1 ];

var wp_index = thisScriptTag.src.indexOf('wp-content/');
var wp_folder = thisScriptTag.src.substring(0, wp_index);
var action_url = wp_folder + 'wp-admin/admin-ajax.php';


// get url vars
var url_vars = getUrlVars( thisScriptTag.src );
var url_json = JSON.stringify( url_vars );

window.addEventListener('load', function( ) {
	var script = document.createElement('script');
	script.src = action_url + '?action=handlewidgetscript&callback=callback';
	
	for( var argument in url_vars ) {
		script.src += '&' + argument + '=' + url_vars[argument];
	}
	
	document.documentElement.getElementsByTagName('head')[0].appendChild( script );

});

// Get response from the handle_script_insertion_cross_domain() PHP function and prepare the iframe
function callback( json ) {
    console.log(json);
}

	
function getUrlVars( path ) { // Read a page's GET URL variables and return them as an associative array.
	if( path.indexOf('?') === -1 ) {
		return {};
	}
    var vars = {},
        hash;
    var hashes = path.slice(path.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars[hash[0]] = hash[1];
    }
    return vars;
}
