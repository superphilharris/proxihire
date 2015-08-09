<?php 

$categories_file = dirname(dirname(__FILE__)).'/js/categories.js';

$categories_json = str_replace('categories = ', '', str_replace(';', '', file_get_contents($categories_file)));
$categories = json_decode($categories_json);

foreach($categories->children as $category){
	printOutSqlForCategory($category);
}

function printOutSqlForCategory($category, $parent=null){
	echo "INSERT IGNORE INTO category (name_fulnam) VALUES ('".$category->aliases[0]."'); \n";
	foreach($category->aliases as $alias){
		echo "INSERT IGNORE INTO category_alias (category_id, alias_fulnam) \n".
		"	SELECT category.category_id, '".$alias."'\n".
		"	FROM category WHERE category.name_fulnam = '".$category->aliases[0]."';\n";
	}
	if(isset($category->children)){
		foreach($category->children as $child){
			printOutSqlForCategory($child, $category->aliases[0]);
		}
	}
}
?>
