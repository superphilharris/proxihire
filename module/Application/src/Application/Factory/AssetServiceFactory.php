<?php
namespace Application\Factory;

use Application\Service\AssetService;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetServiceFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$assetMapperFactory=new AssetMapperFactory();
		$urlMapperFactory=new UrlMapperFactory();
		$assetRateMapperFactory=new AssetRateMapperFactory();
		$assetPropertyMapperFactory=new AssetPropertyMapperFactory();
		return new AssetService(
			new \Application\Model\Asset,
			$assetMapperFactory->createService($serviceLocator),
			$urlMapperFactory->createService($serviceLocator),
			$assetRateMapperFactory->createService($serviceLocator),
			$assetPropertyMapperFactory->createService($serviceLocator)
		);
	}
}
?>
