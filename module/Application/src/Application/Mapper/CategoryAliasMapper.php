<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\CategoryAliasInterface;

class CategoryAliasMapper extends AbstractMapper implements CategoryAliasMapperInterface
{
	/**
	 * @param Zend\Db\Adapter\AdapterInterface $dbAdapter
	 * @param Zend\Stdlib\HydratorInterface $hydrator
	 * @param AdapterInterface $categoryAliasPrototypeArray
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$categoryAliasPrototypeArray,
		$dbStructure
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\CategoryAliasInterface",
			"object"));

		parent::construct( $dbAdapter, $hydrator, $categoryAliasPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\CategoryAliasInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
