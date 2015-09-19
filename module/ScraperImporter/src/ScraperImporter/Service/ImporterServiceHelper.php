<?php
namespace ScraperImporter\Service;

class ImporterServiceHelper {
	const REFRESH_ASSET_IMAGES = FALSE; // Whether we want to check to see whether they've changed the images on their server.
	
	private $propertyAliases = array();
	
	
	/**
	 * Determines the property name and value, using the name and value pair.
	 * Will also fix up the property name using the category and the values in the PropertyAliases.csv file
	 * @param string $key
	 * @param string $value
	 * @param string $categoryName
	 * @return array - the indexes are name_fulnam, datatype, and value_mxd
	 */
	private function determineProperty($key, $value, $categoryName){
		$key 	= $this->fixSpelling($key);
		$value 	= $this->fixValue($value);
		$property = array("name_fulnam" => $key, "datatype"=>"string", "value_mxd"=>$value);
	
		if($numberAndUnit = $this->getNumberAndUnit($key, $value)){
			$number = $numberAndUnit[0];
			$unit	= $numberAndUnit[1];
				
			// Unit Matching
			if($unit === 'deg' OR $unit === 'degrees'){
				$property['datatype']  = 'angle';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'mg'){
				$property['datatype']  = 'weight';
				$property['value_mxd'] = floatval($number) / 1000;
			}elseif($unit === 'g'){
				$property['datatype']  = 'weight';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'kg'){
				$property['datatype']  = 'weight';
				$property['value_mxd'] = floatval($number) * 1000;
			}elseif($unit === 'tonne' OR $unit === 'tonnes'){
				$property['datatype']  = 'weight';
				$property['value_mxd'] = floatval($number) * 1000 * 1000;
			}elseif($unit === 'dan'){
				$property['datatype']  = 'force';
				$property['value_mxd'] = floatval($number) * 10;
			}elseif($unit === 'sec'){
				$property['datatype']  = 'time';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'mm'){
				$property['datatype']  = 'lineal';
				$property['value_mxd'] = floatval($number) / 1000;
			}elseif($unit === 'cm'){
				$property['datatype']  = 'lineal';
				$property['value_mxd'] = floatval($number) / 100;
			}elseif($unit === 'm'){
				$property['datatype']  = 'lineal';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'km'){
				$property['datatype']  = 'lineal';
				$property['value_mxd'] = floatval($number) * 1000;
			}elseif($unit === 'lit'){
				$property['datatype']	= 'volume';
				$property['value_mxd']	= floatval($number);
			}elseif($unit === 'ml'){
				$property['datatype']	= 'volume';
				$property['value_mxd']	= floatval($number) / 1000;
			}elseif($unit === 'm3'){
				$property['datatype']	= 'volume';
				$property['value_mxd']	= floatval($number) * 1000;
			}elseif($unit === 'hp' OR $unit === 'horsepower'){
				$property['datatype']	= 'power';
				$property['value_mxd']	= floatval($number) * 745.699872;
			}elseif($unit === 'hz'){
				$property['datatype']	= 'frequency';
				$property['value_mxd']	= floatval($number);
			}elseif($unit === 'rpm'){
				$property['datatype']	= 'frequency';
				$property['value_mxd']	= floatval($number) / 60;
			}elseif($unit === '/min'){
				$property['datatype']	= 'frequency';
				$property['value_mxd']	= floatval($number) / 60;
			}elseif($unit === 'psi'){
				$property['datatype']	= 'pressure';
				$property['value_mxd']	= floatval($number) * 6894.75729;
			}elseif($unit === 'km/hr' OR $unit === 'km/h'){
				$property['datatype']	= 'speed';
				$property['value_mxd']	= floatval($number) * 1000 / 60 / 60;
			}elseif($unit === 'm/s' OR $unit === 'm/sec'){
				$property['datatype']	= 'speed';
				$property['value_mxd']	= floatval($number);
			}elseif($unit === 'amps' OR $unit === 'amp'){
				$property['datatype']  = 'current';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'watts'){
				$property['datatype']  = 'power';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'kw'){
				$property['datatype']  = 'power';
				$property['value_mxd'] = floatval($number) * 1000;
			}elseif($unit === 'nm'){
				$property['datatype']  = 'torque';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'ltr/hr' OR $unit === 'lit/hr'){
				$property['datatype']  = 'flow';
				$property['value_mxd'] = floatval($number) / 60 / 60; // Convert to ltr/sec
			}elseif($unit === 'ltr/min' OR $unit === 'lit/min'){
				$property['datatype']  = 'flow';
				$property['value_mxd'] = floatval($number) / 60; // Convert to ltr/sec
			}elseif($unit === 'cfm'){
				$property['datatype']  = 'flow';
				$property['value_mxd'] = floatval($number) * 0.471947443; // Convert to ltr/sec
	
				// Key name matching - TODO: find better way of determining the following code
			}elseif($this->isIn($key, 'angle')){
				$property['datatype']  = 'angle';
				$property['value_mxd'] 	= floatval($number);
	
			}elseif($this->isIn($key, 'per minute')){
				$property['datatype']  = 'frequency';
				$property['value_mxd'] 	= floatval($number) * 60;
					
			}elseif($this->isIn($key, 'volts')){
				$property['datatype']	= 'voltage';
				$property['value_mxd']	= floatval($number);
					
			}elseif($this->isIn($key, 'mtr')){
				$property['datatype']	= 'lineal';
				$property['value_mxd']	= floatval($number);
			}
				
		}else{
			if($value === "yes"){
				$property['datatype']	= 'boolean';
				$property['value_mxd']	= true;
	
			}elseif($value === "no"){
				$property['datatype']	= 'boolean';
				$property['value_mxd']	= false;
			}
		}
		$property['name_fulnam'] = $this->fixPropertyName($property['name_fulnam'], $categoryName);
		return $property;
	}

	
	public function __construct(){
		$this->propertyAliases = array_map('str_getcsv', file(__DIR__.'/PropertyAliases.csv'));
	}
	


