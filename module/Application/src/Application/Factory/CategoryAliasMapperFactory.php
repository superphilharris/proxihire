<?php
namespace Application\Factory;

use Application\Mapper\CategoryAliasMapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
// jih: this should really be in its own factory. However, it will be 
//      specific to this factory, so one will need to be created for all Mapper 
//      factories
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
		$hydrator=new \Zend\Stdlib\Hydrator\ArraySerializable;

		$dbStructure=(object) array(
			'table' => 'category_alias',
			'primary_key' => 'category_alias_id',
			'columns' => array(
				'category_alias_id'   => 'id',
				'category_id'         => 'category_id',
				'alias_fulnam'        => 'alias'));

		$namingStrategy = new MapNamingStrategy(array());

		return new CategoryAliasMapper(
			$serviceLocator->get('Zend\Db\Adapter\AdapterInterface'),
			$hydrator,
			new \Application\Model\CategoryAlias, // jih: move this (and in all mappers) to begining
			$dbStructure,
			$namingStrategy
		);
	}
}
?>

