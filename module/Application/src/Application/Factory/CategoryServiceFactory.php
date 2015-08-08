<?php
namespace Application\Factory;

use Application\Service\CategoryService;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryServiceFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$categoryMapperFactory=new CategoryMapperFactory();
		return new CategoryService(
			$categoryMapperFactory->createService($serviceLocator)
		);
	}
}
?>
