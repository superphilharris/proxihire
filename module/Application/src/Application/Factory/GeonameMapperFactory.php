<?php
namespace Application\Factory;

use Application\Mapper\GeonameMapper;
use Application\Model\Geoname;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GeonameMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'table'         => 'geoname',
			'primary_key'   => 'geoname_id',
			'update_key'    => array(
				'name_fulnam' // jih: is this unique?
			),
			'columns'       => array(
				'geoname_id'       => 'id',
				'name_fulnam'      => 'name',
				'latitude_float'   => 'latitude',
				'longitude_float'  => 'longitude'));

		return new GeonameMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new Geoname,
			$dbStructure
		);
	}
}
?>
