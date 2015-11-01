<?php
namespace Application\Factory;

use Application\Service\GeonameService;
use Application\Model\Geoname;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GeonameServiceFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$geonameMapperFactory=new GeonameMapperFactory();
		return new GeonameService(
			new Geoname,
			$geonameMapperFactory->createService($serviceLocator)
		);
	}
}
?>
