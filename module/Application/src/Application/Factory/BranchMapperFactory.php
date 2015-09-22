<?php
namespace Application\Factory;

use Application\Mapper\BranchMapper;
use Application\Model\Branch;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BranchMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'table'         => 'branch',
			'primary_key'   => 'branch_id',
			'columns'       => array(
				'branch_id'     => 'id',
				'location_id'   => 'location_id'));

		return new BranchMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new Branch,
			$dbStructure
		);
	}
}
?>
