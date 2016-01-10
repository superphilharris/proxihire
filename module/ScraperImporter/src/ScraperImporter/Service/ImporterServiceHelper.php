<?php
namespace ScraperImporter\Service;

use Application\Model\Datatype;

class ImporterServiceHelper implements ImporterServiceHelperInterface {
	// The below 3 configurations are used to speed up the scraping for testing purposes.
	const UPDATE_IMAGES       = FALSE; // Whether we want to check to see whether they've changed the images on their server.
	private $propertyAliases  = array();
	private $isCategorizeOnly = false; // Turn on if we are overusing the google api. Set to TRUE to speed up.
	const GOOGLE_API_KEY = "AIzaSyD6QGNeko6_RVm4dMCRdeQhx8oLb24GGxk";
	
	
	/**
	 * Determines the property name and value, using the name and value pair.
	 *
	 * Returns an array with the following elements
	 *
	 * - `datatype` : The standardized string used to define what type of 
	 *   property this is. For example, 'angle' or 'lineal'. This is done by 
	 *   extracting the units from the property value and comparing it to a list 
	 *   of possible unit types.
	 * - `name_fulnam` : the name of this property as specified by `$key`
	 * - `value_mxd` : The value of this property for this asset, cleaned up from 
	 *   `$value`
	 *
	 * @param string $key   - The scraped property name
	 * @param string $value - The scraped property value
	 * @return array        - the indexes are name_fulnam, datatype, and 
	 *                        value_mxd
	 */
	private function determineProperty($key, $value){
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
			}elseif($unit === 'ft'    OR $unit === "'"){
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
			}elseif($unit === 'kw' OR $unit === 'kva'){
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
			}elseif($unit === 'm3/min' OR $unit === 'm3/mim'){
				$property['datatype']  = Datatype::FLOW;
				$property['value_mxd'] = floatval($number) * 1000 / 60; // Convert to ltr/sec
			}elseif($unit === 'cfm'){
				$property['datatype']  = Datatype::FLOW;
				$property['value_mxd'] = floatval($number) * 0.471947443; // Convert to ltr/sec
			}elseif($unit === 'axle'){
				$property['datatype']  = Datatype::INTEGER;
				$property['value_mxd'] = floatval($number);
	
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
		$string = str_replace(' & ',          ' and ',      $string);
		$string = str_replace('acroprop',     'acrow prop',   $string);
		$string = str_replace('bi fold',      'bi-fold',      $string);
		$string = str_replace('crow bar',     'crowbar',      $string);
		$string = str_replace('chain saw',    'chainsaw',     $string);
		$string = str_replace('dimention',    'dimension',    $string);
		$string = str_replace('excxavator',   'excavator',    $string);
		$string = str_replace('furiture',     'furniture',    $string);
		$string = str_replace('flexdrive',    'flexi-drive',  $string);
		$string = str_replace('flexidrive',   'flexi-drive',  $string);
		$string = str_replace('flexi drive',  'flexi-drive',  $string);
		$string = str_replace('fly wheel',    'flywheel',     $string);
		$string = str_replace('hight',        'high',         $string);
		$string = str_replace('lenght',       'length',       $string);
		$string = str_replace('panle',        'panel',        $string);
		$string = str_replace('pedistal',     'pedestal',     $string);
		$string = str_replace('rptation',     'rotation',     $string);
		$string = str_replace('skilsaw',      'skillsaw',     $string);
		$string = str_replace('scissorlift',  'scissor lift', $string);
		$string = str_replace('tarpouline',   'tarpaulin',    $string);
		$string = str_replace('tea spoon',    'teaspoon',     $string);
		$string = str_replace('x box',        'xbox',         $string);
		$string = str_replace('wall paper',   'wallpaper',    $string);
		$string = str_replace('wheel barrow', 'wheelbarrow',  $string);
		$string = str_replace('widht',        'width',        $string);
		return $string;
	}
	
