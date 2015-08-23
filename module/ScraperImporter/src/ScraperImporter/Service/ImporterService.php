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

	public function dumpAssets( $jsonArray ){
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
}
?>
