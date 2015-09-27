<?php
namespace Application\Mapper;

use Application\Helper\ClassHelper;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

abstract class AbstractMapper
{
	/**
	 * @var \Zend\Db\Adapter\AdapterInterface
	 */
	protected $dbAdapter;
	/**
	 * @var string
	 */
	protected $dbTable;
	/**
	 * @var \Zend\Stdlib\Hydrator\HydratorInterface
	 */
	protected $hydrator;
	/**
	 * @var array|Application\Model\AbstractModel
	 */
	protected $prototypeArray;
	/**
	 * This array specifies whether or not the specified sub-objects have been 
	 * reloaded since the last load of the AbstractModel from the database.
	 *
	 * @var array|boolean
	 */
	protected $isLoaded;
	/**
	 * This array holds the dbTable => idColumn pairs of related objects where 
	 * the current model has a one-to-many relationship with that other object.
	 *
	 * @var array|string
	 */
	protected $otherTableStructureArray;
	/**
	 * The column that corresponds with the primary key for this table.
	 *
	 * @var string
	 */
	protected $primaryKey;
	/**
	 * A hashed array that contains the $columnName => $variableName mapping 
	 * pairs for this database table.
	 *
	 * @var array
	 */
	protected $columnMap;
	/**
	 * A hashed array that contains the table join information, if needed.
	 *
	 * @var array
	 */
	protected $join;


