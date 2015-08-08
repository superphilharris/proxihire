<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
			'search' => array(
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
								'category' => '[a-z]*',
								'location'     => '[a-z]*',
							),
							'defaults' => array(
								'controller'    => 'Search',
								'action'        => 'index',
							),
						),
					),
				),
			),
		),
	),
	'service_manager' => array(
		'abstract_factories' => array(
			'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
			'Zend\Log\LoggerAbstractServiceFactory',
		),
		'factories' => array(
			'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
			'Application\Service\AssetServiceInterface'    => 'Application\Factory\AssetServiceFactory',
			'Application\Service\CategoryServiceInterface' => 'Application\Factory\CategoryServiceFactory',
			'Application\Mapper\AssetMapperInterface'      => 'Application\Factory\AssetMapperFactory',
			'Application\Mapper\CategoryMapperInterface'   => 'Application\Factory\CategoryMapperFactory',
			'Zend\Db\Adapter\AdapterInterface'             => 'Zend\Db\Adapter\AdapterServiceFactory',
		),
		'invokables' => array(
			'Application\Service\CategoryAliasesServiceInterface'         => 'Application\Service\CategoryAliasesService',
			'Application\Service\UrlServiceInterface'                     => 'Application\Service\UrlService',
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
			'Application\Controller\Search' => 'Application\Factory\SearchControllerFactory'
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
