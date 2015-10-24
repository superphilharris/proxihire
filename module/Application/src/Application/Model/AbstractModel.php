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
		return get_object_vars( $this );
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
}
?>
