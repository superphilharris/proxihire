<?php
namespace Application\Factory;

use Application\Mapper\UrlMapper;
use Application\Model\Url;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class UrlMapperFactory extends AbstractMapperFactory implements FactoryInterface
{
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$dbStructure=(object) array(
			'table' => 'url',
			'primary_key' => 'url_id',
			'update_key'  => array(
				'path_url',
				'title_desc'
			),
			'columns' => array(
				'url_id' => 'id',
				'path_url' => 'path',
				'title_desc' => 'title',
				'clicks_cnt' => 'clicks'));

		return new UrlMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new Url,
			$dbStructure
		);
	}
}
?>
