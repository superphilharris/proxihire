<?php
namespace Application\Factory;

use Application\Mapper\DatatypeMapper;
use Application\Model\Datatype;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DatatypeMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'table' => 'datatype',
			'primary_key' => 'datatype_id',
			'columns' => array(
				'datatype_id'   => 'id',
				'datatype_abbr' => 'datatype'));

		return new DatatypeMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new Datatype,
			$dbStructure
		);
	}
}
?>

