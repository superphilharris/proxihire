<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

$searchConfig=array(
	'type'    => 'Literal',
	'options' => array(
		'route'    => '/search',
		'defaults' => array(
			'__NAMESPACE__' => 'Application\Controller',
			'controller'    => 'Index',
			'action'        => 'index',
		),
	),
	'may_terminate' => true,
	'child_routes' => array(
		'default' => array(
			'type'    => 'Segment',
			'options' => array(
				'route'    => '/:category[/:location]',
				'constraints' => array(
					'category' => '([a-z][a-z]*(%20)*)*',
					'location' => '([a-z][a-z]*(%20)*)*',
				),
				'defaults' => array(
					'controller'    => 'Search',
					'action'        => 'index',
				),
			),
		),
	)
);

$assetListConfig=$searchConfig;
$assetListConfig['options']['route']='/assetlist';
$assetListConfig['child_routes']['default']['options']['defaults']['controller']='Ajax';
$assetListConfig['child_routes']['default']['options']['defaults']['action']='assetList';

return array(
	'router' => array(
		'routes' => array(
			'home' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/',
					'defaults' => array(
						'controller' => 'Application\Controller\Index',
						'action'     => 'index',
					),
				),
			),
			'assetlist' => $assetListConfig,
			'search'    => $searchConfig
		),
	),
	'service_manager' => array(
		'abstract_factories' => array(
			'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
			'Zend\Log\LoggerAbstractServiceFactory',
		),
		'factories' => array(
			'translator'                                      => 'Zend\Mvc\Service\TranslatorServiceFactory',
			'Zend\Db\Adapter\AdapterInterface'                => 'Zend\Db\Adapter\AdapterServiceFactory',
			'Application\Service\AssetServiceInterface'       => 'Application\Factory\AssetServiceFactory',
			'Application\Service\CategoryServiceInterface'    => 'Application\Factory\CategoryServiceFactory',
			'Application\Mapper\AssetMapperInterface'         => 'Application\Factory\AssetMapperFactory',
			'Application\Mapper\AssetPropertyMapperInterface' => 'Application\Factory\AssetPropertyMapperFactory',
			'Application\Mapper\AssetRateMapperInterface'     => 'Application\Factory\AssetRateMapperFactory',
			'Application\Mapper\CategoryAliasMapperInterface' => 'Application\Factory\CategoryAliasMapperFactory',
			'Application\Mapper\CategoryMapperInterface'      => 'Application\Factory\CategoryMapperFactory',
			'Application\Mapper\DatatypeMapperInterface'      => 'Application\Factory\DatatypeMapperFactory',
			'Application\Mapper\LessorMapperInterface'        => 'Application\Factory\LessorMapperFactory',
			'Application\Mapper\LocationMapperInterface'      => 'Application\Factory\LocationMapperFactory',
			'Application\Mapper\UrlMapperInterface'           => 'Application\Factory\UrlMapperFactory',
			'Application\Mapper\UserMapperInterface'          => 'Application\Factory\UserMapperFactory',
		),
		'invokables' => array(
			'Application\Service\CategoryAliasesServiceInterface' => 'Application\Service\CategoryAliasesService',
			'Application\Service\UrlServiceInterface'             => 'Application\Service\UrlService',
			'Application\Model\AssetInterface'                    => 'Application\Model\Asset',
			'Application\Model\AssetPropertyInterface'            => 'Application\Model\AssetProperty',
			'Application\Model\AssetRateInterface'                => 'Application\Model\AssetRate',
			'Application\Model\CategoryAliasInterface'            => 'Application\Model\CategoryAlias',
			'Application\Model\CategoryInterface'                 => 'Application\Model\Category',
			'Application\Model\DatatypeInterface'                 => 'Application\Model\Datatype',
			'Application\Model\LessorInterface'                   => 'Application\Model\Lessor',
			'Application\Model\LocationInterface'                 => 'Application\Model\Location',
			'Application\Model\UrlInterface'                      => 'Application\Model\Url',
			'Application\Model\UserInterface'                     => 'Application\Model\User',
		),
		'shared' => array(
			'translator'                                          => true,
			'Zend\Db\Adapter\AdapterInterface'                    => true,
			'Application\Service\AssetServiceInterface'           => false,
			'Application\Service\CategoryServiceInterface'        => false,
			'Application\Mapper\AssetMapperInterface'             => false,
			'Application\Mapper\AssetPropertyMapperInterface'     => false,
			'Application\Mapper\AssetRateMapperInterface'         => false,
			'Application\Mapper\CategoryAliasMapperInterface'     => false,
			'Application\Mapper\CategoryMapperInterface'          => false,
			'Application\Mapper\DatatypeMapperInterface'          => false,
			'Application\Mapper\LessorMapperInterface'            => false,
			'Application\Mapper\LocationMapperInterface'          => false,
			'Application\Mapper\UrlMapperInterface'               => false,
			'Application\Mapper\UserMapperInterface'              => false,
			'Application\Service\CategoryAliasesServiceInterface' => false,
			'Application\Service\UrlServiceInterface'             => false,
			'Application\Model\AssetInterface'                    => false,
			'Application\Model\AssetPropertyInterface'            => false,
			'Application\Model\AssetRateInterface'                => false,
			'Application\Model\CategoryAliasInterface'            => false,
			'Application\Model\CategoryInterface'                 => false,
			'Application\Model\DatatypeInterface'                 => false,
			'Application\Model\LessorInterface'                   => false,
			'Application\Model\LocationInterface'                 => false,
			'Application\Model\UrlInterface'                      => false,
			'Application\Model\UserInterface'                     => false,
		),
	),
	'translator' => array(
		'locale' => 'en_US',
		'translation_file_patterns' => array(
			array(
				'type'     => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.mo',
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'Application\Controller\Index' => 'Application\Controller\IndexController',
		),
		'factories' => array(
			'Application\Controller\Search' => 'Application\Factory\SearchControllerFactory',
			'Application\Controller\Ajax' => 'Application\Factory\AjaxControllerFactory'
		),
	),
	'view_manager' => array(
		'display_not_found_reason' => true,
		'display_exceptions'       => true,
		'doctype'                  => 'HTML5',
		'not_found_template'       => 'error/404',
		'exception_template'       => 'error/index',
		'template_map' => array(
			'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
			'application/ajax'        => __DIR__ . '/../view/layout/ajax.phtml',
			'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
			'error/404'               => __DIR__ . '/../view/error/404.phtml',
			'error/index'             => __DIR__ . '/../view/error/index.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
	// Placeholder for console routes
	'console' => array(
		'router' => array(
			'routes' => array(
			),
		),
	),
);
