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
	\Zend\Debug\Debug::dump($categories_file); //jih: remove this
	$categories_json = str_replace('categories = ', '', str_replace(';', '', file_get_contents($categories_file)));
	$categories = json_decode($categories_json);

	//// Read in the crawler dump
	//$pages = json_decode(file_get_contents("hirepool.json.bk"));
	//var_dump(file_get_contents("hirepool.json"));
	foreach($pages as $page){
		if($page->item_type === "asset"){
			$category = $this->findCategory($categories, $page->name);
			if($category === null){
				echo "$page->url\n";
				echo "$page->name: ";
				$categoryName = trim(fgets(fopen ("php://stdin","r")));
			}else $categoryName = $category->aliases[0];
			echo "INSERT INTO url (title_desc, path_url) VALUES ('".$page->name."','".$page->url."');\n";
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
				'title' => $item->name
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
		if(property_exists($category, 'children')) {
			foreach($category->children as $subCategory){
				$result = $this->findCategory($subCategory, $name);
				if($result !== null) return $result;
			}
		}
		if(property_exists($category, 'aliases')) {
			foreach($category->aliases as $alias){
				if(strpos(strtolower($name), strtolower($alias))){
					return $category;
				}
			}
		}
		return null;
	}

	private function isIn($haystack, $needle){
		return preg_match("/".$needle."/", $haystack);
	}

	private function isNumberAndOrUnit($string){
		if(preg_match("/[\-0-9.]*\s*[\D]*/", $string, $result)){
			return ($result[0] === $string);
		}
		return false;
	}
	private function findProperty($key, $value){
		$property = array("name_fulnam" => $key, "datatype"=>"string", "value_mxd"=>$value);
		
		if($this->isNumberAndOrUnit($value)){
			// Key name matching
			if($this->isIn($key, "lenght") OR $this->isIn($key, "length") OR $this->isIn($key, "width") OR $this->isIn($key, "height")){
				$property['datatype'] = 'lineal';
				if($this->isIn($value, 'mm'))     $property['value_mxd'] = floatval($property['value_mxd']);
				elseif($this->isIn($value, 'cm')) $property['value_mxd'] = floatval($property['value_mxd']) * 10;
				elseif($this->isIn($value, 'm'))  $property['value_mxd'] = floatval($property['value_mxd']) * 1000;
				
			}elseif($this->isIn($key, 'angle')){
				$property['datatype']  = 'angle';
				$property['value_mxd'] = floatval($value);
				
			}elseif($this->isIn($key, 'per minute')){
				$property['datatype']  = 'frequency';
				$property['value_mxd'] = floatval($value) * 60;
				
			}elseif($this->isIn($key, 'power') AND $this->isIn($value, 'watts')){
				$property['datatype']  = 'power';
				$property['value_mxd'] = floatval($property['value_mxd']);
			
			// Unit Matching
			}elseif($this->isIn($value, 'deg')){
				$property['datatype']  = 'angle';
				$property['value_mxd'] = floatval($value);
				
			}elseif($this->isIn($value, 'kg')){
				$property['datatype']  = 'angle';
				$property['value_mxd'] = floatval($value);
			
			}elseif($this->isIn($value, 'sec')){
				$property['datatype']  = 'time';
				$property['value_mxd'] = floatval($value);
				
			}elseif($this->isIn($value, 'mm')){
				$property['datatype']  = 'lineal';
				$property['value_mxd'] = floatval($value);
			}
		}
		return $property;
	}

	private function findProperties($key, $value){
		$key   = trim(strtolower($key),   ":- ");
		$value = trim(strtolower($value), ": ");
		
		if($this->isIn($value, "[0-9].* to .*[0-9]")){
			$twoNumbers = explode(" to ", $value);
			$min = floatval($twoNumbers[0]);
			$max = floatval($twoNumbers[1]);
			return array($this->findProperty("min ".$key, $min), $this->findProperty("max ".$key, $max));
			
		}else return array($this->findProperty($key, $value));
	}
}
?>
