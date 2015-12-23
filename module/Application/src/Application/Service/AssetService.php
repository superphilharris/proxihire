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
	 * {@inheritDoc}
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
		$filters=NULL,
		$number=50)
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Model\CategoryInterface",
			"Application\Model\CategoryAliasesInterface",
			"object|null",
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
			$this->assetMapper->findByCategory( $category, $filters );
		}else{
			$leafCategories=array();

			foreach( $childNodes as $childCategory ){
				$leafCategories=array_merge($leafCategories,$allCategoryAliases->getLeafNodesFor($childCategory));
			}
			$categories = $this->categoryMapper->getPopularCategories( 
				$leafCategories, 
				5 
			);
			$this->assetMapper->initPrototypeArray( 5 );
			// jih: pass in filters
			$assets=$this->assetMapper->findByCategory($categories);
			$this->assetMapper->setPrototypeArray($assets);
		}
		$this->assetMapper->getUrls($this->urlMapper);

		return $this->assetMapper->getAssets();
	}
}
?>
