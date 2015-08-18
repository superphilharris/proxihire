<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\LessorInterface;

class LessorMapper extends AbstractMapper implements LessorMapperInterface
{
	/**
	 * @param AdapterInterface $dbAdapter
	 * @param AdapterInterface $hydrator
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$userPrototypeArray,
		$dbStructure
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\LessorInterface",
			"object"));
		
		parent::construct( $dbAdapter, $hydrator, $userPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLocations($locationMapper,$reload=false)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Mapper\LocationMapperInterface",
			"boolean"
		));

		return $this->getSubObject($locationMapper,'location','getLocationId','getLocation','setLocation');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\LessorInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
