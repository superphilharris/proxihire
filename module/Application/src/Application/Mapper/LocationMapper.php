<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\LocationInterface;

class LocationMapper extends AbstractMapper implements LocationMapperInterface
{
	public function __construct(
		$dbAdapter,
		$hydrator,
		$locationPrototypeArray,
		$dbStructure
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\LocationInterface",
			"object"));

		parent::construct( $dbAdapter, $hydrator, $locationPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\LocationInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
