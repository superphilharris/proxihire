
var allMarkers = [];
var googleMap = null;
function addMapsMarker(lat, long, title, url){
	allMarkers.push(new google.maps.Marker({
		position: 	new google.maps.LatLng(lat, long),
		url: 		url,
		title: 		title
	}));
}
function showAllMarkers(){
	for(var i=0; i<allMarkers.length; i++) {
		allMarkers[i].setMap(googleMap);
	}
}
function removeAllMarkers(){
	for(var i=0; i<allMarkers.length; i++) {
		allMarkers[i].setMap(null);
	}
	allMarkers = [];
}

$(document).ready(function(){/* google maps -----------------------------------------------------*/
	google.maps.event.addDomListener(window, 'load', initialize);
	
	
	function initialize() {
	
	  /* position Amsterdam */
	  var latlng = new google.maps.LatLng(-36.84913134182603, 174.76234048604965);
	
	  var mapOptions = {
	    center: latlng,
	    scrollWheel: false,
	    mapTypeControl: false,
	    zoom: 13
	  };
	  
	  var marker = new google.maps.Marker({
	    position: latlng,
	    url: '/',
	    animation: google.maps.Animation.DROP
	  });
	  
	  googleMap = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
	  marker.setMap(googleMap);
	  showAllMarkers();
	};
	/* end google maps -----------------------------------------------------*/
});