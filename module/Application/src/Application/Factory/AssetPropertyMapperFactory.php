<?php
namespace Application\Factory;

use Application\Mapper\AssetPropertyMapper;
use Application\Model\AssetProperty;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetPropertyMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'table' => 'asset_property',
			'primary_key' => 'asset_property_id',
			'columns' => array(
				'asset_property_id' => 'id',
				'asset_id' => 'asset_id',
				'name_fulnam' => 'name',
				'datatype_id' => 'datatype_id',
				'value_mxd' => 'value'));

		$datatypeMapperFactory = new DatatypeMapperFactory();

		return new AssetPropertyMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new AssetProperty,
			$dbStructure,
			$datatypeMapperFactory->createService($serviceLocator)
		);
	}
}
?>
