
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

	// Return only the properties that 80% of the assets have, and if we can display them
	// TODO: determine better way to determine which are the best properties
	var widthOfBreadCrumb = Math.max(300, $('.categoryAndFilterBarWrapper .breadcrumb').width());
	var maxNumberOfMainProperties = Math.floor(($('.categoryAndFilterBarWrapper').width()-widthOfBreadCrumb-30)/120);
	var mainProperties = [];
	var i = 0;
	for(var propertyName in allProperties){
		if(allProperties[propertyName].count/allProperties.length >= 0.8 && i < maxNumberOfMainProperties){
			mainProperties[propertyName] = allProperties[propertyName];
		}
		i++;
	}
	return mainProperties;
}

function updateFilterBar(){
	var mainProperties = getMainProperties();
	var filterBar = $('.filterBar');
	var filterBarColumnNames 	= filterBar.find('.filterBarColumnNames');
	filterBarColumnNames.html('&nbsp;');
	
	// A. Add the filters at the top of the page
	for(var propertyName in mainProperties){
		// 1. Add the html
		var html = 
			'<div class="filterBarPropertyColumn"><div class="filterBarPropertyColumnName">'+propertyName+'</div>'+
				'<div class="filterBarColumnFilter '+propertyName.replace(' ', '_')+'_columnFilter">';
		if(mainProperties[propertyName].count > 1){
			if(typeof(mainProperties[propertyName].max) != "undefined" && typeof(mainProperties[propertyName].min) != "undefined") {
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
		html += '</div>'+
			'</div>';
		filterBarColumnNames.append(html);
		
		// 2. Bind the events
		if(mainProperties[propertyName].count > 1){
			if(typeof(mainProperties[propertyName].max) != "undefined" && typeof(mainProperties[propertyName].min) != "undefined") {
				$('.'+propertyName.replace(' ', '_')+'_columnFilter > input').slider({
					value: 	[mainProperties[propertyName].min, mainProperties[propertyName].max],
					step:	((mainProperties[propertyName].max - mainProperties[propertyName].min)/10).toPrecision(1),
					focus: 	true
				}).on('slide', filterResults);
			}else{
				$('.'+propertyName.replace(' ', '_')+'_columnFilter').find('input').change(filterResults);
			}
		}
	}
	
	// 3. Hide the main properties from the summary list, and show the main properties
	$('.assetPropertiesSummary').each(function(){
		var propertiesSummary = $(this);
		for(var propertyName in mainProperties){
			var devPropertyName = propertyName.replace(' ', '_').toLowerCase();
			var propertySummary = $(this).find('.' + devPropertyName + '_propertySummary');
			if(propertySummary.length == 1){ 	// Show the main property
				propertiesSummary.parent().append('<div class="assetPropertyColumn '+devPropertyName+'_column"><span>'+propertySummary.find('.propertyValue').text()+'</span></div>')
				propertySummary.hide();
			}else{								// Insert a placeholder instead
				propertiesSummary.parent().append('<div class="assetPropertyColumn"><span>&nbsp;</span></div>')
			}
		}
	});
	
	// 4. Show the filter bar
	$('.categoryAndFilterBar').css('margin-right', getScrollBarWidth()+'px')
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
var filterTimeout = null;
function filterResults(){
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
				console.log()
				var step 	= ((parseFloat($(inputs[0]).data('slider-max')) - parseFloat($(inputs[0]).data('slider-min')))/10).toPrecision(1);
				
				$('.assetPanel').each(function(){
					var value = parseFloat($(this).find('.'+inputs[0].name.toLowerCase().replace(' ', '_')+'_column').text());
					if((value <= minLimit-0.5*step) || (value >= maxLimit+0.5*step)){
						$(this).hide();
					}
				});
				
			// This will be a checkbox categories list
			}else{
				var values = [];
				inputs.filter(':checked').each(function(){ values[values.length] = $(this).val(); });
				if(values.length > 0 && values.length != inputs.length){
					$('.assetPanel').each(function(){
						var value = $(this).find('.'+inputs[0].name.toLowerCase().replace(' ', '_')+'_column').text();
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
 * @param category
 */
function goCategory(category){
	window.location.href = "/search/" + category;
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