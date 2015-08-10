<?php
namespace Application\Model;

use Application\Helper\ClassHelper;

abstract class AbstractModel implements HydratableModelInterface
{
	public function __tostring(){
		return get_class($this); //\Zend\Debug\Debug::dump($this,null,false);
	}
}
?>
