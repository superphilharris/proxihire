<?php
namespace Application\Factory;

use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

abstract class AbstractMapperFactory
{
	protected function getMappingHydrator( $map )
	{
		$hydrator=new ArraySerializable;
		$namingStrategy = new MapNamingStrategy($map);
		$hydrator->setNamingStrategy($namingStrategy);
		return $hydrator;
	}
}
?>
