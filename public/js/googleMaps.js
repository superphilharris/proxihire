/**
 * @see http://stackoverflow.com/questions/979975/how-to-get-the-value-from-the-url-parameter
 */ 
var QueryString = function () {
	// This function is anonymous, is executed immediately and 
	// the return value is assigned to QueryString!
	var query_string = {};
	try{
		var query = decodeURIComponent(window.location.search.substring(1));
		query_string = $.parseJSON(query)
	} catch(e){
		
	}
	if(typeof(query_string.location) == "undefined") query_string.location = { latitude: {}, longitude: {} };
	return query_string;
}();

var CURRENT_LOCATION = { latitude: { user: -36.84913134182603 }, longitude: { user: 174.76234048604965 } }; // Default to Auckland
if(QueryString.location.latitude.user && QueryString.location.longitude.user){
	CURRENT_LOCATION.latitude.user 		= QueryString.location.latitude.user;
	CURRENT_LOCATION.longitude.user 	= QueryString.location.longitude.user;
}
var showGoogleMap = (window.innerWidth >= 768);
var allMarkers = [];
var googleMap = null, mapsBouncingTimout = null;
var userMarker = null;
function showAllMarkers(){
	if(googleMap == null || typeof allBranches == "undefined") return;
	for(var i in allBranches){
		if(typeof allMarkers[allBranches[i].lessorId] == "undefined"){
			allMarkers[allBranches[i].lessorId] = [];
		}
		allMarkers[allBranches[i].lessorId].push(new google.maps.Marker({
			position: 	new google.maps.LatLng(allBranches[i].lat, allBranches[i].long),
			title: 		allBranches[i].lessorName + '',
			icon: 		allBranches[i].icon
		}));
	}
	for(var lessorId in allMarkers) {
		for(var i in allMarkers[lessorId]){
			allMarkers[lessorId][i].setMap(googleMap)
		}
	}
}
function removeAllMarkers(){
	if(googleMap == null) return;
	for(var lessorId in allMarkers) {
		for(var i in allMarkers[lessorId]){
			allMarkers[lessorId][i].setMap(null)
		}
	}
	allMarkers = [];
	allBranches = [];
}
function bounceMarker(lessorId){
	if(googleMap == null) return;
	for(var eachLessorId in allMarkers){
		for(var i=0; i<allMarkers[eachLessorId].length; i++) {
			if (eachLessorId == lessorId){
				allMarkers[eachLessorId][i].setAnimation(google.maps.Animation.BOUNCE);
				stopBouncing(eachLessorId, 2);
			} else {
				allMarkers[eachLessorId][i].setAnimation(null);
			}
		}
	}
}
function stopBouncing(lessorId, seconds){
	if(googleMap == null) return;
	clearTimeout(mapsBouncingTimout);
	mapsBouncingTimout = setTimeout(function(){
		if(allMarkers[lessorId]){
			for(var i=0; i<allMarkers[lessorId].length; i++) {
				allMarkers[lessorId][i].setAnimation(null);
			}
		}
	}, seconds * 1000)
}
function goLocation(lat, long){
	CURRENT_LOCATION.latitude.user = lat;
	CURRENT_LOCATION.longitude.user = long;
	goCategory();
}
function goLocationAndChangeGoogleMaps(lat, long){
	goLocation(lat, long);
	if(googleMap){
		var latlng = new google.maps.LatLng(lat, long);
		googleMap.setCenter(latlng);
		userMarker.setPosition(latlng);
	}
}

function initializeGoogleMaps() {
	google.maps.event.addDomListener(window, 'load', function(){
		var latlng = new google.maps.LatLng(CURRENT_LOCATION.latitude.user, CURRENT_LOCATION.longitude.user);
		var mapOptions = {
	    	center: latlng,
	    	scrollWheel: false,
	    	mapTypeControl: false,
	    	zoom: 11
	  	};
	  
	  	googleMap = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

		userMarker = new google.maps.Marker({
			map: googleMap,
			draggable: true,
			position: new google.maps.LatLng(CURRENT_LOCATION.latitude.user, CURRENT_LOCATION.longitude.user)
		});
		google.maps.event.addListener(userMarker, 'dragend', function(){
			goLocation(this.position.lat(), this.position.lng());
		});
		googleMap.addListener('zoom_changed', googleMapsChangedBounds);
		googleMap.addListener('dragend', googleMapsChangedBounds);
	  	showAllMarkers();
	});
};
var googleMapsChangedBoundsTimeout = null;
function googleMapsChangedBounds(){
	setGoogleMapsBoundsAndClearTimeout();
	googleMapsChangedBoundsTimeout = setTimeout(goLocation, 2000);
}
function setGoogleMapsBoundsAndClearTimeout(){
	if(googleMap){
		clearTimeout(googleMapsChangedBoundsTimeout);
		CURRENT_LOCATION.latitude.max 	= googleMap.getBounds().getNorthEast().lat();
		CURRENT_LOCATION.latitude.min 	= googleMap.getBounds().getSouthWest().lat();
		CURRENT_LOCATION.longitude.max 	= googleMap.getBounds().getNorthEast().lng();
		CURRENT_LOCATION.longitude.min 	= googleMap.getBounds().getSouthWest().lng();
	}
}

/*
 * --------------------------------------------------------------------------------------------
 * Get the user location
 */
function getUserLocation(){
	navigator.geolocation.getCurrentPosition(function(position){
		goLocationAndChangeGoogleMaps(position.coords.latitude, position.coords.longitude);
	});
}

if(showGoogleMap){
	$.getScript('http://maps.googleapis.com/maps/api/js?sensor=false&extension=.js&output=embed&callback=initializeGoogleMaps');
}
