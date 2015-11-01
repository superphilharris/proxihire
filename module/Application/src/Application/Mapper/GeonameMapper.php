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
}
?>
