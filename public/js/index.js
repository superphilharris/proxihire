if(typeof CURRENT_CATEGORY == "undefined") CURRENT_CATEGORY = "";
/**
 * Takes in a category and returns the list of sub-categories
 */
function linearize(category){
	var returnList = [];
	if(category.aliases){
		for(var i in category.aliases){
			returnList[returnList.length] = category.aliases[i]
		}
	}
	if(category.children){
		for(var i in category.children){
			returnList = returnList.concat(linearize(category.children[i]))
		}
	}
	return returnList;
}

function getMainProperties(){
	/* Find the:
	 * - total number of assets that use this property
	 * - if numeric, then get the min and max of this property
	 * - if non-numeric, then get all the possible values for this property
	 */
	var allProperties = [];
	var numberOfAssets = 0;
	$('.assetPropertiesSummary').each(function(){
		$(this).children('*').each(function(){
			var propertyName 	= $(this).find('.propertyName').text();
			var propertyValue 	= $(this).find('.propertyValue').text();
			if(typeof allProperties[propertyName] != "undefined"){
				allProperties[propertyName].count ++;
			}else{
				allProperties[propertyName] = { 
					count: 1,
					min: null,
					max: null,
					average: null,
					val: [],
					name: propertyName
				};
			}
			if($.isNumeric(propertyValue)){
				propertyValue = parseFloat(propertyValue);
				if(allProperties[propertyName].min == null){
					allProperties[propertyName].min 	= propertyValue;
					allProperties[propertyName].max 	= propertyValue;
					allProperties[propertyName].average = propertyValue;
				}else{
					allProperties[propertyName].min 	= Math.min(propertyValue, allProperties[propertyName].min);
					allProperties[propertyName].max 	= Math.max(propertyValue, allProperties[propertyName].max);
					allProperties[propertyName].average = allProperties[propertyName].average + (propertyValue - allProperties[propertyName].average)/allProperties[propertyName].count;
				}
			}
			if(allProperties[propertyName].val.indexOf(propertyValue) < 0){
				allProperties[propertyName].val.push(propertyValue);
			}
		});
		numberOfAssets += 1;
	});
	// Convert hashmap to sorted list by the commonality of the property
	var allPropertiesList = [];
	for(var i in allProperties) allPropertiesList.push(allProperties[i]);
	allPropertiesList.sort(function(a,b){  
		if(a.count != b.count) 					return b.count - a.count;				// Put preference first to the number of assets that match this property,
		else if(a.val.length != b.val.length)	return a.val.length - b.val.length;		// then to whether the property is more numeric
		else 									return a.name.length - b.name.length;	// then to a shorter property name length
	});

	// Return only the properties that 50% of the assets have, and if we can display them
	var widthOfBreadCrumb = Math.max(300, $('.categoryAndFilterBarWrapper .breadcrumb').width());
	var maxNumberOfMainProperties = Math.floor(($('.categoryAndFilterBarWrapper').width()-widthOfBreadCrumb-30-30)/120);
	var mainProperties = [];
	var i = 0;
	for(var j in allPropertiesList){
		if(i < maxNumberOfMainProperties && allPropertiesList[j].name != "" && allPropertiesList[j].count>=numberOfAssets/2){
			mainProperties[allPropertiesList[j].name] = allPropertiesList[j];
		}
		i++;
	}
	
	// If we are inserting in some property columns, then shrink the size of the existing columns. Or if we are mobile, then shrink the size of the properties to the page width
	if(Object.keys(mainProperties).length > 0 || window.innerWidth < 768){ 
		var widthOfExistingProperties = Math.min($('.categoryAndFilterBarWrapper').width() - 30 - 30 - 120 - 120 * maxNumberOfMainProperties, window.innerWidth - 120 - 30);
		$('.assetPropertiesSummaryWrapper').each(function(){
			$(this).width(widthOfExistingProperties+'px');
		});
	}else{
		$('.assetPropertiesSummaryWrapper').each(function(){
			$(this).width((window.innerWidth/3-120)+'px');
		});
	}
	return mainProperties;
}


