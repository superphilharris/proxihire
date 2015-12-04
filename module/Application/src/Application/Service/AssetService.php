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
use Application\Mapper\CategoryMapperInterface;

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
	protected $categoryMapper;

	public function __construct( 
		AssetInterface $assetPrototype,
		AssetMapperInterface $assetMapper,
		UrlMapperInterface $urlMapper,
		LessorMapperInterface $lessorMapper,
		LocationMapperInterface $locationMapper,
		AssetRateMapperInterface $assetRateMapper,
		AssetPropertyMapperInterface $assetPropertyMapper,
		BranchMapperInterface $branchMapper,
		CategoryMapperInterface $categoryMapper
	){
		$this->assetPrototype = $assetPrototype;
		$this->assetMapper = $assetMapper;
		$this->urlMapper = $urlMapper;
		$this->lessorMapper = $lessorMapper;
		$this->locationMapper = $locationMapper;
		$this->assetRateMapper = $assetRateMapper;
		$this->assetPropertyMapper = $assetPropertyMapper;
		$this->branchMapper = $branchMapper;
		$this->categoryMapper = $categoryMapper;
	}
	/**
	 * {@inheritDoc} jih: make sure that this is in the interface
	 */
	public function getLessorsForAssets(
		&$assetList
	){
		// Validate argument
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"array|Application\Model\AssetInterface"));

		if(empty($assetList)) return array();
		
		$lessorIds = array();
		foreach( $assetList as $key=>$asset ){
			$lessorId = $asset->getLessorId();
			if( $lessorId > 0 AND ! isset( $lessorIds[$lessorId] ) ){
				$lessorIds[$lessorId] = $lessorId;
			}
		}
		$lessorList = $this->lessorMapper->find( $lessorIds );
		$branchListList=$this->lessorMapper->getBranches( $this->branchMapper );
		foreach( $branchListList as $key=>$branches ){
			$this->branchMapper->setPrototypeArray( $branches );
			$this->branchMapper->getLocation( $this->locationMapper );
		}
		
		// Now go through and change the lessors to point to the assets
		$lessorIdToLessors = array();
		if($lessorList != null){
			foreach($lessorList as $lessor){
				$lessorIdToLessors[$lessor->getId()] = $lessor;
			}
			foreach($assetList as $asset){
				$asset->setLessor($lessorIdToLessors[$asset->getLessorId()]);
			}
		}
		return $lessorList;
	}
	
	/**
	 * {@inheritdoc}
	 * jih: make sure that this is in the interface
	 */
	public function filterAssets(
		&$assets,
		$filters
	){
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"array|Application\Model\AssetInterface",
			"object|null"));
		$this->getLessorsForAssets($assets);

		if( isset($filters->location) &&
		    isset($filters->location->latitude) && 
		      isset($filters->location->latitude->min) && 
		      isset($filters->location->latitude->max) && 
		    isset($filters->location->longitude) &&
		      isset($filters->location->longitude->min) &&
		      isset($filters->location->longitude->max)
		){
			foreach( $assets as $key=>&$asset ){
				$lessor=$asset->getLessor();
				if( is_null($lessor) ) continue;

				$branches=$lessor->getBranches();
				$hasBranchInBounds=false;
				foreach( $branches as $branch ){
					if( is_null($branch) ) continue;

					$location=$branch->getLocation();
					if( is_null($location) ) continue;
					$lat=$location->getLatitude();
					$long=$location->getLongitude();

					if( $this->betweenPoints( 
					       $lat, 
					       $filters->location->latitude->min,  
					       $filters->location->latitude->max  ) &&
					    $this->betweenPoints( 
					       $long, 
					       $filters->location->longitude->min, 
					       $filters->location->longitude->max )
					){
						$hasBranchInBounds=true;
					}
				}
				if( ! $hasBranchInBounds ){
					unset( $assets[$key] );
				}
			}
		}
	}
	
	private function betweenPoints( $actual, $minimum, $maximum ){
		$minimum=$minimum >= 0 ? fmod($minimum, 360) : 360 + fmod($minimum, 360);
		$maximum=$maximum >= 0 ? fmod($maximum, 360) : 360 + fmod($maximum, 360);
		$actual =$actual  >= 0 ? fmod($actual , 360) : 360 + fmod($actual , 360);

		if( $minimum > $maximum ){
			return ! $this->betweenPoints( $actual, $maximum, $minimum );
		}

		return $actual > $minimum && $actual < $maximum;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getAssetList(
		$category, 
		$allCategoryAliases,
		$number=50)
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Model\CategoryInterface",
			"Application\Model\CategoryAliasesInterface",
			"integer"));

		$assetArray=array();
		$childNodes = $allCategoryAliases->getChildrenOf( $category->getName() );

		if(empty($childNodes)){
			if( ! is_null($category->getName()) ){

				$categoryArray=array($category);
				$this->categoryMapper->setPrototypeArray( $categoryArray );
				$categoryArray[0]->incrementLoads();
				$this->categoryMapper->commit();
			}
			for( $i=0; $i<$number; $i++){
				$assetArray[$i]=new $this->assetPrototype;
				$properties = array();
			}
			$this->assetMapper->setPrototypeArray($assetArray);
			$this->assetMapper->findByCategory( $category );
		}else{
		// jih: 	$categories=array();
		// jih: 	foreach( $category->getChildIds() as $childCategory ){
		// jih: 		$categories=$this->categoryMapper->getPopularCategories( 
		// jih: 		       $allCategoryAliases->getLeafNodesFor( $childCategory ), 
		// jih: 		       5 
		// jih: 		);
		// jih: 		array_merge( $assetArray, $this->assetMapper->findByCategory($categories) );
		// jih: 	}
		// jih: 	$this->assetMapper->setPrototypeArray($assetArray);
		}
		$this->assetMapper->getUrls($this->urlMapper);

		return $this->assetMapper->getAssets();
	}
}
?>