	public function jsonDecode($json){
		$array = json_decode($json);
		if(! $array){
			file_put_contents('/tmp/test.json', $json);
			exec('jsonlint /tmp/test.json 2> /tmp/result.jsonlint');
			$result = file_get_contents('/tmp/result.jsonlint');
			throw new \Exception($result);
		}
		return $array;
	}
	/**
	 * This fetches an image of a crawled site and puts it into the /public/img/assets/ folder
	 * @param string $url
	 * @return boolean
	 */
	public function syncImage($url){
		if($url !== null AND $url !== ""){
			$urlComponents = parse_url($url);
			if(isset($urlComponents['host']) AND isset($urlComponents['path'])){
				$localImageRelativePath = $urlComponents['host'].$urlComponents['path'];
				$localImage = __DIR__.'/../../../../../public/img/assets/'.$localImageRelativePath;
				if($this::REFRESH_ASSET_IMAGES OR !file_exists($localImage)){
					$directory = dirname($localImage);
					$this->mkdir($directory);
					exec("cd $directory; wget -N ".addslashes($url));
					if(file_exists($localImage)) return $localImageRelativePath;
				}else{
					return $localImageRelativePath;
				}
			}
		}
		return null;
	}
	
	/**
	 * Resizes and crops an image 
	 * @param string $imagePath
	 * @param integer $x
	 * @param integer $y
	 * @return string 	- the new image url
	 */
	public function resizeAndCropImage($imagePath, $x=120, $y=120){
		if($imagePath !== null){
			$imagePathParts = explode('.', $imagePath);
			$newImagePath = implode('.', array_splice($imagePathParts, 0, -1))."_".$x."x".$y.".".end($imagePathParts);
			if(!file_exists($newImagePath) OR $this::REFRESH_ASSET_IMAGES){
				// Remove whitespace from image
				exec("convert -trim $imagePath $newImagePath");
				// Resize the image to the desired size
				exec("convert -define jpeg:size=".($x*2)."x".($y*2)." $newImagePath -thumbnail ".$x."x".$y."^ -gravity center -extent ".$x."x".$y." $newImagePath"); 
			}
			return $newImagePath;
		}
		return null;
	}
	
	/**
	 * Recursively makes a directory.
	 * As php one doesn't seem to work recursively
	 * @param string $dir
	 * @return boolean
	 */
	private function mkdir($dir){
		if(file_exists($dir)) 				return true;
		elseif(file_exists(dirname($dir))){
			if(!mkdir($dir)) 				echo "Could not create directory. Please run: `sudo chown -R www-data:www-data ".dirname($dir);
			else 							return true;
		}else								return $this->mkdir(dirname($dir));
		return false;
	}
	
	
	
	
	
	
	
	private function isIn($haystack, $needle){
		return preg_match("/".$needle."/", $haystack);
	}
	
