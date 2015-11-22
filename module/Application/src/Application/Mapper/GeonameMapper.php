<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\GeonameInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;


class GeonameMapper extends AbstractMapper implements GeonameMapperInterface
{
	/**
	 * @param AdapterInterface $dbAdapter
	 * @param AdapterInterface $hydrator
	 * @param AdapterInterface $geonamePrototypeArray
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$geonamePrototypeArray,
		$dbStructure
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\GeonameInterface",
			"object"));
		
		parent::construct( $dbAdapter, $hydrator, $geonamePrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function findLike( $name, $number )
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("string","integer"));

		$where = new Where();
		$where->like( "name_fulnam", $name );
		$result = $this->runSelect( $this->dbTable, $where, null, $number );

		$prototype=array_values($this->prototypeArray)[0];

		$i=0;
		$this->prototypeArray=array();
		while( $result->current() ){
			$this->prototypeArray[$i]=new $prototype;
			$this->hydrator->hydrate( $result->current(), $this->prototypeArray[$i] );
			$result->next();
			$i++;
		}
	}
	/**
	 * {@inheritdoc}
	 * jih: actually inheritdoc
	 */
	public function getClosestLocation( $latitude, $longitude, $radius=50.0 ){
		$latitude =(float) $latitude;
		$longitude=(float) $longitude;
		$radius   =(float) $radius;
		$query    ="SELECT name_fulnam, latitude_float, longitude_float, distance FROM ( SELECT g.name_fulnam, g.latitude_float, g.longitude_float, p.radius, p.distance_unit * DEGREES(ACOS(COS(RADIANS(p.latpoint)) * COS(RADIANS(g.latitude_float)) * COS(RADIANS(p.longpoint - g.longitude_float)) + SIN(RADIANS(p.latpoint)) * SIN(RADIANS(g.latitude_float)))) AS distance FROM geoname AS g JOIN ( SELECT $latitude AS latpoint, $longitude AS longpoint, $radius AS radius, 111.045 AS distance_unit ) AS p ON 1=1 WHERE g.latitude_float BETWEEN p.latpoint - (p.radius / p.distance_unit) AND p.latpoint + (p.radius / p.distance_unit) AND g.longitude_float BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint)))) AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint)))) ) AS d WHERE distance <= radius ORDER BY distance LIMIT 15";
		$result=$this->dbAdapter->query( $query, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE );
		$locations=array();
		while( $result->current() ){
			array_push( $locations, $result->current()[ 'name_fulnam' ] );
			$result->next();
		}
		return $locations;

	}
}
?>
