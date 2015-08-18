<?php
namespace Application\Factory;

use Application\Mapper\LocationMapper;
use Application\Model\Location;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocationMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'table' => 'location',
			'primary_key' => 'location_id',
			'columns' => array(
				'location_id'     => 'id',
				'name_fulnam'     => 'name',
				'latitude_float'  => 'latitude',
				'longitude_float' => 'longitude'));

		return new LocationMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new Location,
			$dbStructure
		);
	}
}
?>
