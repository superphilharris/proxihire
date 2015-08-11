<?php
namespace Application\Factory;

use Application\Mapper\CategoryMapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
// jih: this should really be in its own factory. However, it will be 
//      specific to this factory, so one will need to be created for all Mapper 
//      factories
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class CategoryMapperFactory implements FactoryInterface
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
			'table' => 'category',
			'primary_key' => 'category_id',
			'columns' => array(
				'category_id' => 'id',
				'name_fulnam' => 'name',
				'parent_category_id' => 'parent_id',
				'category_alias' => 'alias_id_array'),
			'relationships' => array(
				(object) array(
					'table' => 'category_alias',
					'primary_key' => 'category_alias_id',
					'match_on' =>(object) array(
						'this_table_column' => 'category_id',
						'main_table_column' => 'category_id'))));

		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new MapNamingStrategy($dbStructure->columns);
		$hydrator->setNamingStrategy($namingStrategy);
		$categoryAliasMapperFactory = new CategoryAliasMapperFactory();

		return new CategoryMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new \Application\Model\Category,
			$dbStructure,
			$categoryAliasMapperFactory->createService( $serviceLocator )
		);
	}
}
?>
