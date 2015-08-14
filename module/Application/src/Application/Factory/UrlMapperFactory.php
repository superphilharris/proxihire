<?php
namespace Application\Factory;

use Application\Mapper\UrlMapper;
use Application\Model\Url;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class UrlMapperFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		// jih: put the following in a 'AbstractMapperFactory', and clean up the 
		//      existing mapper factories

		$dbStructure=(object) array(
			'table' => 'url',
			'primary_key' => 'url_id',
			'columns' => array(
				'url_id' => 'id',
				'path_url' => 'path',
				'title_desc' => 'title',
				'clicks_cnt' => 'clicks'));
		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new MapNamingStrategy($dbStructure->columns);
		$hydrator->setNamingStrategy($namingStrategy);

		return new UrlMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new Url,
			$dbStructure
		);
	}
}
?>
