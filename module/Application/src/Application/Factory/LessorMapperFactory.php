<?php
namespace Application\Factory;

use Application\Mapper\LessorMapper;
use Application\Model\Lessor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LessorMapperFactory implements FactoryInterface
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
			'table'       => 'user',
			'primary_key' => 'user_id',
			'join'       => (object) array(
				'table'       => 'lessor',
				'on'          => 'user.user_id = lessor.lessor_user_id'),
			'columns'     => array(
				'user_id'     => 'id',
				'location_id' => 'location_id',
				'name_fulnam' => 'name',
				'url_id'      => 'url_id'));

		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new \Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy($dbStructure->columns);
		$hydrator->setNamingStrategy($namingStrategy);

		return new LessorMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new Lessor(),
			$dbStructure
		);
	}
}
?>
