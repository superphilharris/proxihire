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
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$realServiceLocator     = $serviceLocator->getServiceLocator();

		$assetService           = $realServiceLocator->get('Application\Service\AssetServiceInterface');
		$categoryService        = $realServiceLocator->get('Application\Service\CategoryServiceInterface');
		$categoryAliasesService = $realServiceLocator->get('Application\Service\CategoryAliasesServiceInterface');
		$geonameService         = $realServiceLocator->get('Application\Service\GeonameServiceInterface');

		return new SearchController(
			$assetService,
			$categoryService,
			$categoryAliasesService,
			$geonameService
		);
	}
}
?>
