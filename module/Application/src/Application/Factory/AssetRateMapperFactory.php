<?php
namespace Application\Factory;

use Application\Mapper\AssetRateMapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
// jih: this should really be in its own factory. However, it will be 
//      specific to this factory, so one will need to be created for all Mapper 
//      factories
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class AssetRateMapperFactory implements FactoryInterface
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

		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new MapNamingStrategy($dbStructure->columns);
		$hydrator->setNamingStrategy($namingStrategy);

		return new AssetRateMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new \Application\Model\AssetRate,
			$dbStructure
		);
	}
}
?>
