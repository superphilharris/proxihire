<?php
namespace Application\Model;

use Application\Helper\ClassHelper;

abstract class AbstractModel implements HydratableModelInterface
{
	public function __tostring(){
		return \Zend\Debug\Debug::dump($this,null,false); // get_class($this);
	}

	/**
	 * {@inheritdoc}
	 * 
	 */
	public function getArrayCopy(){
		$properties=array();
		foreach( get_object_vars( $this ) as $property => $value ){
			if( ! $this->isObject($property) AND ! preg_match("/_array$/",$property) ){
				$properties[ $property ] = $value;
			}
		}
		return $properties;
	}

	/**
	 * Sets the id of the model
	 *
	 * @param integer $id - The id of the model
	 */
	public function setId($id){
		$this->id = (integer) $id;
	}

	/**
	 * Returns the id of the model
	 *
	 * @return integer - The id of the model
	 */
	public function getId(){
		if( isset($this->id) ){
			return (integer) $this->id;
		}
		return 0;
	}

	/**
	 * Updates all fields that correspond with subObject ids with the id of the 
	 * subobject.
	 */
	public function updateIds(){
		foreach( get_object_vars($this) as $property => $value ){
			// if $property is an id, but is different from the id of the 
			// corresponding object is populated, then get its id.
			if( $this->isObject($property) AND !is_null($value) AND method_exists($value,'getId') ){ 
				$tmp=$property."_id";
				$this->$tmp = $this->$property->getId();
			}
		}
	}

	/**
	 * Returns the specified property
	 *
	 * @return mixed
	 */
	public function get( $property ){
		if( isset( $this->$property ) ){
			return $this->$property;
		}
		return NULL;
	}

	/**
	 * Returns true if the argument is one of $this' properties and is a 
	 * subobject.
	 */
	private function isObject( $objectName ){
		$allProperties = get_object_vars($this);

		// if 'objectName_id' and 'objectName' are both properties, then 
		// objectName is a property
		$property = $objectName."_id";
		if( array_key_exists($objectName,$allProperties) AND array_key_exists($property,$allProperties) ){ 
			return true;
		}

		return false;
	}
}
?>