/**
 * Replaces invalid characters from the property name to use as a css selector
 * @param propertyName
 * @returns
 */
function getCssPropertyName(propertyName){
	return propertyName.replace(/[^_a-zA-Z0-9-]/g, '_').toLowerCase();
}
function updateFilterBar(){
	var mainProperties = getMainProperties();
	var filterBar = $('.filterBar');
	var filterBarColumnNames 	= filterBar.find('.filterBarColumnNames');
	var showFilterBarIcon		= false;
	filterBarColumnNames.html('&nbsp;');
	
	// A. Add the filters at the top of the page
	for(var propertyName in mainProperties){
		// 1. Add the html
		var html = 
			'<div class="filterBarPropertyColumn"><div class="filterBarPropertyColumnName">'+propertyName+'</div>'+
				'<div class="filterBarColumnFilter '+getCssPropertyName(propertyName)+'_columnFilter">';
		if(mainProperties[propertyName].count > 1 && mainProperties[propertyName].val.length > 1){
			showFilterBarIcon = true;
			if(mainProperties[propertyName].max && mainProperties[propertyName].min) {
				html += '<input type="text" data-slider-orientation="vertical" name="'+propertyName+'" data-slider-min="'+mainProperties[propertyName].min+'" data-slider-max="'+mainProperties[propertyName].max+'"/>';
			}else{
				html += '<div class="btn-group-vertical" data-toggle="buttons">';
				for(var i in mainProperties[propertyName].val){
					html += '<label class="btn btn-default">'+
								'<input type="checkbox" name="'+propertyName+'" value="'+mainProperties[propertyName].val[i]+'">'+mainProperties[propertyName].val[i]+
							'</label>';
				}
				html += '</div>';
			}
		}
		html += 	'</div>'+
			'</div>';
		filterBarColumnNames.append(html);
		
		// 2. Bind the events
		if(mainProperties[propertyName].count > 1 && mainProperties[propertyName].val.length > 1){
			if(mainProperties[propertyName].max && mainProperties[propertyName].min) {
				$('.'+getCssPropertyName(propertyName)+'_columnFilter > input').slider({
					value: 	[mainProperties[propertyName].min, mainProperties[propertyName].max],
					step:	((mainProperties[propertyName].max - mainProperties[propertyName].min)/10).toPrecision(1),
					focus: 	true
				}).on('slide', function(){ filterResults(mainProperties); });
			}else{
				$('.'+getCssPropertyName(propertyName)+'_columnFilter').find('input').change(function(){ filterResults(mainProperties); });
			}
		}
	}
	
	// 3. Hide the main properties from the summary list, and show the main properties
	$('.assetPropertiesSummary').each(function(){
		var propertiesSummary = $(this);
		for(var propertyName in mainProperties){
			var devPropertyName = getCssPropertyName(propertyName);
			var propertySummary = $(this).find('.' + devPropertyName + '_propertySummary');
			if(propertySummary.length == 1){ 	// Show the main property
				propertiesSummary.parent().parent().append('<div class="assetPropertyColumn '+devPropertyName+'_column"><span>'+propertySummary.find('.propertyValue').text()+'</span><span class="propertyUnit">'+propertySummary.find('.propertyUnit').text()+'</span></div>');
				propertySummary.hide();
			}else{								// Insert a placeholder instead
				propertiesSummary.parent().parent().append('<div class="assetPropertyColumn '+devPropertyName+'_column"><span>&nbsp;</span></div>')
			}
		}
	});
	
	// 4. Show the filter bar
	$('.categoryAndFilterBar').css('margin-right', getScrollBarWidth()+'px')
	filterBarColumnNames.show();
	filterBar.show();
	if(showFilterBarIcon){
		$(".categoryAndFilterBar").click(showFilters);
		$('.filterIcon').show();
	}
}
function showFilters(){
	// Only show the filter if they did not click on the breadcrumb, or on the minimize button
	if($(event.target).parents('.breadcrumb').length <= 0 && !$(event.target).hasClass('filterMinimize')) {
		var categoryAndFilterBar = $('.categoryAndFilterBar');
		var height = 280;
		categoryAndFilterBar.find('.filterBarColumnFilter').show();
		categoryAndFilterBar.find('.filterIcon').hide();
		categoryAndFilterBar.find('.filterMinimize').show();
		categoryAndFilterBar.css('height', height+'px');
		$('.categoryAndFilterPadder').css('height', (height+15)+'px');
	}
}
function hideFilters(){
	var categoryAndFilterBar = $('.categoryAndFilterBar');
	var height = 35;
	categoryAndFilterBar.find('.filterBarColumnFilter').each(function(){ $(this).hide(); });
	categoryAndFilterBar.find('.filterBarColumnFilter').hide();
	categoryAndFilterBar.find('.filterIcon').show();
	categoryAndFilterBar.find('.filterMinimize').hide();
	categoryAndFilterBar.css('height', height+'px');
	$('.categoryAndFilterPadder').css('height', (height+15)+'px');
}
var filterTimeout = null;
function filterResults(mainProperties){
	clearTimeout(filterTimeout);
	filterTimeout = setTimeout(function(){
		$('.assetPanel').each(function(){
			$(this).show();
		});
		$('.filterBarColumnNames').find('.filterBarColumnFilter').each(function(){
			var inputs = $(this).find('input');
			// If this is a number slider input then filter out
			if (inputs.length == 1 && inputs[0].type == "text" && inputs[0].value.split(",").length == 2){
				var minLimit 	= parseFloat(inputs[0].value.split(",")[0]);
				var maxLimit 	= parseFloat(inputs[0].value.split(",")[1]);
				var step 	= ((parseFloat($(inputs[0]).data('slider-max')) - parseFloat($(inputs[0]).data('slider-min')))/10).toPrecision(1);
				
				$('.assetPanel').each(function(){
					var value = parseFloat($(this).find('.'+getCssPropertyName(inputs[0].name)+'_column').text());
					if(isNaN(value) || value == ""){ // If this asset does not have a property, then hide it if the filter is not maxed out
						if(minLimit > mainProperties[inputs[0].name].min || maxLimit < mainProperties[inputs[0].name].max){
							$(this).hide();
						}
					}else if((value <= minLimit-0.5*step) || (value >= maxLimit+0.5*step)){
						$(this).hide();
					}
				});
				
			// This will be a checkbox categories list
			}else{
				var values = [];
				inputs.filter(':checked').each(function(){ values[values.length] = $(this).val(); });
				if(values.length > 0 && values.length != inputs.length){
					$('.assetPanel').each(function(){
						var value = $(this).find('.'+getCssPropertyName(inputs[0].name)+'_column').text();
						if(values.indexOf(value) == -1){
							$(this).hide();
						}
					});
				}
			}
		});
	}, 1000);
}


