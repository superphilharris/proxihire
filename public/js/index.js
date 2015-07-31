$(document).ready(function(){
	/**
	 * Takes in a category and returns the list of sub categories
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
	
	var substringMatcher = function(strs) {
	  return function findMatches(q, cb) {
	    var matches, substringRegex;
	    // an array that will be populated with substring matches
	    matches = [];
	    // regex used to determine if a string contains the substring `q`
	    substrRegex = new RegExp(q, 'i');
	    // iterate through the pool of strings and for any string that
	    // contains the substring `q`, add it to the `matches` array
	    $.each(strs, function(i, str) {
	      if (substrRegex.test(str)) {
	        matches.push(str);
	      }
	    });
	    if(q.toLowerCase() == matches[0]) console.log('typed the only suggestion: '+matches[0]);
	    cb(matches);
	  };
	};
	
	$('#mainSearchBar').typeahead({
		hint: true,
		highlight: true,
		minLength: 1
	}, {
		source: substringMatcher(linearize(categories)),
		name: 'categories'
	}).bind('typeahead:select', function(ev, suggestion){
		window.location.href = suggestion;
	}).bind('typeahead:autocomplete', function(ev, suggestion){
		$('#mainSearchBar').blur();
		window.location.href = suggestion;
	}).bind('typeahead:cursorchange', function(ev, suggestion){
		console.log('todo: could have async fetching here?'+suggestion);
	});
});