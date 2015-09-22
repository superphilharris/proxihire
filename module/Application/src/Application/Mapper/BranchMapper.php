<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\BranchInterface;

class BranchMapper extends AbstractMapper implements BranchMapperInterface
{
	/**
	 * @param AdapterInterface $dbAdapter
	 * @param AdapterInterface $hydrator
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$branchPrototypeArray,
		$dbStructure
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\BranchInterface",
			"object"));
		
		parent::construct( $dbAdapter, $hydrator, $branchPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLocation($locationMapper,$reload=false)
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
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\BranchInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
