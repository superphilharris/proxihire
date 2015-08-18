<?php
namespace Application\Factory;

use Application\Mapper\UserMapper;
use Application\Model\User;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'table'         => 'user',
			'primary_key'   => 'user_id',
			'columns'       => array(
				'user_id'     => 'id',
				'location_id' => 'location_id',
				'name_fulnam' => 'name'));

		return new UserMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new User,
			$dbStructure
		);
	}
}
?>
