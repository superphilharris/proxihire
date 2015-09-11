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
	private $importerServiceUtils;
	private $haveCreatedLessor; // psh TODO: remove when we remove the manual sql statements

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
		$this->helper = new ImporterServiceHelper();
	}
	
	public function dumpAssets( $pages ){
		// Read in the categories
		$categories_file = __DIR__.'/../../../../../public/js/categories.js';
		$categories_json = str_replace('categories = ', '', str_replace(';', '', file_get_contents($categories_file)));
		$categories = json_decode($categories_json);
		if(!$categories) throw new \Exception("The categories.js file is not valid json: " . json_last_error_msg());
		
		echo '<code>';
		// Read in the crawler dump
		$this->haveCreatedLessor = false;
		foreach($pages as $page){
			if($page->item_type === "asset"){
				$this->createLessor($page->lessor);

				$imageUrl = $this->helper->syncImage($page->image);
				$imageUrl = ($imageUrl === NULL) ? 'NULL' : "'$imageUrl'";
				
				$category = $this->helper->determineCategory($categories, $page->item_name);
				if($category === null){
					echo "</code><br/><a href=\"$page->url\" target='_blank'>$page->item_name</a>";
					exit;
				}else $categoryName = $category->aliases[0];
				echo "INSERT INTO url (title_desc, path_url) VALUES ('".addslashes($page->item_name)."','".$page->url."'); ";
				echo "INSERT INTO asset (category_id, url_id, lessor_user_id, image_url) SELECT c.category_id, LAST_INSERT_ID(), l.lessor_user_id, $imageUrl FROM category c JOIN lessor l ON true LEFT JOIN user u ON l.lessor_user_id=u.user_id WHERE c.name_fulnam='".$categoryName."' AND u.name_fulnam='".$page->lessor."'; ";
				echo "SET @last_asset_id = LAST_INSERT_ID();<br/>";
				// Get the properties
				$properties = array();
				foreach($page->properties as $propertyName => $propertyValue){
					$properties = array_merge($properties, $this->helper->determineProperties($propertyName, $propertyValue, $categoryName));
				}
				foreach($properties as $property){
					echo "INSERT INTO asset_property (asset_id, name_fulnam, datatype_id, value_mxd) SELECT @last_asset_id, '".addslashes($property['name_fulnam'])."', d.datatype_id, '".addslashes($property['value_mxd'])."' FROM datatype d WHERE d.datatype_abbr = '".$property['datatype']."';";
				}
				echo "<br/><br/>";
				var_dump($properties);
			}
		}
		echo '</code>';
		$assets=$this->getAssets( $jsonArray );
		return $assets;
	}

	private function createLessor($lessorName, $url = "https://www.hirepool.co.nz/"){
		if(!$this->haveCreatedLessor){
			echo "INSERT INTO location (name_fulnam, latitude_float, longitude_float) VALUES ('".$lessorName." - Auckland', '".(-36.8406+rand(-10,10)/300)."', '".(174.761066 + rand(-10, 10)/500)."'); ";
			echo "INSERT INTO user     (name_fulnam, location_id) VALUES ('".$lessorName."', LAST_INSERT_ID()); SET @last_user_id = LAST_INSERT_ID(); ";
			echo "INSERT INTO url      (title_desc, path_url) VALUES ('".$lessorName."', '".$url."'); ";
			echo "INSERT INTO lessor   (lessor_user_id, url_id) VALUES (@last_user_id, LAST_INSERT_ID()); <br/><br/>";
			$this->haveCreatedLessor = true;
		}
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
}
?>
