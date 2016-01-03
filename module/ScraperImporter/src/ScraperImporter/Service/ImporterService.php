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
	private $helper;
	private $outputChannel;
	private $isCategorizeOnly;

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
		
		$this->isCategorizeOnly = isset($_REQUEST['categorizeOnly']);
		$this->helper = new ImporterServiceHelper($this->isCategorizeOnly);
	}
	
	public function dumpAssets( $pages ){
		if(!set_time_limit(600)) exit("Need to turn off safe mode in php.ini.");
		$lastCategorizedAssetName = null;
		$categorizeFileName = '/tmp/.last_categorized_'.basename($_SERVER['REQUEST_URI'])."_asset";
		
		// 1. Read in the categories
		$categories_file = __DIR__.'/../../../../../public/js/categories.js';
		$categories_json = str_replace('categories = ', '', str_replace(';', '', file_get_contents($categories_file)));
		$categories = $this->helper->jsonDecode($categories_json);
		
		// 2. Read in the crawled lessors
		if(!$this->isCategorizeOnly){
			$this->writeComment('Create Lessor SQL');
			$createdLessors = array();
			foreach($pages as $lessor){
				if($lessor->item_type === "lessor"){
					if(! in_array($lessor->name, $createdLessors)){
						if(is_string($lessor->location)) $lessor->location = array($lessor->location);
						$iconUrl = null;
						if(property_exists($lessor, 'icon')){
							$iconUrl = $this->helper->syncImage($lessor->icon, 'lessors');
							$iconUrl = $this->helper->createIcons($iconUrl);
						}
						$iconUrl = ($iconUrl === NULL) ? 'NULL' : "'".addslashes($iconUrl)."'";
						
						$this->writeSQL('DELETE r FROM asset_rate r, asset a WHERE a.asset_id = r.asset_id AND a.lessor_user_id IN (SELECT lessor_user_id FROM lessor l, url u WHERE l.url_id = u.url_id AND title_desc = "'.addslashes($lessor->name).'"); ');
						$this->writeSQL('DELETE ap FROM asset_property ap, asset a WHERE a.asset_id = ap.asset_id AND a.lessor_user_id IN (SELECT lessor_user_id FROM lessor l, url u WHERE l.url_id = u.url_id AND title_desc = "'.addslashes($lessor->name).'"); ');
						$this->writeSQL('DELETE a FROM asset a WHERE lessor_user_id IN (SELECT lessor_user_id FROM lessor l, url u WHERE l.url_id = u.url_id AND title_desc = "'.addslashes($lessor->name).'"); ');
						$this->writeSQL('DELETE l FROM lessor l WHERE l.url_id IN (SELECT url_id FROM url WHERE title_desc = "'.addslashes($lessor->name).'"); ');
							
						$this->writeSQL('DELETE u FROM url u WHERE u.url_id NOT IN (SELECT url_id FROM asset) AND url_id NOT IN (SELECT url_id FROM lessor); ');	
						$this->writeSQL("INSERT INTO user     (name_fulnam) VALUES ('".addslashes($lessor->name)."'); SET @last_user_id = LAST_INSERT_ID(); ");
						$this->writeSQL("INSERT INTO url      (title_desc, path_url) VALUES ('".addslashes($lessor->name)."', '".addslashes($lessor->url)."'); ");
						$this->writeSQL("INSERT INTO lessor   (lessor_user_id, url_id, icon_url) VALUES (@last_user_id, LAST_INSERT_ID(), $iconUrl); ");
						
						foreach($lessor->location as $location){
							$branch = $this->helper->determineBranch($location, $lessor);
							$phoneNumber = ($branch->phone_number === null)? 'NULL' : "'".addslashes($branch->phone_number)."'";
							$email		 = ($branch->email 		  === null)? 'NULL' : "'".addslashes($branch->email)."'";
							$this->writeSQL("INSERT INTO location (name_fulnam, latitude_float, longitude_float) VALUES ('".addslashes($branch->name)."', '".$branch->lat."', '".$branch->long."'); ");
							$this->writeSQL("INSERT INTO branch (user_id, location_id, phone_number_text, email_email) VALUES (@last_user_id, LAST_INSERT_ID(), $phoneNumber, $email);");
						}
	
						array_push($createdLessors, $lessor->name);
					}
				}
			}
			
			// 3. Delete any existing assets
			$this->writeComment('Delete Existing Assets SQL');
			$createdLessors = array();
			foreach($pages as $lessor){
				if($lessor->item_type === "lessor"){
					if(! in_array($lessor->name, $createdLessors)){
						$this->writeSQL('DELETE ap FROM asset_property ap, asset a WHERE a.asset_id = ap.asset_id AND a.lessor_user_id IN (SELECT lessor_user_id FROM lessor l, url u WHERE l.url_id = u.url_id AND title_desc = "'.addslashes($lessor->name).'"); ');
						$this->writeSQL('DELETE a FROM asset a WHERE lessor_user_id IN (SELECT lessor_user_id FROM lessor l, url u WHERE l.url_id = u.url_id AND title_desc = "'.addslashes($lessor->name).'"); ');
						$this->writeSQL('DELETE u FROM url u WHERE u.url_id NOT IN (SELECT url_id FROM asset) AND url_id NOT IN (SELECT url_id FROM lessor); ');
					}
				}
			}
			
			// 4. Update any missing categories and datatypes
			$this->writeComment('New Assets SQL');
			if(true){ // Create new categories and datatypes
				exec('php '.__DIR__.'/../../../../../tools/generate_proxihire_sql.php > /tmp/.tmp_category.sql');
				$sql = file_get_contents('/tmp/.tmp_category.sql');
				$this->writeSQL($sql);
				unlink('/tmp/.tmp_category.sql');
			}
			if(file_exists($categorizeFileName)) unlink($categorizeFileName);
		}else{
			if(file_exists($categorizeFileName)){
				$lastCategorizedAssetName = file_get_contents($categorizeFileName);
			}
		}
		
		// 5. Read in the crawled assets
		$foundLastCategorizedAsset = false;
		foreach($pages as $i => $page){
			if($page->item_type === "asset"){
				$foundLastCategorizedAsset = ($foundLastCategorizedAsset OR $lastCategorizedAssetName === null OR $lastCategorizedAssetName == $page->item_name);
				if(!$this->isCategorizeOnly OR $foundLastCategorizedAsset){
					$itemName = ucfirst($page->item_name);
					// Sync and resize the image
					$imageUrl = null;
					if(property_exists($page, 'image')){
						$imageUrl = $this->helper->syncImage($page->image);
						if($imageUrl != null) $this->helper->resizeAndCropImage(__DIR__.'/../../../../../public/img/assets/'.$imageUrl);
					}
					$imageUrl = ($imageUrl === NULL) ? 'NULL' : "'".addslashes($imageUrl)."'";
					
					// Determine the category
					$category = $this->helper->determineCategory($categories, $itemName);
					if($category === null AND property_exists($page, 'category')){
						$category = $this->helper->determineCategory($categories, $itemName." ".$page->category);
					}
					if($category === null AND property_exists($page, 'description')){
						$category = $this->helper->determineCategory($categories, $itemName." ".$page->description);
					}
					if($category === null){
						$category = (property_exists($page, 'category'))? "(".$page->category.")" : "";
						$comment = "<a href=\"$page->url\" target='_blank'>$itemName $category</a>";
						if (property_exists($page, 'description')) $comment .= ": " . $page->description;
						$this->writeComment($comment);
						
						file_put_contents($categorizeFileName, $page->item_name); // Used for speeding up the categorizing
						chmod($categorizeFileName, 0777);
						exit;
					}else $categoryName = $category->aliases[0];
	
					
					$description = (property_exists($page, 'description'))? "'".addslashes(ucfirst($page->description))."'" : 'NULL';
					$this->writeSQL("INSERT INTO url (title_desc, path_url) VALUES ('".addslashes($itemName)."','".addslashes($page->url)."'); ");
					$this->writeSQL("INSERT INTO asset (category_id, url_id, lessor_user_id, image_url, description_text) SELECT c.category_id, LAST_INSERT_ID(), l.lessor_user_id, $imageUrl, $description FROM category c JOIN lessor l ON true LEFT JOIN user u ON l.lessor_user_id=u.user_id WHERE c.name_fulnam='".addslashes($categoryName)."' AND u.name_fulnam='".addslashes($page->lessor)."'; ");
					$this->writeSQL("SET @last_asset_id = LAST_INSERT_ID();");
					
					// Determine and clean up the properties
					$mainProperties = (property_exists($category, 'properties')) ? 	$category->properties 	: array();
					$pageProperties = (property_exists($page, 'properties')) ? 		$page->properties 		: array();
					$properties = $this->helper->determineProperties($pageProperties, $categoryName, $itemName, $mainProperties);
					foreach($properties as $property){
						$this->writeSQL("INSERT INTO asset_property (asset_id, name_fulnam, datatype_id, value_mxd) SELECT @last_asset_id, '".addslashes($property['name_fulnam'])."', d.datatype_id, '".addslashes($property['value_mxd'])."' FROM datatype d WHERE d.datatype_abbr = '".$property['datatype']."';");
					}
					
					// Determine and clean up the rates
					if (property_exists($page, 'rate')){
						$rates = $this->helper->determineRates($page->rate);
						foreach($rates as $rate){
							if(isset($rate['duration_hrs'])){
								$this->writeSQL("INSERT INTO asset_rate (asset_id, duration_hrs, price_dlr) VALUES (@last_asset_id, '".addslashes($rate['duration_hrs'])."', '".$rate['price_dlr']."');");
							}else{
								$this->writeSQL("INSERT INTO asset_property (asset_id, name_fulnam, datatype_id, value_mxd) SELECT @last_asset_id, '".addslashes($rate['name_fulnam'])."', d.datatype_id, '".addslashes($rate['value_mxd'])."' FROM datatype d WHERE d.datatype_abbr = '".$rate['datatype']."';");
							}
						}
					}
				}
			}
		}
		return array();
	}
	private function writeComment($comment){
		echo "<br/><h1><span style=\"color: grey; font-size: 0.5em;\">-- </span>$comment\n</h1><br/>";
	}
	private function writeSQL($sql){
		echo "<code>$sql</code><br/>\n";
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
