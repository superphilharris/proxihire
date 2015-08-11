<?php
namespace Application\Factory;

use Application\Mapper\AssetPropertyMapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
// jih: this should really be in its own factory. However, it will be 
//      specific to this factory, so one will need to be created for all Mapper 
//      factories
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class AssetPropertyMapperFactory implements FactoryInterface
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
			'table' => 'asset_property',
			'primary_key' => 'asset_property_id',
			'columns' => array(
				'asset_property_id' => 'id',
				'asset_id' => 'asset_id',
				'name_fulnam' => 'name',
				'datatype_id' => 'datatype_id',
				'value_mxd' => 'value'));

		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new MapNamingStrategy($dbStructure->columns);
		$hydrator->setNamingStrategy($namingStrategy);

		$datatypeMapperFactory = new DatatypeMapperFactory();

		return new AssetPropertyMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new \Application\Model\AssetProperty,
			$dbStructure,
			$datatypeMapperFactory->createService($serviceLocator)
		);
	}
}
?>
