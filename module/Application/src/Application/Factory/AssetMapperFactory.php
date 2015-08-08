<?php
namespace Application\Factory;

use Application\Mapper\AssetMapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetMapperFactory implements FactoryInterface
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
			'table'         => 'asset',
			'primary_key'   => 'asset_id',
			'columns'       => array(
				'asset_id'       => 'id',
				'category_id'    => 'category_id',
				'url_id'         => 'url_id',
				'lessor_user_id' => 'lessor_id',
				'asset_property' => 'property_id_array',
				'asset_rate'     => 'rate_id_array'),
			'relationships' => array(
				(object) array(
					'table'       => 'asset_property',
					'primary_key' => 'asset_property_id',
					'match_on'    =>(object) array(
						'this_table_column' => 'asset_id',
						'main_table_column' => 'asset_id')),
				(object) array(
					'table'       => 'asset_rate',
					'primary_key' => 'asset_rate_id',
					'match_on'    =>(object) array(
						'this_table_column' => 'asset_id',
						'main_table_column' => 'asset_id'))));

		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new \Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy(array());

		$assetRateMapperFactory = new AssetRateMapperFactory();
		$assetPropertyMapperFactory = new AssetPropertyMapperFactory();

		return new AssetMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new \Application\Model\Asset,
			$dbStructure,
			$assetRateMapperFactory->createService($serviceLocator),
			$assetPropertyMapperFactory->createService($serviceLocator),
			$namingStrategy
		);
	}
}
?>
