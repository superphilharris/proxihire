<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\UserInterface;

class UserMapper extends AbstractMapper implements UserMapperInterface
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
			"array|Application\Model\UserInterface",
			"object"));
		
		parent::construct( $dbAdapter, $hydrator, $userPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBranches($branchMapper,$reload=false)
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Mapper\BranchMapperInterface",
			"boolean"
		));

		return $this->getSubObject($branchMapper,'branch','getBranchIds','getBranches','setBranches');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\UserInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
