
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
});