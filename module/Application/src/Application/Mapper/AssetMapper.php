<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\AssetInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Where;
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
	public function findByCategory( $category, $filters=NULL )
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"array|Application\Model\CategoryInterface",
			"object|null"
		));

		if( is_array($category) ){
			$categoryList=array();
			foreach( $category as $key => $cat ){
				$categoryList[$key] = $cat->getName();
			}
		}else{
			$categoryList = array($category->getName());
		}

		if( isset($filters->location) &&
		    isset($filters->location->latitude) && 
		      isset($filters->location->latitude->min) && 
		      isset($filters->location->latitude->max) && 
		    isset($filters->location->longitude) &&
		      isset($filters->location->longitude->min) &&
		      isset($filters->location->longitude->max)
		){
			if( isset($filters->location->longitude->user) ){
				$userLong=$filters->location->longitude->user;
			}else{
				$userLong=( $filters->location->longitude->max + $filters->location->longitude->min )/2;
			}
			if( isset($filters->location->latitude->user) ){
				$userLat=$filters->location->latitude->user;
			}else{
				$userLat=( $filters->location->latitude->max + $filters->location->latitude->min )/2;
			}

			// If the longitude bounds are over the date boundary, then we have to 
			// handle it differently
			if( $filters->location->longitude->max < $filters->location->longitude->min ){
				$longitudeFilter="location.longitude_float NOT BETWEEN ".(float)$filters->location->longitude->max." AND ".(float)$filters->location->longitude->max." ";
			}else{
				$longitudeFilter="location.longitude_float BETWEEN ".(float)$filters->location->longitude->min." AND ".(float)$filters->location->longitude->max." ";
			}

			$sql="SELECT ".
					"$this->dbTable.*, ".
					"3956*2*ASIN(SQRT(POWER(SIN((".$userLat."-location.latitude_float)*PI()/360),2) + COS(".$userLat."*PI()/180)*COS(location.latitude_float*PI()/180)*POWER(SIN((".$userLong."-location.longitude_float)*PI()/360),2))) AS distance ".
				"FROM ".
					"category,$this->dbTable,branch,location ".
				"WHERE ".
					"category.name_fulnam IN ('".implode("','",$categoryList)."') ".
					"AND category.category_id = $this->dbTable.category_id ".
					"AND $this->dbTable.lessor_user_id = branch.user_id ".
					"AND branch.location_id = location.location_id ".
					"AND location.latitude_float BETWEEN ".$filters->location->latitude->min." AND ".$filters->location->latitude->max." ".
					"AND $longitudeFilter".
				"ORDER BY distance;";
		}else{
			$sql="SELECT ".
					"$this->dbTable.* ".
				"FROM $this->dbTable,category ".
				"WHERE category.name_fulnam IN ('".implode("','",$categoryList)."') ".
					"AND asset.category_id = category.category_id;";

		}

		$statement = $this->dbAdapter->query($sql);
		$result = $statement->execute();

		$idArray=array();
		while( $result->current() ){
			array_push( $idArray,(int) $result->current()['asset_id'] );
			$result->next();
		}
		$this->find($idArray);
		return $this->getPrototypeArray();

	}

	/**
	 * {@inheritdoc}
	 */
	public function getLessors($lessorMapper,$reload=false)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Mapper\LessorMapperInterface",
			"boolean"
		));

		return $this->getSubObject($lessorMapper,'lessor','getLessorId','getLessor','setLessor');
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
		return $this->getPrototypeArray();
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
