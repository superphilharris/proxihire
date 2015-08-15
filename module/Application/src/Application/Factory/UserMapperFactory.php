<?php
namespace Application\Factory;

use Application\Mapper\UserMapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserMapperFactory implements FactoryInterface
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

		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new \Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy($dbStructure->columns);
		$hydrator->setNamingStrategy($namingStrategy);

		return new UserMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new \Application\Model\User,
			$dbStructure
		);
	}
}
?>