/**
 * This will modify the url to show the category and replace the search results of the page
 * with the ajax response for the category
 * If there are any errors at all, then stop any trickiness and just do a full page reload
 * @param category
 */
function goCategory(category){
	$('#mainSearchBar').typeahead('val', ''); // Clear main search bar
	if ($('.navbar-collapse').hasClass('in')) $('.navbar-toggle').trigger('click');
	if(typeof(category) != "undefined" && category != CURRENT_CATEGORY) 	CURRENT_CATEGORY = category;
	else if(QueryString.lat == CURRENT_LOCATION.lat && QueryString.long == CURRENT_LOCATION.long) return false;
	
	var urlEnd = CURRENT_CATEGORY + "?lat=" + CURRENT_LOCATION.lat + "&long=" + CURRENT_LOCATION.long;
	try {
		$('#searchResults').html('');
		removeAllMarkers();
	
		var title = toTitleCase(CURRENT_CATEGORY) + " - Proxihire";
		$.ajax({
			type: 	"POST",
			url: 	"/assetlist/"+urlEnd, 
			success: function(html){
				try{
					History.pushState({html: html}, title, "/search/"+urlEnd);
				}catch(e){
					window.location.href = "/search/" + urlEnd;
				}
			},
			error: function(){
				window.location.href = "/search/" + urlEnd;
			}
		});
	} catch (e) {
		window.location.href = "/search/" + urlEnd;
	}	
	return false;
}

