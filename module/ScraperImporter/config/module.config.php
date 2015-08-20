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
					'route'    => '/admin/scraper_importer',
					'defaults' => array(
						'__NAMESPACE__' => 'ScraperImporter\Controller',
						'controller'    => 'Index',
						'action'        => 'index',
					),
				),
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'ScraperImporter\Controller\Index' => 'ScraperImporter\Controller\IndexController',
		),
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
