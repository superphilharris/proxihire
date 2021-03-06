<?php
namespace Application\Factory;

use Application\Mapper\AssetMapper;
use Application\Model\Asset;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'update_key'    => array(
				'lessor_user_id',
				'description_text'
			),
			'columns'       => array(
				'asset_id'         => 'id',
				'category_id'      => 'category_id',
				'url_id'           => 'url_id',
				'lessor_user_id'   => 'lessor_id',
				'image_url'        => 'image_url',
				'description_text' => 'description',
				'asset_property'   => 'property_id_array',
				'asset_rate'       => 'rate_id_array'),
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

		$assetRateMapperFactory = new AssetRateMapperFactory();
		$assetPropertyMapperFactory = new AssetPropertyMapperFactory();

		return new AssetMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new Asset,
			$dbStructure,
			$serviceLocator->get('Application\Mapper\AssetRateMapperInterface'),
			$serviceLocator->get('Application\Mapper\AssetPropertyMapperInterface')
		);
	}
}
?>
