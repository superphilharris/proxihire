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
		$categoryAliasMapper
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface", 
			"Application\Model\CategoryInterface",
			"object",
			"Application\Mapper\CategoryAliasMapper"));

		$this->categoryAliasMapper=$categoryAliasMapper;
		parent::construct($dbAdapter,$hydrator,$categoryPrototype, $dbStructure );
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
	public function getPopularCategories( $categoryList, $limit=5 ){
		$sql="SELECT ".$this->primaryKey." FROM ".$this->dbTable." WHERE name_fulnam IN ('".implode("','",$categoryList)."') ORDER BY loads_cnt DESC LIMIT $limit";

		$statement = $this->dbAdapter->query($sql);
		$result = $statement->execute();

		$idArray=array();
		while( $result->current() ){
			array_push( $idArray,(int) $result->current()['category_id'] );
			$result->next();
		}
		$this->find($idArray);
		return $this->getPrototypeArray();
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
