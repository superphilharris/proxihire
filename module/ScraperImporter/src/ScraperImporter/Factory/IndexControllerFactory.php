<?php

namespace ScraperImporter\Factory;

use ScraperImporter\Controller\IndexController;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return IndexController
	 */
	public function createService(
		ServiceLocatorInterface $serviceLocator
	){
		$realServiceLocator = $serviceLocator->getServiceLocator();
		$importerService    = $realServiceLocator->get('ScraperImporter\Service\ImporterServiceInterface');

		return new IndexController(
			$importerService
		);
	}
}
?>