function postGoCategory(){
	updateFilterBar();
	adjustOverflowingCategoryPicker();
	showAllMarkers();
	
	var bounds = '&bounds=' + (CURRENT_LOCATION.lat-2) + "," + (CURRENT_LOCATION.long-2) + ',' + (CURRENT_LOCATION.lat+2) + "," + (CURRENT_LOCATION.long+2);
	$('#googleMapSearchBar').typeahead({
		hint: true,
		highlight: true,
		minLength: 1
	}, {
		name: 'geonames',
		display: function(results){
			if(results && results[0]) return results[0].formatted_address;
		},
		source: new Bloodhound({
			datumTokenizer: Bloodhound.tokenizers.obj.whitespace('formatted_address'),
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			prefectch: 'https://maps.googleapis.com/maps/api/geocode/json?address=auckland&key=AIzaSyD6QGNeko6_RVm4dMCRdeQhx8oLb24GGxk'+bounds,
			remote: {
				url: 'https://maps.googleapis.com/maps/api/geocode/json?address=%QUERY&key=AIzaSyD6QGNeko6_RVm4dMCRdeQhx8oLb24GGxk'+bounds,
				wildcard: '%QUERY'
			}
		})
	}).bind('typeahead:select', function(ev, suggestion){
		if(suggestion.geometry && suggestion.geometry.location){
			goLocationAndChangeGoogleMaps(suggestion.geometry.location.lat, suggestion.geometry.location.lng);
		}
	}).bind('typeahead:autocomplete', function(ev, suggestion){
		$('#googleMapSearchBar').blur();
		if(suggestion.geometry && suggestion.geometry.location){
			goLocationAndChangeGoogleMaps(suggestion.geometry.location.lat, suggestion.geometry.location.lng);
		}
	}).bind('typeahead:cursorchange', function(ev, suggestion){
		console.log('could have async fetching here as well?' + suggestion);
	});
}

$(document).ready(function(){
    History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
        if(History.getState().data.html){
    		$('#searchResults').html(History.getState().data.html);
    		postGoCategory();
        }
    });
    
	$('#mainSearchBar').typeahead({
		hint: true,
		highlight: true,
		minLength: 1
	}, {
		source: substringMatcher(linearize(categories)),
		name: 'categories'
	}).bind('typeahead:select', function(ev, suggestion){
		goCategory(suggestion);
	}).bind('typeahead:autocomplete', function(ev, suggestion){
		$('#mainSearchBar').blur();
		goCategory(suggestion);
	}).bind('typeahead:cursorchange', function(ev, suggestion){
		console.log('todo: could have async fetching here?'+suggestion);
	});

	postGoCategory();
});

function adjustOverflowingCategoryPicker(){
	var filterBar = $('.categoryAndFilterBar');
	if(filterBar.height() > 20){
		filterBar.find('.breadcrumb > .dropdown > .dropdown-toggle > span').each(function(){
			if(filterBar.height() > 20 && !$(this).parent().is(':last-child')){
				$(this).hide();
			}
		});
	}
}





/**
 * @see http://stackoverflow.com/questions/986937/how-can-i-get-the-browsers-scrollbar-sizes
 */
function getScrollBarWidth () {
	var inner = document.createElement('p');
	inner.style.width = "100%";
	inner.style.height = "200px";
	var outer = document.createElement('div');
	outer.style.position = "absolute";
	outer.style.top = "0px";
	outer.style.left = "0px";
	outer.style.visibility = "hidden";
	outer.style.width = "200px";
	outer.style.height = "150px";
	outer.style.overflow = "hidden";
	outer.appendChild (inner);
	document.body.appendChild (outer);
	var w1 = inner.offsetWidth;
	outer.style.overflow = 'scroll';
	var w2 = inner.offsetWidth;
	if (w1 == w2) w2 = outer.clientWidth;
	document.body.removeChild (outer);
	return (w1 - w2);
};
function toTitleCase(str)
{
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1);});
}
