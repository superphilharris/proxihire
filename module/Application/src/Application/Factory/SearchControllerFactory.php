<?php
namespace Application\Factory;

use Application\Controller\SearchController;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchControllerFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 *
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$realServiceLocator     = $serviceLocator->getServiceLocator();
		$assetService           = $realServiceLocator->get('Application\Service\AssetServiceInterface');
		$categoryAliasesService = $realServiceLocator->get('Application\Service\CategoryAliasesServiceInterface');

		return new SearchController($assetService,$categoryAliasesService);
	}
}
?>
