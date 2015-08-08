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
		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;

		$dbStructure=(object) array(
			'table' => 'asset_property',
			'primary_key' => 'asset_property_id',
			'columns' => array(
				'asset_property_id' => 'id',
				'asset_id' => 'asset_id',
				'name_fulnam' => 'name',
				'datatype_id' => 'datatype_id',
				'value_mxd' => 'value'),
			'relationships' => array(
				(object) array(
					'table' => 'datatype',
					'primary_key' => 'datatype_id',
					'match_on' =>(object) array(
						'this_table_column' => 'datatype_id',
						'main_table_column' => 'datatype_id'))));

		$namingStrategy = new MapNamingStrategy(array());

		return new AssetPropertyMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new \Application\Model\AssetProperty,
			$dbStructure,
			$namingStrategy
		);
	}
}
?>
