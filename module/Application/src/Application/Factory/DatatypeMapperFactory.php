<?php
namespace Application\Factory;

use Application\Mapper\DatatypeMapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
// jih: this should really be in its own factory. However, it will be 
//      specific to this factory, so one will need to be created for all Mapper 
//      factories
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class DatatypeMapperFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{

		$dbStructure=(object) array(
			'table' => 'datatype',
			'primary_key' => 'datatype_id',
			'columns' => array(
				'datatype_id'   => 'id',
				'datatype_abbr' => 'datatype'));

		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new MapNamingStrategy($dbStructure->columns);
		$hydrator->setNamingStrategy($namingStrategy);

		return new DatatypeMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new \Application\Model\Datatype, // jih: move this (and in all mappers) to begining
			$dbStructure
		);
	}
}
?>

