<?php
namespace ScraperImporter\Service;

use Application\Model\Datatype;

class ImporterServiceHelper {
	// The below 3 configurations are used to speed up the scraping for testing purposes.
	const UPDATE_IMAGES 			= FALSE; // Whether we want to check to see whether they've changed the images on their server.
// 	const GENERATE_RANDOM_LOCATIONS = FALSE; // Turn on if we are overusing the google api. Set to TRUE to speed up.
// 	const CREATE_IMAGES				= TRUE;  // Whether we want to copy their images over. Set to FALSE to speed up.
	const GENERATE_RANDOM_LOCATIONS = TRUE;
	const CREATE_IMAGES				= FALSE;
	
	private $propertyAliases = array();
	const GOOGLE_API_KEY = "AIzaSyD6QGNeko6_RVm4dMCRdeQhx8oLb24GGxk";
	
	
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
		$property = array("name_fulnam" => $key, "datatype"=>Datatype::STRING, "value_mxd"=>$value);
	
		if($numberAndUnit = $this->getNumberAndUnit($key, $value)){
			$number = $numberAndUnit[0];
			$unit	= $numberAndUnit[1];
				
			// Unit Matching
			if($unit === 'deg' OR $unit === 'degrees'){
				$property['datatype']  = Datatype::ANGLE;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'degrees celsius'){
				$property['datatype']  = Datatype::TEMPERATURE;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'mg'){
				$property['datatype']  = Datatype::WEIGHT;
				$property['value_mxd'] = floatval($number) / 1000;
			}elseif($unit === 'g'){
				$property['datatype']  = Datatype::WEIGHT;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'kg' OR $unit === 'kgs'){
				$property['datatype']  = Datatype::WEIGHT;
				$property['value_mxd'] = floatval($number) * 1000;
			}elseif($unit === 'tonne' OR $unit === 'tonnes'){
				$property['datatype']  = Datatype::WEIGHT;
				$property['value_mxd'] = floatval($number) * 1000 * 1000;
			}elseif($unit === 'kg/hr'){
				$property['datatype']  = Datatype::WEIGHT_FLOW;
				$property['value_mxd'] = floatval($number) * 3600 / 1000;
			}elseif($unit === 'dan'){
				$property['datatype']  = Datatype::FORCE;
				$property['value_mxd'] = floatval($number) * 10;
			}elseif($unit === 'sec'){
				$property['datatype']  = Datatype::TIME;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'days' OR $unit === 'day'){
				$property['datatype']  = Datatype::TIME;
				$property['value_mxd'] = floatval($number) * 60 * 60 * 24;
			}elseif($unit === 'hours' OR $unit === 'hour'){
				$property['datatype']  = Datatype::TIME;
				$property['value_mxd'] = floatval($number) * 60 * 60;
			}elseif($unit === 'minutes' OR $unit === 'minute'){
				$property['datatype']  = Datatype::TIME;
				$property['value_mxd'] = floatval($number) * 60;
			}elseif($unit === 'ft'){
				$property['datatype']  = Datatype::LINEAL;
				$property['value_mxd'] = floatval($number) * 0.3048;
			}elseif($unit === 'mm'){
				$property['datatype']  = Datatype::LINEAL;
				$property['value_mxd'] = floatval($number) / 1000;
			}elseif($unit === 'cm'){
				$property['datatype']  = Datatype::LINEAL;
				$property['value_mxd'] = floatval($number) / 100;
			}elseif($unit === 'm' OR $unit === "metre" OR $unit === "metres"){
				$property['datatype']  = Datatype::LINEAL;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'km'){
				$property['datatype']  = Datatype::LINEAL;
				$property['value_mxd'] = floatval($number) * 1000;
			}elseif($unit === 'lit' OR $unit === 'litre'){
				$property['datatype']	= Datatype::VOLUME;
				$property['value_mxd']	= floatval($number);
			}elseif($unit === 'ml'){
				$property['datatype']	= Datatype::VOLUME;
				$property['value_mxd']	= floatval($number) / 1000;
			}elseif($unit === 'm3'){
				$property['datatype']	= Datatype::VOLUME;
				$property['value_mxd']	= floatval($number) * 1000;
			}elseif($unit === 'hp' OR $unit === 'horsepower'){
				$property['datatype']	= Datatype::POWER_MECHANICAL;
				$property['value_mxd']	= floatval($number) * 745.699872;
			}elseif($unit === 'hz'){
				$property['datatype']	= Datatype::FREQUENCY;
				$property['value_mxd']	= floatval($number);
			}elseif($unit === 'rpm'){
				$property['datatype']	= Datatype::FREQUENCY;
				$property['value_mxd']	= floatval($number) / 60;
			}elseif($unit === '/min'){
				$property['datatype']	= Datatype::FREQUENCY;
				$property['value_mxd']	= floatval($number) / 60;
			}elseif($unit === "bar"){
				$property['datatype']   = Datatype::PRESSURE;
				$property['value_mxd']  = floatval($number) * 100000;
			}elseif($unit === 'psi'){
				$property['datatype']	= Datatype::PRESSURE;
				$property['value_mxd']	= floatval($number) * 6894.75729;
			}elseif($unit === 'km/hr' OR $unit === 'km/h'){
				$property['datatype']	= Datatype::SPEED;
				$property['value_mxd']	= floatval($number) * 1000 / 60 / 60;
			}elseif($unit === 'm/s' OR $unit === 'm/sec'){
				$property['datatype']	= Datatype::SPEED;
				$property['value_mxd']	= floatval($number);
			}elseif($unit === 'amps' OR $unit === 'amp'){
				$property['datatype']  = Datatype::CURRENT;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'mamps'){
				$property['datatype']  = Datatype::CURRENT;
				$property['value_mxd'] = floatval($number) / 1000;
			}elseif($unit === 'btu/hr' OR $unit === 'btus/hr' OR $unit === 'btu'){
				$property['datatype']  = Datatype::POWER_ELECTRICAL;
				$property['value_mxd'] = floatval($number) * 0.29307107;
			}elseif($unit === 'watts'){
				$property['datatype']  = Datatype::POWER_ELECTRICAL;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'kw'){
				$property['datatype']  = Datatype::POWER_ELECTRICAL;
				$property['value_mxd'] = floatval($number) * 1000;
			}elseif($unit === 'volt' OR $unit === 'volts'){
				$property['datatype']  = Datatype::VOLTAGE;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'nm'){
				$property['datatype']  = Datatype::TORQUE;
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'ltr/hr' OR $unit === 'lit/hr'){
				$property['datatype']  = Datatype::FLOW;
				$property['value_mxd'] = floatval($number) / 60 / 60; // Convert to ltr/sec
			}elseif($unit === 'ltr/min' OR $unit === 'lit/min'){
				$property['datatype']  = Datatype::FLOW;
				$property['value_mxd'] = floatval($number) / 60; // Convert to ltr/sec
			}elseif($unit === 'cfm'){
				$property['datatype']  = Datatype::FLOW;
				$property['value_mxd'] = floatval($number) * 0.471947443; // Convert to ltr/sec
	
				// Key name matching - TODO: find better way of determining the following code
			}elseif($this->isIn($key, 'angle')){
				$property['datatype']  = Datatype::ANGLE;
				$property['value_mxd'] 	= floatval($number);
	
			}elseif($this->isIn($key, 'per minute')){
				$property['datatype']  = Datatype::FREQUENCY;
				$property['value_mxd'] 	= floatval($number) * 60;
					
			}elseif($this->isIn($key, 'volts')){
				$property['datatype']	= Datatype::VOLTAGE;
				$property['value_mxd']	= floatval($number);
					
			}elseif($this->isIn($key, 'mtr')){
				$property['datatype']	= Datatype::LINEAL;
				$property['value_mxd']	= floatval($number);
			}
			$property['name_fulnam'] = str_replace('('.$unit.')', '', $property['name_fulnam']);
		}else{
			if($value === "yes"){
				$property['datatype']	= Datatype::BOOLEAN;
				$property['value_mxd']	= true;
	
			}elseif($value === "no"){
				$property['datatype']	= Datatype::BOOLEAN;
				$property['value_mxd']	= false;
			}
		}
		if($property['datatype'] !== Datatype::STRING AND $property['name_fulnam'] === "") $property['name_fulnam'] = $property['datatype'];
		
