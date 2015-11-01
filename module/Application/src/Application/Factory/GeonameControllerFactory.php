<?php
namespace Application\Factory;

use Application\Controller\GeonameController;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GeonameControllerFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$realServiceLocator     = $serviceLocator->getServiceLocator();

		$geonameService         = $realServiceLocator->get('Application\Service\GeonameServiceInterface');

		return new GeonameController(
			$geonameService
		);
	}
}
?>
