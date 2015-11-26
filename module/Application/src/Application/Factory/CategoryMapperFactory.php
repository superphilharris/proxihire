<?php
namespace Application\Factory;

use Application\Mapper\CategoryMapper;
use Application\Model\Category;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CategoryMapperFactory extends AbstractMapperFactory implements FactoryInterface
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
			'primary_key'   => 'category_id',
			'update_key'    => array(
				'name_fulnam'),
			'columns'       => array(
				'category_id'        => 'id',
				'name_fulnam'        => 'name',
				'loads_cnt'          => 'loads',
				'parent_category_id' => 'parent_id',
				'category_alias'     => 'alias_id_array'),
			'relationships' => array(
				(object) array(
					'table'           => 'category_alias',
					'primary_key'     => 'category_alias_id',
					'match_on'        =>(object) array(
						'this_table_column' => 'category_id',
						'main_table_column' => 'category_id'))));

		$categoryAliasMapperFactory=new CategoryAliasMapperFactory();

		return new CategoryMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$this->getMappingHydrator( $dbStructure->columns ),
			new Category,
			$dbStructure,
			$categoryAliasMapperFactory->createService( $serviceLocator )
		);
	}
}
?>
