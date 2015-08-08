<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\AssetRateInterface;
use Application\Model\LessorInterface;

class AssetRateMapper extends AbstractMapper implements AssetRateMapperInterface
{
	/**
	 * @param Zend\Db\Adapter\AdapterInterface $dbAdapter
	 * @param Zend\Stdlib\HydratorInterface $hydrator
	 * @param AdapterInterface $assetRatePrototypeArray
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$assetRatePrototypeArray,
		$dbStructure,
		$namingStrategy
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\AssetRateInterface",
			"object",
			"null|Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy"));

		parent::construct( $dbAdapter, $hydrator, $assetRatePrototypeArray, $dbStructure, $namingStrategy );
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\AssetRateInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