		return $property;
	}
	

	/**
	 * Takes in a phrase and fixes the spelling of common spelling mixtakes
	 * and returns the fixed phrase
	 * @param string $string
	 * @return string
	 */
	private function fixSpelling($string){
		$string = str_replace(' & ', 			' and ',		$string);
		$string = str_replace('acroprop', 		'acrow prop',	$string);
		$string = str_replace('bi fold', 		'bi-fold',		$string);
		$string = str_replace('crow bar', 		'crowbar',		$string);
		$string = str_replace('chain saw', 		'chainsaw',		$string);
		$string = str_replace('excxavator', 	'excavator',	$string);
		$string = str_replace('furiture', 		'furniture',	$string);
		$string = str_replace('flexdrive', 		'flexi-drive',	$string);
		$string = str_replace('flexidrive', 	'flexi-drive',	$string);
		$string = str_replace('flexi drive', 	'flexi-drive',	$string);
		$string = str_replace('fly wheel', 		'flywheel',		$string);
		$string = str_replace('hight', 			'high',			$string);
		$string = str_replace('lenght', 		'length', 		$string);
		$string = str_replace('panle', 			'panel', 		$string);
		$string = str_replace('pedistal', 		'pedestal', 	$string);
		$string = str_replace('rptation', 		'rotation', 	$string);
		$string = str_replace('skilsaw', 		'skillsaw',		$string);
		$string = str_replace('scissorlift', 	'scissor lift',	$string);
		$string = str_replace('tarpouline', 	'tarpaulin',	$string);
		$string = str_replace('tea spoon', 		'teaspoon',		$string);
		$string = str_replace('x box', 			'xbox',			$string);
		$string = str_replace('wall paper', 	'wallpaper',	$string);
		$string = str_replace('wheel barrow', 	'wheelbarrow', 	$string);
		$string = str_replace('widht', 			'width', 		$string);
		return $string;
	}
	
	public function __construct(){
		$this->propertyAliases = array_map('str_getcsv', file(__DIR__.'/PropertyAliases.csv'));
	}
	

	/**
	 * Throws an exception that displays the problem a bit better when decoding json
	 * @param unknown $json
	 * @throws \Exception
	 * @return mixed
	 */
	public function jsonDecode($json){
		$array = json_decode($json);
		if(! $array){
			file_put_contents('/tmp/test.json', $json);
			exec('jsonlint /tmp/test.json 2> /tmp/result.jsonlint');
			$result = file_get_contents('/tmp/result.jsonlint');
			unlink('/tmp/test.json');
			unlink('/tmp/result.jsonlint');
			throw new \Exception($result);
		}
		return $array;
	}
	/**
	 * This fetches an image of a crawled site and puts it into the /public/img/assets/ folder
	 * @param string $url
	 * @return boolean
	 */
	public function syncImage($url, $type="assets"){
		if($this::CREATE_IMAGES AND $url !== null AND $url !== ""){
			$urlComponents = parse_url($url);
			if(isset($urlComponents['host']) AND isset($urlComponents['path'])){
				$localImageRelativePath = $urlComponents['host'].$urlComponents['path'];
				$localImage = __DIR__.'/../../../../../public/img/'.$type.'/'.$localImageRelativePath;
				if($this::UPDATE_IMAGES OR !file_exists($localImage)){
					$directory = dirname($localImage);
					$this->mkdir($directory);
					exec("cd $directory; wget -N ".addslashes($url));
					if(file_exists($localImage)) return $localImageRelativePath;
				}else{
					if($this::UPDATE_IMAGES) return null;
					else 			return $localImageRelativePath;
				}
			}
		}
		return null;
	}
	
	/**
	 * Gets the latitude and longitude from the scraped site.
	 * 	This can either be a string 				- in which case we will ask google for the lat and long
	 * 	or it can be the explicit lat and long	- in which case we will just return it
	 * @param string|\stdClass $location
	 * @return \stdClass
	 */
	public function getLatitudeAndLongitude($location){
		if (is_string($location)) return $this->getLatitudeAndLongitudeFromAddress($location);
		else return $location;
	}
	
	private function getLatitudeAndLongitudeFromAddress($physicalAddress){
		if($this::GENERATE_RANDOM_LOCATIONS){
			$latLong = new \stdClass();
			$latLong->lat  = -36.862043 + rand(-10,10)/300;
			$latLong->long = 174.761066 + rand(-10, 10)/500;
			return $latLong;
		}
		usleep(100000); // No more than 10 requests/second
		$json = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode(trim($physicalAddress)).'&key='.$this::GOOGLE_API_KEY));
		if(count($json->results) === 0){
			usleep(100000); // No more than 10 requests/second
			$json = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode(trim($physicalAddress, ' 0123456789')).'&key='.$this::GOOGLE_API_KEY));
		}
		while(count($json->results) === 0 AND strpos($physicalAddress, ",")){
			$lastSpacePosition = strrpos(rtrim($physicalAddress, ", ")," ");
			$physicalAddress =substr($physicalAddress,0,$lastSpacePosition);// Remove the last word
			usleep(100000); // No more than 10 requests/second
			$json = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode(trim($physicalAddress)).'&key='.$this::GOOGLE_API_KEY));
		}
		if(count($json->results) === 0) exit('Google could not determine the address:"'.$physicalAddress.'" at: https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($physicalAddress).'&key='.$this::GOOGLE_API_KEY) .' because: '.$json->error_message;
		$latLong = new \stdClass();
		$latLong->lat  = $json->results[0]->geometry->location->lat;
		$latLong->long = $json->results[0]->geometry->location->lng;
		return $latLong;
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
			// var_dump($imagePathParts);
			if(strlen($imagePathParts[count($imagePathParts) - 1]) > 4){ // The image doesn't have an extension
				$newImagePath = $imagePath . "_".$x."x".$y;
			} else {
				$newImagePath = implode('.', array_splice($imagePathParts, 0, -1))."_".$x."x".$y.".".end($imagePathParts);
			}
			if(!file_exists($newImagePath) OR $this::UPDATE_IMAGES){
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
	 * This puts a border around a favicon.ico and will also generate a marker for google maps.
	 * The marker for google maps is the same, except that it ends in ico_marker.ico
	 * @param string $iconPath
	 * @return NULL|string
	 */
	public function createIcons($iconPath){
		if($iconPath !== null){
			$iconDir = __DIR__.'/../../../../../public/img/lessors/';
			$iconPathParts = explode('.', $iconPath);
			$newIconPath = implode('.', array_splice($iconPathParts, 0, -1))."_18x18.".end($iconPathParts);
			if(!file_exists($iconDir.$newIconPath) OR $this::UPDATE_IMAGES){
				// Resize the image to the desired size of 16x16
				exec("convert -define jpeg:size=32x32 $iconDir".escapeshellarg($iconPath)." -thumbnail 16x16^ -gravity center -extent 16x16 $iconDir".escapeshellarg($newIconPath));
				// Now combine the image with the white marker to create a marker
				exec("convert ".$iconDir."white.ico_marker.ico $iconDir".escapeshellarg($newIconPath)." -geometry 16x16+1+1 -compose over -composite $iconDir".escapeshellarg($newIconPath)."_marker.ico");
				// And combine the image to create a bordered icon
				exec("convert ".$iconDir."white.ico $iconDir".escapeshellarg($newIconPath)." -gravity center -compose over -composite $iconDir".escapeshellarg($newIconPath));
			}
			return $newIconPath;
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
	
	
	
	
	
	/**
	 * This routine attempt to extract out properties from the title of the asset.
	 * It will not create a property, if the property already exists.
	 * It will also adjust the title and remove any text that was used to decode the property
	 * @todo psh
	 * @param string $phrase
	 * @param array $existingProperties - properties to check before creating a new one
	 * @return array - the properties has
	 */
	public function extractPropertiesFromTitle(&$title, $mainProperties, $existingProperties){
		
	}
	private function extractPropertiesFromTitleInternal(&$title, $mainProperties, $existingProperties){
		$titleIn = $title;
		// Extract out min and max, eg: "Ladder Extension 7-9m"
		if(preg_match('/([0-9.]+)[\s]*-[\s]*([0-9.]+)([a-zA-Z]+)/', $titleIn, $result)){
			
		}
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
			if($propertyAlias[0] === $categoryName AND $this->isSamePropertyName(strtolower($propertyName), strtolower($propertyAlias[1]))){
				return $propertyAlias[2];
			}
		}
		return $propertyName;
	}
	private function isSamePropertyName($propertyName, $propertyAlias){
		if($propertyName === $propertyAlias) return true;
		else{
			$shortPropertyName = str_replace('maximum', 'max', $propertyName);
			$shortPropertyName = str_replace('minimum', 'min', $propertyName);
			if($shortPropertyName === $propertyAlias) 	return true;
			else 										return false;
		}
	}
	
	private function fixValue($string){
		$string = str_replace('approx.', 	'', 	$string);
		$string = str_replace('(approx)', 	'', 	$string);
		if(preg_match('/([0-9\-.]+.*)\([0-9\-.]+.*\)/', $string, $result)){ // If there is a metric unit and imperial unit. One of them is in brackets
			return trim($result[1]);
		}
		return $string;
	}
	
	public function determineProperties($properties, $categoryName){
		$propertiesOut = array();
		foreach($properties as $propertyName => $propertyValue){
			$propertiesOut = array_merge($propertiesOut, $this->determinePropertyWrapper($propertyName, $propertyValue, $categoryName));
		}
		
		// Now go and fix the property names using the PropertyAliases.csv
		foreach($this->propertyAliases as $propertyAlias){
			if($propertyAlias[0] === $categoryName AND $categoryName =="ladder"){
				$foundAnotherPropertyWithFixedName = false;
				foreach($propertiesOut as $siblingProperty){
					if($this->isSamePropertyName(strtolower($siblingProperty['name_fulnam']), strtolower($propertyAlias[2]))){
						$foundAnotherPropertyWithFixedName = true;
					}
				}
				// If we haven't found another property with this fixed name, then try and fix this one
				if(!$foundAnotherPropertyWithFixedName){
					foreach($propertiesOut as $i => $property){
						if($this->isSamePropertyName(strtolower($property['name_fulnam']), strtolower($propertyAlias[1]))){
							$propertiesOut[$i]['name_fulnam'] = $propertyAlias[2];
						}
					}
				}
			}
		}
		return $propertiesOut;
	}
	
	private function determineRate($timePeriod, $costForPeriod){
		if($timePeriod === $costForPeriod) 	$result = $this->extractRateFromString($timePeriod);
		else 								$result = array("duration_hrs" => $timePeriod, "price_dlr" => $costForPeriod);
		if($result === null) return false;

		// Now get the money
		if(preg_match('/\$([0-9.]+)/', $result["price_dlr"], $pregMatch)){
			$result["price_dlr"] = floatval($pregMatch[1]);
		}else{
			$result["price_dlr"] = floatval($result["price_dlr"]);
		}
		if ($result["price_dlr"] == 0) return false;
		
		// Get the time period
		$timePeriod = strtolower(trim($result["duration_hrs"]));
		if    ($timePeriod == "full month") 	$result["duration_hrs"] = 24 * 30;
		elseif($timePeriod == "monthly") 		$result["duration_hrs"] = 24 * 30;
		elseif($timePeriod == "month hire")		$result["duration_hrs"] = 24 * 30;
		elseif($timePeriod == "fortnightly") 	$result["duration_hrs"] = 24 * 14;
		elseif($timePeriod == "fortnight hire")	$result["duration_hrs"] = 24 * 14;
		elseif($timePeriod == "p/week") 		$result["duration_hrs"] = 24 * 7;
		elseif($timePeriod == "full week") 		$result["duration_hrs"] = 24 * 7;
		elseif($timePeriod == "weekly") 		$result["duration_hrs"] = 24 * 7;
		elseif($timePeriod == "week hire")		$result["duration_hrs"] = 24 * 7;
		elseif($timePeriod == "full day") 		$result["duration_hrs"] = 24;
		elseif($timePeriod == "daily") 			$result["duration_hrs"] = 24;
		elseif($timePeriod == "day hire")		$result["duration_hrs"] = 24;
		elseif($timePeriod == "half day") 		$result["duration_hrs"] = 12;
		elseif($timePeriod == "1/2 day") 		$result["duration_hrs"] = 12;
		elseif($timePeriod == "quick hire")		$result["duration_hrs"] = 12;
		else{
			return array("datatype" => Datatype::STRING, "value_mxd" => $result["price_dlr"], "name_fulnam" => $timePeriod);
		}
		
		return $result;
	}
	
	public function determineRates($rates){
		$ratesOut = array();
		foreach($rates as $timePeriod => $costForPeriod){
			$rate = $this->determineRate($timePeriod, $costForPeriod);
			if($rate) array_push($ratesOut, $rate);
		}
		return $ratesOut;
	}
	private function extractRateFromString($string){
		$result = array("price_dlr" => $string, "duration_hrs" => $string);
		
		$string = strtolower($string);
		if($string == "poa") return null;
		else throw new \Exception("Please write the code that will extract out the time and cost from: '$string'");
	}
	
	private function determinePropertyWrapper($key, $value, $categoryName){
		$results = $this->determinePropertiesInternal($key, $value, $categoryName);
		if(count($results) === 1 AND $key === $value){ // If we have not done anything except make it lower case, then revert the determineProperties
			$result = $results[0];
			if($result['datatype'] === Datatype::STRING){
				if(strtolower($result['value_mxd']) === trim($key)){
					$result['value_mxd'] = ucfirst(trim($this->fixSpelling($key)));
					return array($result);
				}else{
					$result['value_mxd'] = ucfirst($this->fixSpelling($result['value_mxd']));
					return array($result);
				}
			}
		}
		return $results;
	}
	
	
	/**
	 * Determines the properties that are for an asset
	 * @param string $key
	 * @param string $value
	 * @param string $categoryName
	 * @return array 
	 */
	private function determinePropertiesInternal($key, $value, $categoryName){
		if($key === $value) $key = "";
		$key   = trim(strtolower($key),   ":- ");
		$value = trim(strtolower($value), ": ");
	
		// If there is a min and max in the value, then strip them out.
		if($this->isIn($value, "[0-9].* to .*[0-9]")){
			if (!strpos($key, ' ')) $key = Porter::Stem($key);
			$twoNumbers = explode(" to ", $value, 2);
			$min = $twoNumbers[0];
			$max = $twoNumbers[1];
			// If the min or max has the units, then ensure that the other one is updated too
			if(preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $max, $maxUnits) AND !preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $min, $minUnits) AND floatval($min) == $min){
				$min = $min.$maxUnits[1];
			}elseif(preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $min, $minUnits) AND !preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $max, $maxUnits) AND floatval($max) == $max){
				$max = $max.$minUnits[1];
			}
			return array($this->determineProperty("min ".$key, $min, $categoryName), $this->determineProperty("max ".$key, $max, $categoryName));
				
		}elseif($this->isIn($value, '[0-9.]+.*- *[0-9.]+') AND $this->isIn($key, ' range')){
			$key = str_replace('range', 'bound', $key);
			$twoNumbers = explode("-", $value, 2);
			$min = trim($twoNumbers[0]);
			$max = trim($twoNumbers[1]);
			// If the min or max has the units, then ensure that the other one is updated too
			if(preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $max, $maxUnits) AND !preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $min, $minUnits) AND floatval($min) == $min){
				$min = $min.$maxUnits[1];
			}elseif(preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $min, $minUnits) AND !preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $max, $maxUnits) AND floatval($max) == $max){ 
				$min = $min.$maxUnits[1];
			}
			return array($this->determineProperty("min ".$key, $min, $categoryName), $this->determineProperty("max ".$key, $max, $categoryName));
		}elseif($this->isIn($value, '[0-9.]+.*- *[0-9.]+') AND !strpos($key, ' ')){
			$key = Porter::Stem($this->fixSpelling($key));
			$twoNumbers = explode("-", $value, 2);
			$min = trim($twoNumbers[0]);
			$max = trim($twoNumbers[1]);
			// If the min or max has the units, then ensure that the other one is updated too
			if(preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $max, $maxUnits) AND !preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $min, $minUnits) AND floatval($min) == $min){ // If the max has the units, then put the units onto the min as well
				$min = $min.$maxUnits[1];
			}elseif(preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $min, $minUnits) AND !preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $max, $maxUnits) AND floatval($max) == $max){ // If the max has the units, then put the units onto the min as well
				$min = $min.$maxUnits[1];
			}
			return array($this->determineProperty("min ".$key, $min, $categoryName), $this->determineProperty("max ".$key, $max, $categoryName));
		}
		else return array($this->determineProperty($key, $value, $categoryName));
	}

	/**
	 * Determines the category by looking at categories.js file
	 * @param CategoryAliases $category
	 * @param string $name
	 * @return Ambigous Category|NULL
	 */
	public function determineCategory($category, $name){
		$name = $this->fixSpelling(strtolower($name));
		if($matchedCategory = $this->determineCategoryExactMatch($category, $name)) 	return array_values($matchedCategory)[0];
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
		if(property_exists($category, 'aliases') AND !property_exists($category, 'children')) {
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
	 * If there are multiple results, then it will return the alias that matches with the longest name
	 *
	 * @param Category $category
	 * @param string $name
	 * @return array(string => Category)|NULL
	 */
	private function determineCategoryExactMatch($category, $name){
		$matchedAliases = array();
		if(property_exists($category, 'children')) {
			foreach($category->children as $subCategory){
				$result = $this->determineCategoryExactMatch($subCategory, $name);
				if($result !== null){
					$key = key($result);
					$matchedAliases[$key] = $result[$key];
				}
			}
		}
		if(property_exists($category, 'aliases') AND !property_exists($category, 'children')) {
			foreach($category->aliases as $alias){
				if($this->isIn(strtolower($name), strtolower($alias))){
					$matchedAliases[$alias] = $category;
				}
			}
		}
		
		// Find and return the longest alias
		$longestAlias = "";
		foreach($matchedAliases as $alias => $categoryForAlias){
			if(strlen($alias) > strlen($longestAlias)) $longestAlias = $alias;
		}
		if($longestAlias !== "") 	return array($longestAlias => $matchedAliases[$longestAlias]);
		else 						return null;
	}
}
?>
