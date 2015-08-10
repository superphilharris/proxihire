<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Application\Model\CategoryInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface;

class CategoryMapper extends AbstractMapper implements CategoryMapperInterface
{
	protected static $dbAliasTable = 'category_alias';

	/**
	 * @param AdapterInterface $dbAdapter
	 * @param AdapterInterface $hydrator
	 * @param AdapterInterface $categoryPrototype
	 */
	public function __construct(
		AdapterInterface $dbAdapter,
		HydratorInterface $hydrator,
		CategoryInterface $categoryPrototype,
		$dbStructure,
		$namingStrategy
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"Application\Model\CategoryInterface",
			"object",
			"null|Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy"));
		parent::construct($dbAdapter,$hydrator,$categoryPrototype, $dbStructure, $namingStrategy);
	}

	/**
	 * {@inheritdoc}
	 */
	public function findByName( $categoryName )
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("string"));
		$result = $this->runSelect( $this->dbTable, array(
			$this->namingStrategy->extract('name')." = ?" => $categoryName,
			// jih: once removed namingStrategy, also remove it from AbstractMapper
		));

		$categoryArray=$result->current();
		if( isset($categoryArray[$this->namingStrategy->extract('id')]) )
		{
			$categoryArray['aliases'] = $this->findAlias($categoryArray[$this->namingStrategy->extract('id')]);
		}
		return $this->hydrator->hydrate($categoryArray, $this->prototypeArray[0]);
	}

	private function findAlias($id)
	{
		$result = $this->runSelect( self::$dbAliasTable, array(
			$this->namingStrategy->extract('id')." = ?" => $id
		));

		$aliases=array();
		$current=$result->current();
		while ( $current ){
			if( isset($current[$this->namingStrategy->extract('alias.name')]) ){
				$aliases[]=$current[$this->namingStrategy->extract('alias.name')]; 
			}
			$result->next();
			$current=$result->current();
		}

		if( empty($aliases) ){
			return false;
		}
		return $aliases;
	}

}
?>
