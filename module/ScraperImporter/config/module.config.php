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
			'scraper_importer' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/admin/scraper_importer/',
					'defaults' => array(
						'__NAMESPACE__' => 'ScraperImporter\Controller',
						'controller'    => 'Index',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes'  => array(
					'dump' => array(
						'type'    => 'Segment',
						'options' => array(
							'route' => ':action[/:file]',
							'constraints' => array(
								'file' => '[a-z_]*\.json'
							),
							'defaults' => array(
								'controller' => 'Index'
							),
						),
					),
				),
			),
		),
	),
	'controllers' => array(
		'factories' => array(
			'ScraperImporter\Controller\Index' => 'ScraperImporter\Factory\IndexControllerFactory',
		),
	),
	'service_manager' => array(
		'factories' => array(
			'ScraperImporter\Service\ImporterServiceInterface' => 'ScraperImporter\Factory\ImporterServiceFactory'
		)
	),
	'view_manager' => array(
		'template_map' => array(
			'scraper-importer/layout'      => __DIR__ . '/../view/scraper-importer/layout.phtml',
			'scraper-importer/index/index' => __DIR__ . '/../view/scraper-importer/index/index.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
);
