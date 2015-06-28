$(document).ready(function(){
	/**
	 * Takes in a category and returns the list of sub categories
	 */
	function linearize(category){
		var returnList = [];
		if(category.aliases){
			for(var i in category.aliases){
				returnList[returnList.length] = {
					value:		category.aliases[i],
					category:	category.aliases[0]
				}
			}
		}
		if(category.children){
			for(var i in category.children){
				returnList = returnList.concat(linearize(category.children[i]))
			}
		}
		return returnList;
	}
	console.log(linearize(categories));
	
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
		 
		    cb(matches);
		  };
		};
	
	$('#mainSearchBar').typeahead({
		hint: 		true,
		highlight: 	true,
		minLength: 	1
	}, {
		display: 'value',
		source: substringMatcher(categories),
		name: 'categories',
		suggestion: Handlebars.compile('<div><strong>{{value}}</strong> – {{category}}</div>')
	})
	// TODO psh send list to typeahead search box.
});