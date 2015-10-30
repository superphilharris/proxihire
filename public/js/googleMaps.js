/**
 * @see http://stackoverflow.com/questions/979975/how-to-get-the-value-from-the-url-parameter
 */ 
var QueryString = function () {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
        // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = decodeURIComponent(pair[1]);
        // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
      query_string[pair[0]] = arr;
        // If third or later entry with this name
    } else {
      query_string[pair[0]].push(decodeURIComponent(pair[1]));
    }
  } 
    return query_string;
}();

var CURRENT_LOCATION = { lat: -36.84913134182603, long: 174.76234048604965 };
if(QueryString.lat && QueryString.long){
	CURRENT_LOCATION.lat = QueryString.lat;
	CURRENT_LOCATION.long = QueryString.long;
}
var showGoogleMap = (window.innerWidth >= 768);
var allMarkers = [], allAssets = [], allBranches = [];
var googleMap = null, mapsBouncingTimout = null;
var userMarker = null;
function showAllMarkers(){
	if(googleMap == null) return;
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
	for(var i=0; i<allMarkers.length; i++) {
		allMarkers[i].setMap(null);
	}
	allMarkers = [];
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
		for(var i=0; i<allMarkers[lessorId].length; i++) {
			allMarkers[lessorId][i].setAnimation(null);
		}
	}, seconds * 1000)
}

function initializeGoogleMaps() {
	google.maps.event.addDomListener(window, 'load', function(){
		var latlng = new google.maps.LatLng(CURRENT_LOCATION.lat, CURRENT_LOCATION.long);
		var mapOptions = {
	    	center: latlng,
	    	scrollWheel: false,
	    	mapTypeControl: false,
	    	zoom: 13
	  	};
	  
	  	googleMap = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

		userMarker = new google.maps.Marker({
			map: googleMap,
			draggable: true,
			position: new google.maps.LatLng(CURRENT_LOCATION.lat, CURRENT_LOCATION.long)
		});
	  	showAllMarkers();
	});
};

if(showGoogleMap){
	$.getScript('http://maps.googleapis.com/maps/api/js?sensor=false&extension=.js&output=embed&callback=initializeGoogleMaps');
}
