<?php
namespace Application\Factory;

use Application\Mapper\AssetRateMapper;
use Application\Model\AssetRate;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetRateMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'table' => 'asset_rate',
			'primary_key' => 'asset_rate_id',
			'columns' => array(
				'asset_rate_id' => 'id',
				'duration_hrs' => 'duration',
				'price_dlr' => 'price',
				'asset_id' => 'asset_id'));

		return new AssetRateMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new AssetRate,
			$dbStructure
		);
	}
}
?>
