<?php 
/**
 * This reads in the files
 * 	- public/js/categories.js
 * 	- module/Application/src/Application/Model/Datatype.php
 * And generates the SQL for creating all the cateogries and datatypes
 * 
 * @author philip.harris
 */

// 1. Categories
$categories_file = dirname(dirname(__FILE__)).'/public/js/categories.js';
$categories_json = str_replace('categories = ', '', str_replace(';', '', file_get_contents($categories_file)));
$categories = json_decode($categories_json);

echo "USE RentStuff;\n";
foreach($categories->children as $category){
	printOutSqlForCategory($category);
}

function printOutSqlForCategory($category, $parent=null){
	if( $parent == null ){
		echo "INSERT IGNORE INTO category (name_fulnam) VALUES ('".$category->aliases[0]."'); \n";
	} else {
		echo "INSERT IGNORE INTO category (name_fulnam,parent_category_id) \n".
		"	SELECT '".$category->aliases[0]."',category_id \n".
		"	FROM category WHERE name_fulnam='$parent'; \n";
	}
	foreach($category->aliases as $key=>$alias){
		// Ignore the first item
		if( $key > 0 ){
			echo "INSERT IGNORE INTO category_alias (category_id, alias_fulnam) \n".
			"	SELECT category.category_id, '".$alias."'\n".
			"	FROM category WHERE category.name_fulnam = '".$category->aliases[0]."';\n";
		}
	}
	if(isset($category->children)){
		foreach($category->children as $child){
			printOutSqlForCategory($child, $category->aliases[0]);
		}
	}
}



// 2. Datatypes
$categories_file = file_get_contents(dirname(dirname(__FILE__)).'/module/Application/src/Application/Model/Datatype.php');
foreach(preg_split("/((\r?\n)|(\r\n?))/", $categories_file) as $line){
	if(preg_match('/const .*= ([\'"a-zA-Z"]*)/', $line, $match)){ // find the value of the constant in the Datatype.php file
		echo "INSERT IGNORE INTO datatype ( datatype_abbr ) VALUES ( " . $match[1] ." );\n";
	}
}
?>
