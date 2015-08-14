<?php
namespace Application\Factory;

use Application\Mapper\CategoryAliasMapper;
use Application\Model\CategoryAlias;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy;

class CategoryAliasMapperFactory implements FactoryInterface
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
			'table' => 'category_alias',
			'primary_key' => 'category_alias_id',
			'columns' => array(
				'category_alias_id'   => 'id',
				'category_id'         => 'category_id',
				'alias_fulnam'        => 'alias'));

		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;
		$namingStrategy = new MapNamingStrategy($dbStructure->columns);
		$hydrator->setNamingStrategy($namingStrategy);

		return new CategoryAliasMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new CategoryAlias, 
			$dbStructure
		);
	}
}
?>

