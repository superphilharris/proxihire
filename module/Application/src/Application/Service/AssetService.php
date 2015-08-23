<?php
namespace Application\Service;

use Application\Helper\ClassHelper;
use Applciation\Model\CategoryInterface;
use Application\Model\AssetInterface;
use Application\Mapper\AssetMapperInterface;
use Application\Mapper\AssetRateMapperInterface;
use Application\Mapper\AssetPropertyMapperInterface;
use Application\Mapper\UrlMapperInterface;
use Application\Mapper\LessorMapperInterface;
use Application\Mapper\LocationMapperInterface;

class AssetService implements AssetServiceInterface
{
	protected $assetMapper;
	protected $assetPrototype;
	protected $urlMapper;
	protected $lessorMapper;
	protected $locationMapper;
	protected $assetRateMapper;
	protected $assetPropertyMapper;

	public function __construct( 
		AssetInterface $assetPrototype,
		//$assetPrototype, // jih: importer testing
		AssetMapperInterface $assetMapper,
		UrlMapperInterface $urlMapper,
		LessorMapperInterface $lessorMapper,
		LocationMapperInterface $locationMapper,
		AssetRateMapperInterface $assetRateMapper,
		AssetPropertyMapperInterface $assetPropertyMapper
	){
		$this->assetPrototype = $assetPrototype;
		$this->assetMapper = $assetMapper;
		$this->urlMapper = $urlMapper;
		$this->lessorMapper = $lessorMapper;
		$this->locationMapper = $locationMapper;
		$this->assetRateMapper = $assetRateMapper;
		$this->assetPropertyMapper = $assetPropertyMapper;
	}
	/**
	 * {@inheritDoc}
	 */
	public function getAssetList(
		$category, 
		$filters=NULL, 
		$location=NULL, 
		$number=50)
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"array|Application\Model\CategoryInterface",
			"object|null",
			"string|null",
			"integer"));

		// jih: accumulate location into filters, if not already there

		//return array_slice($this->assetPrototype,0,30); // jih: importer testing
		$assetArray=array();
		for( $i=0; $i<$number; $i++){
			$assetArray[$i]=new $this->assetPrototype;
			$properties = array();
		}

		$this->assetMapper->setPrototypeArray($assetArray);
		$this->assetMapper->findByCategory( $category, $filters );
		$this->assetMapper->getUrls($this->urlMapper);
		$this->assetMapper->getLessors($this->lessorMapper);

		$this->lessorMapper->getLocations( $this->locationMapper );
		return $this->assetMapper->getAssets();
	}
}
?>
