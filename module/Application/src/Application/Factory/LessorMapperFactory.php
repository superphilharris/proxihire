<?php
namespace Application\Factory;

use Application\Mapper\LessorMapper;
use Application\Model\Lessor;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LessorMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
				'url_id'      => 'url_id',
				'branch'      => 'branch_id_array'),
			'relationships' => array(
				(object) array(
					'table'       => 'branch',
					'primary_key' => 'branch_id',
					'match_on'    =>(object) array(
						'this_table_column' => 'user_id',
						'main_table_column' => 'user_id'))));

		return new LessorMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new Lessor,
			$dbStructure
		);
	}
}
?>
