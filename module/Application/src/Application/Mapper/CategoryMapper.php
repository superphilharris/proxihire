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
	private $categoryAliasMapper;

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
		$categoryAliasMapper,
		$namingStrategy
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"Application\Model\CategoryInterface",
			"object",
			"Application\Mapper\CategoryAliasMapper",
			"null|Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy"));
		$this->categoryAliasMapper=$categoryAliasMapper;
		parent::construct($dbAdapter,$hydrator,$categoryPrototype, $dbStructure, $namingStrategy);
	}

	/**
	 * {@inheritdoc}
	 */
	public function findByName( $categoryName )
	{
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("string"));
		$nameColumn="";
		foreach( $this->columnMap  AS $column => $variable ){
			if( $variable == 'name' ){
				$nameColumn = $column;
			}
		}
		return parent::findBy( $nameColumn, $categoryName );
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterRetrieval()
	{
		$aliases=$this->getSubObject(
			$this->categoryAliasMapper,
			'category_alias',
			'getAliasIds',
			'getAliases',
			'setAliases' );
	}
}
?>
