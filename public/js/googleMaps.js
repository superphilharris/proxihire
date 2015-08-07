
var showGoogleMap = (window.innerWidth > 768);
var allMarkers = [], allAssets = [];
var googleMap = null, mapsBouncingTimout = null;
function showAllMarkers(){
	if(googleMap == null) return;
	for(var i in allAssets){
		allMarkers.push(new google.maps.Marker({
			position: 	new google.maps.LatLng(allAssets[i].lat, allAssets[i].long),
			url: 		allAssets[i].url,
			title: 		allAssets[i].id + ''
		}));
	}
	for(var i=0; i<allMarkers.length; i++) {
		allMarkers[i].setMap(googleMap);
	}
}
function removeAllMarkers(){
	if(googleMap == null) return;
	for(var i=0; i<allMarkers.length; i++) {
		allMarkers[i].setMap(null);
	}
	allMarkers = [];
}
function bounceMarker(url){
	if(googleMap == null) return;
	for(var i=0; i<allMarkers.length; i++) {
		if(url == allMarkers[i].url) {
			allMarkers[i].setAnimation(google.maps.Animation.BOUNCE);
			stopBouncing(url, 2);
		} else {
			allMarkers[i].setAnimation(null);
		}
	}
}
function stopBouncing(url, seconds){
	if(googleMap == null) return;
	clearTimeout(mapsBouncingTimout);
	mapsBouncingTimout = setTimeout(function(){
		for(var i=0; i<allMarkers.length; i++) {
			if(url == allMarkers[i].url) {
				allMarkers[i].setAnimation(null);
			}
		}
	}, seconds * 1000)
}

function initializeGoogleMaps() {
	google.maps.event.addDomListener(window, 'load', function(){
		var latlng = new google.maps.LatLng(-36.84913134182603, 174.76234048604965);
		var mapOptions = {
	    	center: latlng,
	    	scrollWheel: false,
	    	mapTypeControl: false,
	    	zoom: 13
	  	};
	  
	  	googleMap = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
	  	showAllMarkers();
	});
};

if(showGoogleMap){
	$.getScript('http://maps.googleapis.com/maps/api/js?sensor=false&extension=.js&output=embed&callback=initializeGoogleMaps');
}
