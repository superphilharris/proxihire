<?php
namespace Application\Service;

use Application\Helper\ClassHelper;
use Applciation\Model\CategoryInterface;
use Application\Model\AssetInterface;
use Application\Mapper\AssetMapperInterface;
use Application\Mapper\AssetRateMapperInterface;
use Application\Mapper\AssetPropertyMapperInterface;
use Application\Mapper\BranchMapperInterface;
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
	protected $branchMapper;

	public function __construct( 
		AssetInterface $assetPrototype,
		AssetMapperInterface $assetMapper,
		UrlMapperInterface $urlMapper,
		LessorMapperInterface $lessorMapper,
		LocationMapperInterface $locationMapper,
		AssetRateMapperInterface $assetRateMapper,
		AssetPropertyMapperInterface $assetPropertyMapper,
		BranchMapperInterface $branchMapper
	){
		$this->assetPrototype = $assetPrototype;
		$this->assetMapper = $assetMapper;
		$this->urlMapper = $urlMapper;
		$this->lessorMapper = $lessorMapper;
		$this->locationMapper = $locationMapper;
		$this->assetRateMapper = $assetRateMapper;
		$this->assetPropertyMapper = $assetPropertyMapper;
		$this->branchMapper = $branchMapper;
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

		$assetArray=array();
		for( $i=0; $i<$number; $i++){
			$assetArray[$i]=new $this->assetPrototype;
			$properties = array();
		}

		$this->assetMapper->setPrototypeArray($assetArray);
		$this->assetMapper->findByCategory( $category, $filters );
		$this->assetMapper->getUrls($this->urlMapper);
		$this->assetMapper->getLessors($this->lessorMapper);

		$this->lessorMapper->getBranches( $this->branchMapper );
		$this->branchMapper->getLocation( $this->locationMapper );

		return $this->assetMapper->getAssets();
	}
}
?>
