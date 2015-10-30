<?php

$country = "NZ";
generateSQL($country);

function generateSQL($country){
	$filename = dirname(__FILE__)."/geonames/".$country.".txt";
	$fh = fopen($filename, "r");
	if($fh){
		while(!feof($fh)){
			$line = fgets($fh);
			$columns = explode("\t", $line);
			if(count($columns) > 8){
				echo "INSERT IGNORE INTO geoname (geoname_id, name_fulnam, latitude_float, longitude_float, country_code) VALUES ('".
						$columns[0]."','".
		   				addslashes($columns[2])."','".
						$columns[4]."','".
						$columns[5]."','".
						$columns[8]."'); \n";
			}
		}
	}else echo "Could not open $filename";
}


?>
