$(document).ready(function($) {

  var googleMapsURL = 'https://maps.googleapis.com/maps/api/js?sensor=false';
  var geocoder;
  var map;

  function loadGoogleMapsAPI () {
  	$.getScript(googleMapsURL, initializeMap());
  }

  function initializeMap () {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var mapOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map($('#map_canvas'), mapOptions);

    var address = $('#demo_zip').val();

    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        alert('Geocode was not successful for the following reason: ' + status);
      }
    });
  }

  var demo_buttons = {
		1 : {
			text: "No, Thanks",
			click: function() {
				 $( this ).dialog( "close" );
			}
		},
		2 : {
			text: "Confirm",
			id: 'confirm_demo_button',
			click: function() {
				 $('#pls_integration_form').trigger('submit');
			}
		}
	}

	$( "#demo_data_wizard" ).dialog({
		autoOpen: false,
		draggable: false,
		modal: true,
		title: '<h3>Test-drive your Site with Demo Listings</h3>',
		width: 500,
		buttons: demo_buttons
	});

});