<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\AssetInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface;


class AssetMapper extends AbstractMapper implements AssetMapperInterface
{
	/**
	 * @param AdapterInterface $dbAdapter
	 * @param AdapterInterface $hydrator
	 * @param AdapterInterface $assetPrototypeArray
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$assetPrototypeArray,
		$dbStructure,
		$assetRateMapper,
		$assetPropertyMapper
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\AssetInterface",
			"object",
			"Application\Mapper\AssetRateMapper",
			"Application\Mapper\AssetPropertyMapper"));
		
		$this->assetRateMapper=$assetRateMapper;
		$this->assetPropertyMapper=$assetPropertyMapper;

		parent::construct( $dbAdapter, $hydrator, $assetPrototypeArray, $dbStructure );
	}

	// jih: do we need all of these subroutines?
	/**
	 * {@inheritdoc}
	 */
	public function afterRetrieval()
	{
		$this->getSubObject($this->assetRateMapper,'asset_rate','getRateIds','getRates','setRates');
		$this->getSubObject($this->assetPropertyMapper,'asset_property','getPropertyIds','getProperties','setProperties');
	}

	/**
	 * {@inheritdoc}
	 */
	public function findByCategory( $category, $filters=NULL)
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"array|Application\Model\CategoryInterface",
			"object|null"));

		$category_ids=array();
		if( is_array($category) ){
			foreach( $category AS $key => $value ){
				$category_ids[$key] = $value->getId();
			}
		} else {
			$category_ids[]=$category->getId();
		}

		// jih: filter by $filters
		
		return $this->findBy( 'category_id', $category_ids );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAssetProperties($assetPropertyMapper,$reload=false)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Mapper\AssetPropertyMapperInterface",
			"boolean"
		));

		return $this->getSubObject($assetPropertyMapper,'asset_property','getPropertyIds','getProperties','setProperties');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAssetRates($assetRateMapper,$reload=false)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Mapper\AssetRateMapperInterface",
			"boolean"
		));

		return $this->getSubObject($assetRateMapper,'asset_rate','getRateIds','getRates','setRates');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUrls($urlMapper,$reload=false)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Mapper\UrlMapperInterface",
			"boolean"
		));

		return $this->getSubObject($urlMapper,'url','getUrlId','getUrl','setUrl');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAssets($reload=false)
	{
		// jih: reload the assets if $reload is set
		return $this->prototypeArray;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\AssetInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