	private function getNumberAndUnit($key, $value){
		$key = trim($key);
		$value = trim($value);
		// Try to extract out number-unit in $value
		if(preg_match("/([\-0-9.]+)[\s]*([\D]*)/", $value, $result)){
			if(count($result) === 3){
				if($result[0] === $value){
					if($result[2] !== ''){
						return array(trim($result[1]), trim($result[2]));
					}
				}
			}
			// Try to extract out (unit) out of $value
			preg_match('/(\([a-zA-Z \/]*\))/', $key, $keyResult);
			if(count($keyResult) === 2){
				return array(trim($result[1]), trim($keyResult[1], '() '));
			}
			return array(trim($result[1]), $key);
		}
		return false;
	}
	
	private function fixPropertyName($propertyName, $categoryName){
		foreach($this->propertyAliases as $propertyAlias){
			if($propertyAlias[0] === $categoryName AND $propertyName === $propertyAlias[1]){
				return $propertyAlias[2];
			}
		}
		return $propertyName;
	}
	
	private function fixSpelling($string){
		$string = str_replace('hight', 		'high',		$string);
		$string = str_replace('lenght', 	'length', 	$string);
		$string = str_replace('rptation', 	'rotation', $string);
		$string = str_replace('widht', 		'width', 	$string);
		return $string;
	}
	private function fixValue($string){
		$string = str_replace('approx.', 	'', 	$string);
		$string = str_replace('(approx)', 	'', 	$string);
		if(preg_match('/([0-9\-.]+.*)\([0-9\-.]+.*\)/', $string, $result)){ // If there is a metric unit and imperial unit. One of them is in brackets
			return trim($result[1]);
		}
		return $string;
	}
	
	/**
	 * Determines the properties that are for an asset
	 * @param string $key
	 * @param string $value
	 * @param string $categoryName
	 * @return array 
	 */
	public function determineProperties($key, $value, $categoryName){
		$key   = trim(strtolower($key),   ":- ");
		$value = trim(strtolower($value), ": ");
	
		if($this->isIn($value, "[0-9].* to .*[0-9]")){
			$twoNumbers = explode(" to ", $value);
			$min = $twoNumbers[0];
			$max = $twoNumbers[1];
			if(preg_match('/[0-9\-.]+\s*([a-zA-Z\/]+)/', $max, $maxUnits) AND floatval($min) == $min){ // If the max has the units, then put the units onto the min as well
				$min = $min.$maxUnits[1];
			}
			return array($this->determineProperty("min ".$key, $min, $categoryName), $this->determineProperty("max ".$key, $max, $categoryName));
				
		}else return array($this->determineProperty($key, $value, $categoryName));
	}

	/**
	 * Determines the category by looking at categories.js file
	 * @param CategoryAliases $category
	 * @param string $name
	 * @return Ambigous Category|NULL
	 */
	public function determineCategory($category, $name){
		if($matchedCategory = $this->determineCategoryExactMatch($category, $name)) 	return $matchedCategory;
		if($matchedCategory = $this->determineCategoryMatchedWords($category, $name)) 	return $matchedCategory;
		return null;
	}
	
	/**
	 * Finds the subcategory that contains all of the words in any part of the string.
	 * TODO: return multiple results, and the one that is the longest,
	 * rather than the first that we find.
	 *
	 * @param Category $category
	 * @param string $name
	 * @return Category|NULL
	 */
	private function determineCategoryMatchedWords($category, $name){
		if(property_exists($category, 'children')) {
			foreach($category->children as $subCategory){
				$result = $this->determineCategoryMatchedWords($subCategory, $name);
				if($result !== null) return $result;
			}
		}
		if(property_exists($category, 'aliases')) {
			foreach($category->aliases as $alias){
				$aliasWords = explode(' ', $alias);
				$usesAllAliasWords = true;
				foreach($aliasWords as $word){
					if(! $this->isIn(strtolower($name), strtolower($word)) OR strlen($word) === 1){
						$usesAllAliasWords = false;
					}
				}
				if($usesAllAliasWords){
					return $category;
				}
			}
		}
		return null;
	}
	/**
	 * Finds the subcategory that matches the exact name.
	 * TODO: return multiple results, and the one that is the longest,
	 * rather than the first that we find.
	 *
	 * @param Category $category
	 * @param string $name
	 * @return Category|NULL
	 */
	private function determineCategoryExactMatch($category, $name){
		if(property_exists($category, 'children')) {
			foreach($category->children as $subCategory){
				$result = $this->determineCategoryExactMatch($subCategory, $name);
				if($result !== null) return $result;
			}
		}
		if(property_exists($category, 'aliases')) {
			foreach($category->aliases as $alias){
				if($this->isIn(strtolower($name), strtolower($alias))){
					return $category;
				}
			}
		}
		return null;
	}
}
?>