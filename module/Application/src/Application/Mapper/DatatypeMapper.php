<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\DatatypeInterface;

class DatatypeMapper extends AbstractMapper implements DatatypeMapperInterface
{
	/**
	 * @param Zend\Db\Adapter\AdapterInterface $dbAdapter
	 * @param Zend\Stdlib\HydratorInterface $hydrator
	 * @param AdapterInterface $datatypePrototypeArray
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$datatypePrototypeArray,
		$dbStructure,
		$namingStrategy
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\DatatypeInterface",
			"object",
			"null|Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy"));

		parent::construct( $dbAdapter, $hydrator, $datatypePrototypeArray, $dbStructure, $namingStrategy );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\DatatypeInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
