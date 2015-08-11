<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\AssetPropertyInterface;
use Application\Model\LessorInterface;

class AssetPropertyMapper extends AbstractMapper implements AssetPropertyMapperInterface
{
	protected $dbTable;

	/**
	 * @param Zend\Db\Adapter\AdapterInterface $dbAdapter
	 * @param Zend\Stdlib\HydratorInterface $hydrator
	 * @param AdapterInterface $assetPropertyPrototypeArray
	 */
	public function __construct(
		$dbAdapter,
		$hydrator,
		$assetPropertyPrototypeArray,
		$dbStructure,
		$datatypeMapper
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"array|Application\Model\AssetPropertyInterface",
			"object",
			"Application\Mapper\DatatypeMapperInterface"));

		$this->datatypeMapper=$datatypeMapper; // jih: make sure that this is actually populating things
		parent::construct( $dbAdapter, $hydrator, $assetPropertyPrototypeArray, $dbStructure );
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterRetrieval()
	{
		$this->getSubObject($this->datatypeMapper,'datatype','getDatatypeId','getDatatype','setDatatype');
	}

	/**
	 * {@inheritdoc}
	 */
	public function setPrototypeArray( $prototypeArray ){ 
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|Application\Model\AssetPropertyInterface"));
		parent::setPrototypeArray( $prototypeArray );
	}
}
?>
