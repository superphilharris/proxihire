<?php
namespace ScraperImporter\Service;

use Application\Helper\ClassHelper;

class ImporterService implements ImporterServiceInterface
{
	protected $assetMapper;
	protected $asset;
	protected $assetPropertyMapper;
	protected $assetProperty;
	protected $assetRateMapper;
	protected $assetRate;
	protected $categoryMapper;
	protected $category;
	protected $lessorMapper;
	protected $lessor;
	protected $locationMapper;
	protected $location;
	protected $urlMapper;
	protected $url;

	public function __construct(
		$assetMapper,
		$asset,
		$assetPropertyMapper,
		$assetProperty,
		$assetRateMapper,
		$assetRate,
		$categoryMapper,
		$category,
		$lessorMapper,
		$lessor,
		$locationMapper,
		$location,
		$urlMapper,
		$url
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(), array(
			'Application\Mapper\AssetMapperInterface',
			'Application\Model\AssetInterface',
			'Application\Mapper\AssetPropertyMapperInterface',
			'Application\Model\AssetPropertyInterface',
			'Application\Mapper\AssetRateMapperInterface',
			'Application\Model\AssetRateInterface',
			'Application\Mapper\CategoryMapperInterface',
			'Application\Model\CategoryInterface',
			'Application\Mapper\LessorMapperInterface',
			'Application\Model\LessorInterface',
			'Application\Mapper\LocationMapperInterface',
			'Application\Model\LocationInterface',
			'Application\Mapper\UrlMapperInterface',
			'Application\Model\UrlInterface'
		));
		$this->assetMapper=$assetMapper;
		$this->asset=$asset;
		$this->assetPropertyMapper=$assetPropertyMapper;
		$this->assetProperty=$assetProperty;
		$this->assetRateMapper=$assetRateMapper;
		$this->assetRate=$assetRate;
		$this->categoryMapper=$categoryMapper;
		$this->category=$category;
		$this->lessorMapper=$lessorMapper;
		$this->lessor=$lessor;
		$this->locationMapper=$locationMapper;
		$this->location=$location;
		$this->urlMapper=$urlMapper;
		$this->url=$url;
	}

	public function dumpAssets( $pages ){
		// Read in the categories
		$categories_file = __DIR__.'/../../../../../public/js/categories.js';
		$categories_json = str_replace('categories = ', '', str_replace(';', '', file_get_contents($categories_file)));
		$categories = json_decode($categories_json);
	
		// Read in the crawler dump
		foreach($pages as $page){
			if($page->item_type === "asset"){
				$category = $this->findCategory($categories, $page->item_name);
				if($category === null){
					echo "<br/><a href=\"$page->url\" target='_blank'>$page->item_name</a>";
					exit;
				}else $categoryName = $category->aliases[0];
				echo "INSERT INTO url (title_desc, path_url) VALUES ('".$page->item_name."','".$page->url."');\n";
				echo "INSERT INTO asset (category_id, url_id, lessor_user_id) SELECT c.category_id, LAST_INSERT_ID(), l.lessor_user_id FROM category c JOIN lessor l ON true LEFT JOIN user ON l.lessor_user_id=u.user_id WHERE c.name_fulnam='".$categoryName."' AND u.name_fulnam='".$page->lessor."';\n";
				echo "SET @last_asset_id = LAST_INSERT_ID();\n";
				$properties = array();
				foreach($page->properties as $propertyName => $propertyValue){
					$properties = array_merge($properties, $this->findProperties($propertyName, $propertyValue));
				}
				var_dump($properties);
			}
		}

		$assets=$this->getAssets( $jsonArray );
		return $assets;
	}
	public function getAssets( $jsonArray ){
		$assets=array();
		foreach( $jsonArray as $item ){
			if( isset($item->item_type) AND  $item->item_type=='asset' ){
				$assets[]=$this->getAsset( $item );
			}
		}
		return $assets;
	}

	private function getAsset( $item ){
		$asset=new $this->asset;
		$asset->setUrl( $this->getUrl($item) );
		$asset->setProperties( $this->getProperties($item) );
		$asset->setRates( $this->getRates($item) );
		$asset->setLessor( $this->getLessor($item) );
		return $asset;
	}

	private function getUrl( $item ){
		$url=new $this->url;
		if( isset( $item->url ) ){
			$url->exchangeArray(array(
				'path' => $item->url,
				'title' => $item->item_name
			));
		}
		return $url;
	}
	
	private function getProperties( $item ){
		$properties=array();
		if( isset( $item->properties ) ){
			$i=0;
			foreach( $item->properties as $name=>$property ){
				$properties[$i] = new $this->assetProperty;
				$properties[$i]->exchangeArray(array(
					'name' => $name,
					'value' => $property
				));
				$i++;
			}
		}
		return $properties;
	}

	private function getRates( $item ){
		$rates=array();
		if( isset( $item->rates ) ){
			$i=0;
			foreach( $item->rates as $duration=>$price ){
				$rates[$i] = new $this->assetRate;
				$rates[$i]->exchangeArray(array(
					'price' => $price,
					'duration' => $duration
				));
				$i++;
			}
		}
		return $rates;
	}

	private function getLessor( $item ){
		$lessor=new $this->lessor;
		if( isset($item->lessor) ){
			$lessor->exchangeArray(array(
				'name' => $item->lessor
			));
		}
		return $lessor;
	}

	private function getCategory( $item ){
		$category=new $this->category;
		if( isset($item->category) ){
			$category->exchangeArray(array(
				'name' => $item->category
			));
		}
		return $category;
	}
	
	private function findCategory($category, $name){
		if($matchedCategory = $this->findCategoryExactMatch($category, $name)) 	return $matchedCategory;
		if($matchedCategory = $this->findCategoryMatchedWords($category, $name)) 	return $matchedCategory;
		return null;
	}

	private function findCategoryMatchedWords($category, $name){
		if(property_exists($category, 'children')) {
			foreach($category->children as $subCategory){
				$result = $this->findCategoryMatchedWords($subCategory, $name);
				if($result !== null) return $result;
			}
		}
		if(property_exists($category, 'aliases')) {
			foreach($category->aliases as $alias){
				$aliasWords = explode(' ', $alias);
				$usesAllAliasWords = true;
				foreach($aliasWords as $word){
					if(! $this->isIn(strtolower($name), strtolower($word))){
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
	private function findCategoryExactMatch($category, $name){
		if(property_exists($category, 'children')) {
			foreach($category->children as $subCategory){
				$result = $this->findCategoryExactMatch($subCategory, $name);
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
	private function findProperty($key, $value){
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
			}elseif($unit === 'tonne'){
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
			}elseif($unit === 'hp'){
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
			}elseif($unit === 'km/hr'){
				$property['datatype']	= 'speed';
				$property['value_mxd']	= floatval($number) * 1000 / 60 / 60;
			}elseif($unit === 'm/s' OR $unit === 'm/sec'){
				$property['datatype']	= 'speed';
				$property['value_mxd']	= floatval($number);
			}elseif($unit === 'amps'){
				$property['datatype']  = 'current';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'watts'){
				$property['datatype']  = 'power';
				$property['value_mxd'] = floatval($number);
			}elseif($unit === 'kw'){
				$property['datatype']  = 'power';
				$property['value_mxd'] = floatval($number) * 1000;
			}elseif($unit === 'ltr/hr' OR $unit === 'lit/hr'){
				$property['datatype']  = 'flow';
				$property['value_mxd'] = floatval($number) / 60 / 60; // Convert to ltr/sec
			}elseif($unit === 'ltr/min' OR $unit === 'lit/min'){
				$property['datatype']  = 'flow';
				$property['value_mxd'] = floatval($number) / 60; // Convert to ltr/sec
			}elseif($unit === 'cfm'){
				$property['datatype']  = 'flow';
				$property['value_mxd'] = floatval($number) * 0.471947443; // Convert to ltr/sec
				
			// Key name matching				
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
		return $property;
	}
	
	private function fixSpelling($string){
		$string = str_replace('lenght', 	'length', 	$string);
		$string = str_replace('widht', 		'width', 	$string);
		$string = str_replace('rptation', 	'rotation', $string);
		$string = str_replace('lenght', 	'length',	$string);
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

	private function findProperties($key, $value){
		$key   = trim(strtolower($key),   ":- ");
		$value = trim(strtolower($value), ": ");
		
		if($this->isIn($value, "[0-9].* to .*[0-9]")){
			$twoNumbers = explode(" to ", $value);
			$min = $twoNumbers[0];
			$max = $twoNumbers[1];
			if(preg_match('/[0-9\-.]+\s*([a-zA-Z\/]+)/', $max, $maxUnits) AND floatval($min) == $min){ // If the max has the units, then put the units onto the min as well
				$min = $min.$maxUnits[1];
			}
			return array($this->findProperty("min ".$key, $min), $this->findProperty("max ".$key, $max));
			
		}else return array($this->findProperty($key, $value));
	}
}
?>
