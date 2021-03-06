<?php
namespace Application\Factory;

use Application\Service\AssetService;
use Application\Model\Asset;

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
		$lessorMapperFactory=new LessorMapperFactory();
		$locationMapperFactory=new LocationMapperFactory();
		$assetRateMapperFactory=new AssetRateMapperFactory();
		$assetPropertyMapperFactory=new AssetPropertyMapperFactory();
		$branchMapperFactory=new BranchMapperFactory();
		$categoryMapperFactory=new CategoryMapperFactory();
		return new AssetService(
			new Asset,
			$assetMapperFactory->createService($serviceLocator),
			$urlMapperFactory->createService($serviceLocator),
			$lessorMapperFactory->createService($serviceLocator),
			$locationMapperFactory->createService($serviceLocator),
			$assetRateMapperFactory->createService($serviceLocator),
			$assetPropertyMapperFactory->createService($serviceLocator),
			$branchMapperFactory->createService($serviceLocator),
			$categoryMapperFactory->createService($serviceLocator)
		);
	}
}
?>
