<?php
namespace ScraperImporter\Factory;

use ScraperImporter\Service\ImporterService;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ImporterServiceFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
	 * @return ScraperImporter\Service\ImporterService
	 */
	public function createService(
		ServiceLocatorInterface $serviceLocator
	){
		return new ImporterService(
			$serviceLocator->get('Application\Mapper\AssetMapperInterface'),
			$serviceLocator->get('Application\Model\AssetInterface'),
			$serviceLocator->get('Application\Mapper\AssetPropertyMapperInterface'),
			$serviceLocator->get('Application\Model\AssetPropertyInterface'),
			$serviceLocator->get('Application\Mapper\AssetRateMapperInterface'),
			$serviceLocator->get('Application\Model\AssetRateInterface'),
			$serviceLocator->get('Application\Mapper\CategoryMapperInterface'),
			$serviceLocator->get('Application\Model\CategoryInterface'),
			$serviceLocator->get('Application\Mapper\LessorMapperInterface'),
			$serviceLocator->get('Application\Model\LessorInterface'),
			$serviceLocator->get('Application\Mapper\LocationMapperInterface'),
			$serviceLocator->get('Application\Model\LocationInterface'),
			$serviceLocator->get('Application\Mapper\UrlMapperInterface'),
			$serviceLocator->get('Application\Model\UrlInterface')
		);
	}
}