	/**
	 * @param Zend\Db\Adapter\AdapterInterface $dbAdapter
	 * @param Zend\Stdlib\HydratorInterface $hydrator
	 */
	protected function construct(
		$dbAdapter,
		$hydrator,
		$prototypeArray,
		$dbTableStructure
	){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			"Zend\Db\Adapter\AdapterInterface", 
			"Zend\Stdlib\Hydrator\HydratorInterface&Zend\Stdlib\Hydrator\NamingStrategyEnabledInterface",
			"array|Application\Model\AbstractModel",
			"object"
		));


		$this->dbAdapter        = $dbAdapter;
		$this->hydrator         = $hydrator;
		$this->isLoaded         = array(); 

		$this->setDbTableStructure( $dbTableStructure );
		$this->setPrototypeArray( $prototypeArray );
	}

	/**
	 * Finds the object by id
	 *
	 * @param int|array $id
	 * @returns Zend\Db\Adapter\Driver\ResultInterface
	 */
	public function find( $idArray )
	{
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array("array|integer"));
		return $this->findBy( $this->primaryKey, $idArray, true, true );
	}

	/**
	 * Finds the objects by the specified property.
	 *
	 * @param string $property 
	 *           The column name 
	 * @param array|string $matchArray
	 *           An array of items that $property should match one of.
	 * @param boolean $createNewPrototypesArray
	 *           If true, then create a new prototypes array.
	 *           If false, then overwrite the existing prototypes array. This 
	 *           means that the maximum number of results is equal to the current 
	 *           number of items in the prototype array.
	 *           Default false.
	 * @param boolean $ordered
	 *           If true, then the the prototypes array will be populated in the 
	 *           same order as $matchArray. 
	 *
	 *           > NB: This means that if an item in 
	 *           $matchArray has multiple matches, only one match will be found 
	 *           for it. 
	 *
	 *           > NB: This does allow for duplicates in the returned array, if 
	 *           more than one item in the $matchArray points to the same 
	 *           $property
	 *
	 *           If false, then the prototypes array will be in no particular 
	 *           order, but it *will* include all matches on $matchArray.
	 *
	 *           Default false.
	 * @return array|Application\Model\AbstractModel
	 */
	protected function findBy( $property, $matchArray, $createNewPrototypesArray=false, $ordered=false ){
		// Validate arguments
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"string",
			"array|string|integer",
			"boolean",
			"boolean"
		));

		if( !is_array($matchArray) ){
			$matchArray=array($matchArray);
		}

		// When creating a new model, use the last item of the prototype array as 
		// the prototype.
		$prototype=array_values($this->prototypeArray)[0];

		// Run the query to find all objects with one of the specified ids
		$where=$this->wherePropertyEquals( $property, $matchArray );

		$result = $this->runSelect( $this->dbTable, $where, $this->join );


		// Get all of the models from the database
		$resultArrays=array();
		$modelsByProperty=array();
		$i=0;
		while( $result->current() 
		       AND ( $i < count($this->prototypeArray) 
		             OR $createNewPrototypesArray ))
		{
			// this allows population of more data
			$resultArrays[$i]=$result->current(); 

			// When there is a one-to-many relationship to another object, select 
			// the id columns from the other table also.
			$tmpMatchArray=array();

			// Load up all the subobjects
			foreach( $this->otherTableStructureArray as $key => $structure ){
				if( isset( $resultArrays[$i][$structure->match_on->main_table_column] ) ){
					try{
						$where = $this->wherePropertyEquals( $structure->match_on->this_table_column, $resultArrays[$i][$structure->match_on->main_table_column] );
						if( isset( $structure->join ) ){
							$result2 = $this->runSelect( $structure->table, $where, $structure->join );
						} else {
							$result2 = $this->runSelect( $structure->table, $where );
						}
						$resultArrays[$i][$structure->table]=array();
						while( $result2->current() ){
							$resultArrays[$i][$structure->table][]=$result2->current()[$structure->primary_key];
							$result2->next();
						}
					} catch( \Exception $e ){
						// Could not find any results
					}
				}
			}

			// if we want the results ordered, then order them by $property
			$key=($ordered)?$resultArrays[$i][$property]:$i;
			if($prototype !== null){ // psh TODO: Some Assets do not have a AssetRate
				$modelsByProperty[$key]=new $prototype;
				$this->hydrator->hydrate( $resultArrays[$i], $modelsByProperty[$key] );
			}
			$result->next();
			$i++;
		}

		$models=array();
		if( $ordered ){
			// Order the models to match the corresponding entries in $matchArray
			if( $prototype !== null ){
				foreach($matchArray as $key=>$match){
					$models[$key] = isset($modelsByProperty[$match]) ? $modelsByProperty[$match] : new $prototype;
				}
			}
		}else{
			$models=$modelsByProperty;
		}

		$this->setPrototypeArray($models);
		$this->afterRetrieval();
		return $this->prototypeArray;
	}

	/**
	 * A callback function that gets called after retrieval from the database
	 */
	protected function afterRetrieval(){
		// do nothing, as this should be implemented where necessary by the child 
		// objects.
	}

	/**
	 * Runs a select where on the specified table
	 *
	 * @param string $table
	 * @param array $where
	 * @param array $join
	 * @return Zend\Db\Adapter\Driver\ResultInterface
	 */
	protected function runSelect( $table, $where, $join=null )
	{
		$sql    = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select->from( $table );
		if( !is_null($join) ){
			$select->join( $join->table, $join->on );
		}
		$select->where( $where );

		$stmt   = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();

		if ( ! $result instanceof ResultInterface ||
		     ! $result->isQueryResult() )
		{
			throw new \InvalidArgumentException( "There are no entries in the '{$table}' table which match the condition: ".print_r( $where, true ) );
		}
		return $result;
	}

	/**
	 * Gets the mapper's prototypes.
	 *
	 * @return array|Application\Model\AbstractModel
	 */
	public function getPrototypeArray(){
		return $this->prototypeArray;
	}

	/**
	 * Sets the mapper's prototype(s).
	 *
	 * When this is called, it also forces a reload of the subobjects when they 
	 * next are requested.
	 *
	 * @param array|Application\Model\AbstractModel $prototype 
	 *        Can be either a scalar or an array
	 */
	public function setPrototypeArray( $prototype ){
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array( "array|Application\Model\AbstractModel" ));

		if( is_array($prototype) ) $this->prototypeArray=$prototype;
		else $this->prototypeArray=array($prototype);

		foreach( $this->isLoaded as $key => $value ){
			$this->isLoaded[$key] = false;
		}
	}



	/**
	 * Gets the subobject.
	 * 
	 * Will only reload them from the database if they haven't been loaded, or if 
	 * the `$reload` parameter is set to true.
	 * 
	 * @param Application\Mapper\AbstractMapper $subObjectMapper
	 *        The mapper used to populate the subobject.
	 * @param string $subObjectName
	 *        A unique name of the subobject. Used as a key to the array that 
	 *        stores info on this subobject.
	 * @param string $getSubObjectId
	 *        The name of the method of this model (not the subobject model) 
	 *        that gets the subobject IDs
	 * @param string $getSubObject
	 *        The name of the method of this model (not the subobject model) 
	 *        that gets the subobject itself.
	 * @param string $setSubObject
	 *        The name of the method of this model (not the subobject model) 
	 *        that sets the subobject.
	 * @param boolean $reload=false
	 *        Forces a reload from the database even if the models are 
	 *        up-to-date.
	 */
	protected function getSubObject(
		$subObjectMapper,
		$subObjectName,
		$getSubObjectId,
		$getSubObject,
		$setSubObject,
		$reload=false
	){
		ClassHelper::checkAllArguments(__METHOD__, func_get_args(), array(
			"Application\Mapper\AbstractMapper",
			"string",
			"string",
			"string",
			"string",
			"boolean"
		));

		$subObjects=array();
		// If we wish to force a reload from the database, or the subObjects have 
		// not yet been fetched since setting the prototypes, then reload them.
		if ( $reload OR ! isset($this->isLoaded[$subObjectName]) OR ! $this->isLoaded[$subObjectName] ){
			$idArray=array();
			$oneToOne=true;
			foreach( $this->prototypeArray as $key => $prototype ){
				$idArray[$key]=$prototype->$getSubObjectId();
				if( is_array( $idArray[ $key ] ) ) $oneToOne=false;
			}
			if( $oneToOne ){
				$subObjectMapperCopy=clone $subObjectMapper;
				$subObjects=$subObjectMapperCopy->find( $idArray );
			}
			foreach( $this->prototypeArray as $key => $prototype ){

				// This is needed as $subObjects can be modified by reference.
				$subObjectsCopy=$subObjects; 
				$subObjectMapperCopy=clone $subObjectMapper;

				if( isset($subObjectsCopy[$key]) ){
					$prototype->$setSubObject( $subObjectsCopy[$key] );
				} else {
					$subObjectsCopy=$subObjectMapperCopy->find($idArray[$key]);
					$prototype->$setSubObject( $subObjectsCopy );
				}
			}
			$this->isLoaded[$subObjectName]=true;
		}
		$arrayToReturn=array();
		foreach( $this->prototypeArray as $key => $prototype){
			$arrayToReturn[$key]=$prototype->$getSubObject();
		}
		return $arrayToReturn;
	}
	private function wherePropertyEquals( $property, $matchArray ){
		$where = new Where();
		$matchArray=is_array($matchArray)?$matchArray:array($matchArray);
		$firstloop=true;
		foreach($matchArray as $match){
			if($firstloop){
				$firstloop=false;
			}else{
				$where->or;
			}
			$where->equalTo($property,$match);
		}
		return $where;
	}



	/**
	 * Decodes the table structure passed in the constructor, and uses it to set 
	 * $this->otherTableStructureArray;
	 *
	 * @param object $tableStructure
	 * @return object
	 */
	private function setDbTableStructure($tableStructure){
		ClassHelper::checkAllArguments( __METHOD__, func_get_args(),  array( 
			'object'
	  	));

		$this->checkPropertiesExist( $tableStructure, array( "table", "primary_key", "columns" ) );

		$this->dbTable    = $tableStructure->table;
		$this->primaryKey = $tableStructure->primary_key;
		$this->columnMap  = $tableStructure->columns;

		$this->join = null;
		if( isset( $tableStructure->join ) ){
			$this->checkPropertiesExist( $tableStructure->join, array( "table", "on" ) );
			$this->join=$tableStructure->join;
		}

		$this->otherTableStructureArray = array();
		if( isset( $tableStructure->relationships )){
			foreach( $tableStructure->relationships AS $index => $relationship ){
				try{
					$this->checkPropertiesExist( $relationship, array( "table", "primary_key", "match_on" ) );
					$this->checkPropertiesExist( $relationship->match_on, array( "this_table_column", "main_table_column" ) );
					$this->otherTableStructureArray[$index] = $relationship;
				}catch(\Exception $e){
					trigger_error($e->getMessage());
				}
			}
		}
	}



	/**
	 * Checks to see whether all of the properties specified exist in the object. 
	 * Throws an InvalidArgumentException if they don't.
	 *
	 * @param object $object
	 * @param array $properties
	 * @throws InvalidArgumentException
	 */
	private function checkPropertiesExist( $object, $properties ){
		foreach( $properties as $property ){
			if( !isset( $object->$property )){
				throw new \InvalidArgumentException(
					"Incorrectly formatted object. Must have the '$property' property.");
			}
		}
	}

}
?>