	private function determinePhoneNumber($location, $locale=null){
		$phoneNumber = null;
		if($this->propertyExists($location, 'phone_number') AND trim($location->phone_number) !== ''){
			$phoneNumber = trim($location->phone_number);
			if(strpos($phoneNumber, '+64') === 0){
				return preg_replace("/[^0-9+]/", '', $phoneNumber);
			}elseif($locale === 'nz'){
				if(strpos($phoneNumber, '64') === 0){ // Badly formatted phone number
					return '+' . preg_replace("/[^0-9]/", '', $phoneNumber); 
				}elseif(strpos($phoneNumber, '0') === 0){
					return '+64' . preg_replace("/[^0-9]/", '', substr($phoneNumber, 1));
				}
			}
			throw new \Exception("Do not know how to deal with phone numbers like: '$location->phone_number'.\nPlease add the country locale code to your scraper lessor and ensure that this routine deals with your locale.");
		}
		return null;
	}
	
	
	public function __construct($isCategorizeOnly=false){
		$this->propertyAliases = array_map('str_getcsv', file(__DIR__.'/PropertyAliases.csv'));
		$this->isCategorizeOnly = $isCategorizeOnly;
	}
	

	/**
	 * {@inheritdoc}
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
	 * {@inheritdoc}
	 */
	public function syncImage($url, $type="assets"){
		if(!$this->isCategorizeOnly AND !empty($url)){
			$urlComponents = parse_url($url);
			if(isset($urlComponents['host']) AND isset($urlComponents['path'])){
				$localImageRelativePath = $urlComponents['host'].$urlComponents['path'];
				$localImage = __DIR__.'/../../../../../public/img/'.$type.'/'.$localImageRelativePath;
				if($this::UPDATE_IMAGES OR !file_exists($localImage)){
					$directory = dirname($localImage);
					$this->mkdir($directory);
					exec("cd $directory; wget -N ".addslashes($url));
				}
				if(file_exists($localImage)){
					if(filesize($localImage) > 100) return $localImageRelativePath;
					else 							unlink($localImage);
				}
			}
		}
		return null;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function determineBranch($location, $lessor){
		$locale = ($this->propertyExists($lessor, 'locale'))? $lessor->locale : null;
		$branch = $this->getLatitudeAndLongitude($location);
		$branch->email        = ($this->propertyExists($lessor, 'email'))?        $lessor->email        : null;
		$branch->phone_number = ($this->propertyExists($lessor, 'phone_number'))? $lessor->phone_number : null;
		$branch->name         = ($this->propertyExists($lessor, 'name'))?         preg_replace('!\s+!', ' ', $lessor->name) : null;
		if(!is_string($location)){
			if($this->propertyExists($location, 'email')) $branch->email = $location->email;
			if($this->propertyExists($location, 'name'))  $branch->name  = preg_replace('!\s+!', ' ', $location->name);
			$branch->phone_number = $this->determinePhoneNumber($location, $locale);
			
			if((($branch->email === null AND !property_exists($location, 'email')) OR $branch->phone_number === null) AND $branch->name != null AND !$this->isCategorizeOnly) {
				$bingsBranch = $this->determineBranchFromBing(preg_replace('/[^0-9A-Za-z ]/', '', $branch->name));
				if(isset($bingsBranch['email']) AND $branch->email === null)       $branch->email = $bingsBranch['email'];
				if(isset($bingsBranch['phone']) AND $branch->phone_number === null) $branch->phone_number = $bingsBranch['phone'];
			}
		}
		return $branch;
	}
	
	
	/**
	 * This searches up a branch's contact details using bing, (google does not allow it easily)
	 * then searches the page for a string with a '@' inside it,
	 * and searches the page for an 0800 number, or another recognizable phone number format.
	 * @param string $branchName
	 * @return object with email and phone_number
	 */
	private function determineBranchFromBing($branchName){
		// 1. Search Bing for the name and contact
		$bingResults = file_get_contents('http://www.bing.com/search?q='.urlencode($branchName.' email phone number'));
		$anchorEnd = 0;
		$allAppropriateUrls = array();
		
		// 2. Rank all the results
		$subRank = 9;
		while(($anchorStart = strpos($bingResults, '<a href="', $anchorEnd)) > 0){
			$anchorEnd = strpos($bingResults, '"', $anchorStart + 10);
			$url = substr($bingResults, $anchorStart+9, $anchorEnd - $anchorStart - 9);
			if(strpos($url, 'http') === 0 AND count($allAppropriateUrls) <= 5){
				$rank = 0;
				$textStart  = strpos($bingResults, '<strong>', $anchorEnd);
				$textEnd 	= strpos($bingResults, '</strong>', $textStart);
				$linkText 	= strtolower(substr($bingResults, $textStart + 8, $textEnd - $textStart - 8));
				foreach(explode(' ', strtolower($branchName)) as $branchNameWord){
					if($this->isIn($linkText, $branchNameWord)) $rank += 2;
				}
				
				// Give higher priority to ones with the name in the domain
				$endOfDomainName = strpos($url, '/', 8);
				if($endOfDomainName === false) $endOfDomainName = strlen($url);
				$domainName = substr($url, 0, $endOfDomainName);
				foreach(explode(' ', strtolower($branchName)) as $branchNameWord){
					if($this->isIn($domainName, strtolower($branchNameWord))) $rank += 2;
					elseif($this->isIn($url, strtolower($branchNameWord))) $rank += 1;
				}
				$allAppropriateUrls[$rank.'.'.$subRank] = $url;
				$subRank --;
				if($subRank < 0) break;
			}
		}
		ksort($allAppropriateUrls, SORT_NUMERIC);
		$allAppropriateUrls = array_reverse($allAppropriateUrls);
		
		// 3. Now for each of the results, lets see if they have the phone number / email address
		foreach($allAppropriateUrls as $url){
			$phoneAndEmail = $this->determineBranchFromUrl($url);
			if(count($phoneAndEmail) > 0) return $phoneAndEmail;
		}
	}
	
	private function determineBranchFromUrl($url){
		$webPage = @file_get_contents($url);
		if($webPage === FALSE) return array();
		$phoneNumbers = array();
		$phoneAndEmail = array();
		// 1. Find all Emails, and sort them based on their relevance
		$emails = array();
		$subRank = 9999;
		if(preg_match_all('/[0-9a-zA-Z!#$%&\*+\-=?^_`}{|~]+@[a-zA-Z0-9.]+/', $webPage, $matches)){
			foreach($matches[0] as $email){
				$rank = 0;
				if($this->isIn($email, 'hire')) 	$rank += 3;
				if($this->isIn($email, 'contact')) 	$rank += 2;
				if($this->isIn($email, 'info')) 	$rank += 2;
				if($this->isIn($email, 'admin')) 	$rank += 1;
				$emails[$rank.'.'.$subRank] = $email;
				$subRank --;
			}
			ksort($emails, SORT_NUMERIC);
			$emails = array_reverse($emails);
			$phoneAndEmail['email'] = array_values($emails)[0];
			

			// 2. Try and find a phone number
			if(preg_match('/0800[0-9 \-]+/', $webPage, $phoneMatches)) 		$phoneAndEmail['phone'] = $phoneMatches[0];
			elseif(preg_match('/0508[0-9 \-]+/', $webPage, $phoneMatches)) 	$phoneAndEmail['phone'] = $phoneMatches[0];
			elseif(preg_match('/\+64[0-9 \-]+/', $webPage, $phoneMatches)) 	$phoneAndEmail['phone'] = $phoneMatches[0];
			elseif(preg_match('/0064[0-9 \-]+/', $webPage, $phoneMatches)) 	$phoneAndEmail['phone'] = $phoneMatches[0];
			elseif(preg_match('/0[0-9 \-]+/', $webPage, $phoneMatches)) 	$phoneAndEmail['phone'] = $phoneMatches[0];
		}
		return $phoneAndEmail;
	}
	
	/**
	 * Gets the latitude and longitude for the passed argument.
	 *
	 * If the object is
	 *
	 * - A string : we will assume that it is an address ask google for the lat and long
	 * - An object : we will simply return it
	 *
	 * @param string|\stdClass $location
	 * @return \stdClass
	 */
	private function getLatitudeAndLongitude($location){
		if (is_string($location)) 						return $this->getLatitudeAndLongitudeFromAddress($location);
		elseif ($this->propertyExists($location, 'address')){
			if(is_string($location->address))			return $this->getLatitudeAndLongitudeFromAddress($location->address);
			else										return $location->address;
		}else 											return $location;
	}
	
	private function getLatitudeAndLongitudeFromAddress($physicalAddress){
		if($this->isCategorizeOnly){
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
	 * {@inheritdoc}
	 */
	public function resizeAndCropImage($imagePath, $x=120, $y=120){
		if($this->isCategorizeOnly) return null;
		if($imagePath !== null){
			$imagePathParts = explode('.', $imagePath);
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
	 * {@inheritdoc}
	 */
	public function createIcons($iconPath){
		if($this->isCategorizeOnly) return null;
		$iconDir = __DIR__.'/../../../../../public/img/lessors/';
		if($iconPath !== null AND file_exists($iconDir.$iconPath)){
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
			if(!file_exists($iconDir.$newIconPath))			return null;
			elseif(filesize($iconDir.$newIconPath) < 100)	unlink($iconDir.$newIconPath);
			else 											return $newIconPath;
		}
		return null;
	}
	/**
	 * Recursively makes a directory.
	 * As php one doesn't seem to work recursively
	 *
	 * psh: PHP can do it, you just need to set the `recursive` argument. So 
	 * something like
	 *
	 *     mkdir( $dirname, 0777, true );
	 *
	 * should do it
	 *
	 * @param string $dir
	 * @return boolean
	 */
	private function mkdir($dir){
		if(file_exists($dir)) 				return true;
		elseif(file_exists(dirname($dir))){
			if(!mkdir($dir)) 				throw new \Exception("Could not create directory. Please run: `sudo chown -R www-data:www-data ".dirname($dir));
			else 							return true;
		}else								return $this->mkdir(dirname($dir));
		return false;
	}
	
	
	
	
	
	/**
	 * This routine attempts to extract out properties from the title of the 
	 * asset.
	 *
	 * @param  string $assetName      - the scraped asset name
	 * @param  array  $mainProperties - the properties that we'd expect from this 
	 *                                  asset.
	 * @return array                  - the properties; extracted from the asset 
	 *                                  name, and in a standardized form.
	 */
	private function extractPropertiesFromAssetName($assetName, $mainProperties){
		if($this->isCategorizeOnly) return array();
		$fixedProperties = array();
		// Extract out min and max, eg: "Ladder Extension 7-9m"
		if(count($mainProperties) > 0) {
			$extractedProperties = null;
			// Try different regex's to extract out common patterns
			if(property_exists($mainProperties, 'length') AND property_exists($mainProperties, 'width') AND preg_match("/[0-9].* x [0-9.]+\s*[^\s]+/", $assetName, $result)) { // 4m x 6m
				$extractedProperties = $this->determinePropertiesInternal("__key__", $result[0]);
			}elseif(property_exists($mainProperties, 'length') AND property_exists($mainProperties, 'width') AND preg_match("/[0-9.]+x[0-9.]+\s*[^\s]+/", $assetName, $result)) { // 4x6m
				$extractedProperties = $this->determinePropertiesInternal("__key__", $result[0]);
			}elseif(preg_match("/([0-9].*)\((.*[0-9].*)\)/", $assetName, $result)){					// Try: 2.4 meters (8')
				$extractedProperties = $this->determinePropertiesInternal("__key__", $result[1]);
			}elseif(preg_match("/([0-9.]+\s*[^0-9.]+)\s+([0-9.]+\s*[^\s]+)/", $assetName, $result)){// Try 6m 20'
				$extractedProperties = $this->determinePropertiesInternal("__key__", $result[1]);
			}elseif(preg_match("/([0-9].+[0-9]\s*[^\s]+)/", $assetName, $result)){					// Try: 7-9m
				$extractedProperties = $this->determinePropertiesInternal("__key__", $result[0]);
			}elseif(preg_match("/[0-9.]+\s*[^\s]+/", $assetName, $result)){							// Try: 4psi
				$extractedProperties = $this->determinePropertiesInternal("__key__", $result[0]);
			}elseif(preg_match("/[0-9.][^\s]+/", $assetName, $result) AND count($mainProperties) === 1){	// Try: 5
				$extractedProperties = $this->determinePropertiesInternal("__key__", $result[0]); // TODO: should we specify the default unit in the categories.js?
			}
			if($extractedProperties !== null){
				// Now see whether they match our expected 
				foreach($extractedProperties as $extractedProperty){
					// If our extraction has not determined the property
					if($this->isIn($extractedProperty['name_fulnam'], '__key__')){ 
						$foundProperty = null;
						foreach($mainProperties as $mainPropertyName => $mainPropertyDatatype){
							if($extractedProperty['datatype'] === $mainPropertyDatatype){
								if($mainPropertyName === '__key__'){
									$extractedProperty['name_fulnam'] = $mainPropertyName;
									$foundProperty = $mainPropertyName;
								}else{
									$extractedProperty['name_fulnam'] = str_replace('__key__', $mainPropertyName, $extractedProperty['name_fulnam']);
								}
								$fixedProperties[$extractedProperty['name_fulnam']] = $extractedProperty;
								break;
							}
						}
						if($foundProperty !== null){ // Ensure that we do not match a property twice
							$mainProperties->{$foundProperty} = null;
						}
						
					// Our extraction has determined the property
					}else{ 
						$fixedProperties[$extractedProperty['name_fulnam']] = $extractedProperty;
					}
				}
			}
		}
		
		return $fixedProperties;
	}
	
	private function convertIntegersInString($string){
		$mappings = array(
			"one"      => 1,
			"two"      => 2,
			"three"    => 3,
			"four"     => 4,
			"five"     => 5,
			"six"      => 6,
			"seven"    => 7,
			"eight"    => 8,
			"nine"     => 9,
			"ten"      => 10,
			"eleven"   => 11,
			"single"   => 1,
			"double"   => 2,
			"tandem"   => 2,
			"triple"   => 3
		);
		$factors = array(
			"ten"      => 10,
			"hundred"  => 100,
			"thousand" => 1000,
			"million"  => 1000000,
			"billion"  => 1000000000
		);
		
	}
	
	/**
	 * Searches for an instance of the $neeedle within all elements of $haystack
	 *
	 * @param $haystack - Either a string or an array of strings. If any instance 
	 *                    of $needle is in this, then count it as a match.
	 * @param $needle   - A regular expression to search for
	 * @return boolean  - True if a match found
	 */
	private function isIn($haystack, $needle){
		return preg_match("/".$needle."/", $haystack);
	}
	private function propertyExists($value, $key){
		return (property_exists($value, $key) AND trim($value->{$key}) != "");
	}
	
	/**
	 * Returns a length-2 array with the number and unit of a property.
	 *
	 * When scraping a property, if the property name and property value are 
	 * passed into this function, it will try to return a length-2 array, where 
	 * the first element is the property value, and the second element is the 
	 * units of measure, as specified by the site
	 *
	 * @param  $key   - The name of the property, as scraped from the site.
	 * @param  $value - The value of the property, as scraped from the site.
	 * @return array
	 */
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
	
	/**
	 * Compares a property to a potential property alias
	 *
	 * @param  $scrapedName   - The scraped name, once it has been cleaned up
	 * @param  $propertyAlias - The alias to try to match the scraped name 
	 *                          against
	 * @param  $propertyName  - The property name, which is returned if the alias 
	 *                          matches
	 * @return string         - The aliased name. NULL if not a match
	 */
	private function getSamePropertyName($scrapedName, $propertyAlias, $propertyName){
		$scrapedName   = strtolower( $scrapedName );
		$propertyAlias = strtolower( $propertyAlias );
		$propertyName  = strtolower( $propertyName );

		$scrapedName = str_replace('maximum', 'max', $scrapedName);
		$scrapedName = str_replace('minimum', 'min', $scrapedName);
		if($scrapedName === $propertyAlias)          return $propertyName;

		if( $scrapedName === "max ".$propertyAlias ) return "max ".$propertyName;
		if( $scrapedName === "min ".$propertyAlias ) return "min ".$propertyName;
		return NULL;
	}
	
	/**
	 * Cleans up the scraped property value
	 *
	 * When scraping a property from site, the property's value will often have 
	 * extraneous information that needs to be removed.
	 *
	 * - Any 'approx' will be removed
	 * - Often the value is given in both metric and imperial. If this is the 
	 *   case, only the value *not* inside brackets will be returned.
	 */
	private function fixValue($string){
		$string = str_replace('approx.', 	'', 	$string);
		$string = str_replace('(approx)', 	'', 	$string);
		if(preg_match('/([0-9\-.]+.*)\([0-9\-.]+.*\)/', $string, $result)){ // If there is a metric unit and imperial unit. One of them is in brackets
			return trim($result[1]);
		}
		return $string;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function determineProperties($properties, $categoryName, $assetName, $mainProperties){
		if($this->isCategorizeOnly) return array();
		$propertiesOut = $this->extractPropertiesFromAssetName($assetName, $mainProperties);
		foreach($properties as $propertyName => $propertyValue){
			$newProperties = $this->determinePropertyWrapper($propertyName, $propertyValue);
			foreach($newProperties as $newProperty){
				$propertiesOut[$newProperty['name_fulnam']] = $newProperty;
			}
		}
		
		// Now go and fix the property names using the PropertyAliases.csv
		foreach($this->propertyAliases as $propertyAlias){
			if($propertyAlias[0] === $categoryName){
				$foundAnotherPropertyWithFixedName = false;
				// First, see if this property matches the actual property name
				foreach($propertiesOut as $property){
					if(!is_null($this->getSamePropertyName($property['name_fulnam'], $propertyAlias[2], $propertyAlias[2]))){
						$foundAnotherPropertyWithFixedName = true;
					}
				}
				// If we haven't found another property with this fixed name, then try and fix this one
				if(!$foundAnotherPropertyWithFixedName){
					foreach($propertiesOut as $i => $property){
						$tmpName = $this->getSamePropertyName($property['name_fulnam'], $propertyAlias[1], $propertyAlias[2]);
						if( !is_null( $tmpName ) ){
							$propertiesOut[$i]['name_fulnam'] = $tmpName;
						}
					}
				}
			}
		}
		return array_values($propertiesOut);
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
		$timePeriod = trim(str_replace('hire', '', $timePeriod), ' :');
		if    ($timePeriod == "full month") 	$result["duration_hrs"] = 24 * 30;
		elseif($timePeriod == "monthly") 		$result["duration_hrs"] = 24 * 30;
		elseif($timePeriod == "month")			$result["duration_hrs"] = 24 * 30;
		elseif($timePeriod == "fortnightly") 	$result["duration_hrs"] = 24 * 14;
		elseif($timePeriod == "fortnight")		$result["duration_hrs"] = 24 * 14;
		elseif($timePeriod == "p/week") 		$result["duration_hrs"] = 24 * 7;
		elseif($timePeriod == "full week") 		$result["duration_hrs"] = 24 * 7;
		elseif($timePeriod == "weekly") 		$result["duration_hrs"] = 24 * 7;
		elseif($timePeriod == "week")			$result["duration_hrs"] = 24 * 7;
		elseif($timePeriod == "full day") 		$result["duration_hrs"] = 24;
		elseif($timePeriod == "daily") 			$result["duration_hrs"] = 24;
		elseif($timePeriod == "day")			$result["duration_hrs"] = 24;
		elseif($timePeriod == "half day") 		$result["duration_hrs"] = 12;
		elseif($timePeriod == "1/2 day") 		$result["duration_hrs"] = 12;
		elseif($timePeriod == "quick")			$result["duration_hrs"] = 12;
		else{
			// throw new \Exception("Please add the rate for '".$timePeriod."' to list of possible durations.");
			return array("datatype" => Datatype::STRING, "value_mxd" => $result["price_dlr"], "name_fulnam" => $timePeriod);
		}
		
		return $result;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function determineRates($rates){
		if($this->isCategorizeOnly) return array();
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
	
	/**
	 * Try to get the property from the scraped `propertyName`=>`propertyValue` 
	 * pair.
	 *
	 * This is a wrapper for the `determinePropertiesInternal` method, but it 
	 * also checks to see whether or not it was successful. If not, and the 
	 * datatype of the returned value was `STRING`, then
	 *
	 * 1. Checks to see whether the `$value` contains a `:`. If so, then it 
	 *    assumes that the `$value` contains both the key and value, and extracts 
	 *    it.
	 * 2. Checks to see whether the propertyName and propertyValue are the same. 
	 *    If so, we need to set the first character to upper case.
	 *
	 * @param  string $key          - The scraped name of the property
	 * @param  string $value        - The scraped value of the property
	 * @return array                - An array of all of the properties in a 
	 *                                standardized form.
	 */
	private function determinePropertyWrapper($key, $value ){
		$results = $this->determinePropertiesInternal($key, $value);
		if(count($results) === 1){
			$result = $results[0];
			if($result['datatype'] === Datatype::STRING){
				// If there is a colon, then the value could contain both the key 
				// and the value
				if(preg_match('/([A-Za-z].*)\:\s*([0-9].*)/', $value, $matches)){
					$newResults = $this->determinePropertiesInternal($matches[1], $matches[2]);
					if($newResults[0]['datatype'] !== Datatype::STRING) $results = $newResults;
				// If we have not done anything except make it lower case, then 
				// revert the determineProperties
				}elseif($key === $value){ 
					if(strtolower($result['value_mxd']) === trim($key)){
						$result['value_mxd'] = ucfirst(trim($this->fixSpelling($key)));
						return array($result);
					}else{
						$result['value_mxd'] = ucfirst($this->fixSpelling($result['value_mxd']));
						return array($result);
					}
				}
			}
		}
		return $results;
	}
	
	private function giveBothValuesTheSameUnit(&$value1, &$value2){
		if(preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $value1, $value1Units) AND !preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $value2, $value2Units) AND floatval($value2) == $value2){
			$value2 = $value2.$value1Units[1];
		}elseif(preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $value2, $value2Units) AND !preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $value1, $value1Units) AND floatval($value1) == $value1){
			$value1 = $value1.$value2Units[1];
		}
	}
	
	/**
	 * Determines the asset properties based on the scraped `property`=>`value` 
	 * pair
	 *
	 * The `$key` variable is simply cleaned up, then passed to the 
	 * `determineProperty` method.
	 *
	 * The `$value` property is compared against a variety of possible formats 
	 * before getting passed to the `determineProperty` method, which determines 
	 * the property type based on the units used. These possible formats are
	 *
	 *  - `L x W` : then *both* of these value are independently extracted using 
	 *    the determineProperty method. If either the length or the width have 
	 *    units specified, then the same units are used for both.
	 *    The length, width, and area properties are all returned.
	 *  - `A to B` : This is interpreted as minimum an maximum values. The 
	 *    two properties returned use the same metric, but one will have a 
	 *    property name `min <name>`, and the other will have a property name 
	 *    `max <name>`.
	 *  - `A - B` : This is also interpreted as minimum and maximum *if* the 
	 *    *name* also contains the string `range`, or the name is a single word.
	 *  - If the value seems to contain two separate values, and they seem to be 
	 *    measuring different things (eg. angle and depth), then we return both 
	 *    of them separately, but with the same name.
	 *  - Otherwise it's assumed that the property is just a simply `name` => 
	 *    `property` pair
	 * 
	 * @param  string $key   - The name of the scraped property
	 * @param  string $value - The value of the scraped property
	 * @return array 
	 */
	private function determinePropertiesInternal($key, $value){
		if($key === $value) $key = "";

		// trim any unwanted prefix/suffix
		$key   = trim(strtolower($key),   ":- \t\n\r\0\x0B");
		$value = trim(strtolower($value), ": \t\n\r\0\x0B");
	
		// If there is a min and max in the value, then strip them out.
		if($this->isIn($value, "[0-9].* to .*[0-9]")){
			if (!strpos($key, ' ')) $key = Porter::Stem($key);
			$twoNumbers = explode(" to ", $value, 2);
			$min = $twoNumbers[0];
			$max = $twoNumbers[1];
			// If the min or max has the units, then ensure that the other one is 
			// updated too
			if(preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $max, $maxUnits) AND !preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $min, $minUnits) AND floatval($min) == $min){
				$min = $min.$maxUnits[1];
			}elseif(preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $min, $minUnits) AND !preg_match('/[0-9\-.]+\s*([a-zA-Z\/ ]+)/', $max, $maxUnits) AND floatval($max) == $max){
				$max = $max.$minUnits[1];
			}
			return array($this->determineProperty("min ".$key, $min), $this->determineProperty("max ".$key, $max));
				
		}
		
		if($this->isIn($value, '[0-9.]+.*- *[0-9.]+') AND $this->isIn($key, ' range')){
			$key = str_replace('range', 'bound', $key);
			$twoNumbers = explode("-", $value, 2);
			$min = trim($twoNumbers[0]);
			$max = trim($twoNumbers[1]);
			// If the min or max has the units, then ensure that the other one is 
			// updated too
			if(preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $max, $maxUnits) AND !preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $min, $minUnits) AND floatval($min) == $min){
				$min = $min.$maxUnits[1];
			}elseif(preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $min, $minUnits) AND !preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $max, $maxUnits) AND floatval($max) == $max){ 
				$min = $min.$maxUnits[1];
			}
			return array($this->determineProperty("min ".$key, $min), $this->determineProperty("max ".$key, $max));
		}
		
		if( $this->isIn($value, '[0-9.]+.*- *[0-9.]+') ){
			$key = Porter::Stem($this->fixSpelling($key));
			$twoNumbers = explode("-", $value, 2);
			$min = trim($twoNumbers[0]);
			$max = trim($twoNumbers[1]);
			// If the min or max has the units, then ensure that the other one is 
			// updated too
			if(preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $max, $maxUnits) AND !preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $min, $minUnits) AND floatval($min) == $min){ // If the max has the units, then put the units onto the min as well
				$min = $min.$maxUnits[1];
			}elseif(preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $min, $minUnits) AND !preg_match('/[0-9.]+([a-zA-Z\/\s]+)/', $max, $maxUnits) AND floatval($max) == $max){ // If the min has the units, then put the units onto the max as well
				$max = $max.$minUnits[1];
			}
			return array_merge($this->determinePropertiesInternal("min ".$key, $min), $this->determinePropertiesInternal("max ".$key, $max));
		}
		
		// If there are actually 2 units in this value
		if(preg_match('/([0-9.]+[^0-9]+),\s*([0-9].*)/', $value, $matches)){
			$key = Porter::Stem($this->fixSpelling($key));
			$unit1 = $matches[1];
			$unit2 = $matches[2];
			
			$result1 = $this->determineProperty($key, $unit1);
			$result2 = $this->determineProperty($key, $unit2);
			if($result1['datatype'] !== $result2['datatype'] AND $result1['datatype'] !== Datatype::STRING AND $result2['datatype'] !== Datatype::STRING){
				$result1['name_fulnam'] = $result1['name_fulnam'] . ' ' . Datatype::getDisplayName($result1['datatype']);
				$result2['name_fulnam'] = $result2['name_fulnam'] . ' ' . Datatype::getDisplayName($result2['datatype']);
				// TODO: could we use one of the extracted datatypes?
				return array($result1, $result2);
			}else return array($this->determineProperty($key, $value));
		}
		
		// If this is an area, then grab the length, the width and the total area
		if($this->isIn($value, "[0-9].* x [0-9]") OR $this->isIn($value,  '[0-9.]+x[0-9.]+\s*[^\s]+')){
			// We *DON'T* want to limit the explodes to 2, as this disallows 
			// L x W x H
			$value = $this->fixValue($value);
			if($this->isIn($value, "[0-9].* x [0-9]")) $dimensions = explode(" x ", $value );
			else                                       $dimensions = explode('x',   $value );
			$width     = $dimensions[0];
			$length    = $dimensions[1];
			$widthResult  = $this->determineProperty($key." width", $width);
			$lengthResult = $this->determineProperty($key." length", $length);

			if( count($dimensions) > 2 ){
				$height = $dimensions[2];
				$heightResult = $this->determineProperty($key." height", $height);
			}
			
			// Now check to see how we should interpret the result - it may not be 
			// area, but 2 other dimensions
			if($widthResult['datatype'] === Datatype::LINEAL OR $lengthResult['datatype'] === Datatype::LINEAL){
				$this->giveBothValuesTheSameUnit($width, $length);
				$widthResult  = $this->determineProperty($key." width", $width);
				$lengthResult = $this->determineProperty($key." length", $length);
				if( count($dimensions) > 2 ){
					$this->giveBothValuesTheSameUnit($width, $height);
					$heightResult = $this->determineProperty($key." height", $height);
				}
				
				$areaResult = array('value_mxd' => $widthResult['value_mxd'] * $lengthResult['value_mxd']);
				$areaResult['name_fulnam'] = $key.' area';
				$areaResult['datatype']    = Datatype::AREA;
				if( count($dimensions) > 2 ){
					return array($widthResult, $lengthResult, $heightResult, $areaResult);
				}else{
					return array($widthResult, $lengthResult, $areaResult);
				}
			}elseif($widthResult['datatype'] === Datatype::STRING AND $lengthResult['datatype'] === Datatype::STRING){
				return array($this->determineProperty($key, $value));
			}else{
				// TODO: figure out whether there are other # x # ones that we want 
				// to extract
				// throw new \Exception("Can't extract units out of: '$key' : '$value'. Please add the dimensions: '".$widthResult['datatype']."' x '".$lengthResult['datatype']."' to the list of multi-dimensional checks.");
				return array($this->determineProperty($key, $value));
			}
			
		}
		
		return array($this->determineProperty($key, $value));
	}

	/**
	 * {@inheritdoc}
	 */
	public function determineCategory($category, $name){
		$lowercaseName = $this->fixSpelling(strtolower($name));
		if($matchedCategory = $this->determineCategoryExactMatch($category, $lowercaseName)) 	return array_values($matchedCategory)[0];
		if($matchedCategory = $this->determineCategoryMatchedWords($category, $lowercaseName)) 	return $matchedCategory;
		if($matchedCategory = $this->determineCategoryExactMatch($category, $name)) 			return array_values($matchedCategory)[0];
		if($matchedCategory = $this->determineCategoryMatchedWords($category, $name)) 			return $matchedCategory;
		return null;
	}
	
	
	
	/**
	 * Finds the subcategory that contains all of the words in any part of the 
	 * string.
	 *
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
	 *
	 * If there are multiple results, then it will return the alias that matches 
	 * with the longest name
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
