<?php
namespace Application\Service;

use Application\Helper\ClassHelper;
use Applciation\Model\CategoryInterface;
use Application\Model\AssetInterface;
use Application\Mapper\AssetMapperInterface;
use Application\Mapper\AssetRateMapperInterface;
use Application\Mapper\AssetPropertyMapperInterface;
use Application\Mapper\UrlMapperInterface;

class AssetService implements AssetServiceInterface
{
	protected $assetMapper;
	protected $assetPrototype;
	protected $urlMapper;
	protected $assetRateMapper;
	protected $assetPropertyMapper;

	public function __construct( 
		AssetInterface $assetPrototype,
		AssetMapperInterface $assetMapper,
		UrlMapperInterface $urlMapper,
		AssetRateMapperInterface $assetRateMapper,
		AssetPropertyMapperInterface $assetPropertyMapper
	){
		$this->assetPrototype = $assetPrototype;
		$this->assetMapper = $assetMapper;
		$this->urlMapper = $urlMapper;
		$this->assetRateMapper = $assetRateMapper;
		$this->assetPropertyMapper = $assetPropertyMapper;
	}
	/**
	 * {@inheritDoc}
	 */
	public function getAssetList($category, $filters=NULL, $location=NULL, $number=50)
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("Application\Model\CategoryInterface","object|null","string|null","integer"));

		// jih: accumulate location into filters, if not already there

		$assetArray=array();
		for( $i=0; $i<$number; $i++){
			$assetArray[$i]=new $this->assetPrototype;
			$properties = array();
		}

		$this->assetMapper->setPrototypeArray($assetArray);
		$this->assetMapper->findByCategory( $category, $filters );
		$this->assetMapper->getUrls($this->urlMapper);
		return $this->assetMapper->getAssets();
	}

}
?>
