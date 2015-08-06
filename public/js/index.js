
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
	$('.assetPropertiesSummary').each(function(){
		$(this).children('div').each(function(){
			var propertyName 	= $(this).find('.propertyName').text();
			var propertyValue 	= $(this).find('.propertyValue').text();
			if(typeof allProperties[propertyName] != "undefined"){
				allProperties[propertyName].count ++;
				if($.isNumeric(propertyValue)){
					allProperties[propertyName]['min'] = Math.min(propertyValue, allProperties[propertyName].min);
					allProperties[propertyName]['max'] = Math.max(propertyValue, allProperties[propertyName].max);
				}else{
					if(allProperties[propertyName].val.indexOf(propertyValue) < 0){
						allProperties[propertyName].val.push(propertyValue);
					}
				}
			}else{
				allProperties[propertyName] = { count: 1 };
				if($.isNumeric(propertyValue)){
					allProperties[propertyName].min = propertyValue;
					allProperties[propertyName].max = propertyValue;
				}else{
					allProperties[propertyName].val = [propertyValue];
				}
			}
		});
	});

	// Return only the properties that 80% of the assets have
	var mainProperties = [];
	for(var propertyName in allProperties){
		if(allProperties[propertyName].count/allProperties.length >= 0.8){
			mainProperties[propertyName] = allProperties[propertyName];
		}
	}
	return mainProperties;
}
function updateFilterBar(){
	var mainProperties = getMainProperties();
	var filterBar = $('.filterBar');
	var filterBarColumnNames 	= filterBar.find('.filterBarColumnNames');
	filterBarColumnNames.html('&nbsp;');
	
	for(var propertyName in mainProperties){
		// 1. Add the html
		var html = 
			'<div class="filterBarPropertyColumn">'+propertyName+
				'<div class="filterBarColumnFilter '+propertyName.replace(' ', '_')+'_columnFilter">';
		if(typeof(mainProperties[propertyName].max) != "undefined" && typeof(mainProperties[propertyName].min) != "undefined") {
			html += '<input type="text" data-slider-orientation="vertical"/>';
		}else{
			html += '<div class="btn-group-vertical" data-toggle="buttons">';
			for(var i in mainProperties[propertyName].val){
				html += '<label class="btn btn-default">'+
							'<input type="checkbox" name="'+propertyName+'" value="'+mainProperties[propertyName].val[i]+'">'+mainProperties[propertyName].val[i]+
						'</label>';
			}
			html += '</div>';
		}
		html += '</div>'+
			'</div>';
		filterBarColumnNames.append(html);
		
		// 2. Bind the events
		if(typeof(mainProperties[propertyName].max) != "undefined" && typeof(mainProperties[propertyName].min) != "undefined") {
			$('.'+propertyName.replace(' ', '_')+'_columnFilter > input').slider({
				min: 	mainProperties[propertyName].min,
				max: 	mainProperties[propertyName].max,
				value: 	[mainProperties[propertyName].min, mainProperties[propertyName].max],
				step:	((mainProperties[propertyName].max - mainProperties[propertyName].min)/10).toPrecision(1),
				focus: 	true
			});
		}else{
			
		}
	}
	filterBarColumnNames.show();
	filterBar.show();
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


/**
 * This will modify the url to show the category and replace the search results of the page
 * with the ajax response for the category
 * @param category
 */
function goCategory(category){
	window.location.href = category;
 /*   document.getElementById("content").innerHTML = response.html;
    document.title = response.pageTitle;
    window.history.pushState({"html":response.html,"pageTitle":response.pageTitle},"", urlPath);*/
}


$(document).ready(function(){
	
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

	updateFilterBar();
});