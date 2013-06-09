<?php

/**
 * WebHemi
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webhemi.gixx-web.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@gixx-web.com so we can send you a copy immediately.
 *
 * @category   WebHemi
 * @package    WebHemi
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
return array(
	'wh_themes' => array(
		'current_theme' => 'default',
		'theme_paths'   => array(
			WEB_ROOT . '/wh_themes/' . APPLICATION_MODULE . '/'
		),
		'adapters'      => array(
			'WebHemi\Theme\Adapter\ConfigurationAdapter',
		),
	),
	'router' => array(
		'routes' => array(
			// website application
			'index' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/',
					'defaults' => array(
						'__NAMESPACE__' => 'WebHemi\Controller',
						'controller'    => 'Website',
						'action'        => 'index',
					)
				),
				'may_terminate' => true,
			),
			'view' => array(
				'type'    => 'Regex',
				'options' => array(
					'regex'    => '(?:/(?<category>[a-zA-Z0-9_-]+))?/(?<id>[\/a-zA-Z0-9_-]+)(\.(?<format>(json|html|rss)))?',
					'defaults' => array(
						'__NAMESPACE__' => 'WebHemi\Controller',
						'controller'    => 'Website',
						'action'        => 'view',
						'category'      => 'default',
						'format'        => 'html',
					),
					'spec' => '/%category%/%id%.%format%',
				),
				'may_terminate' => true,
			),
			'user' => array(
				'type'     => 'Literal',
				'priority' => 1000,
				'options'  => array(
					'route' => '/user',
					'defaults' => array(
						'__NAMESPACE__' => 'WebHemi\Controller',
						'controller' => 'User',
						'action'     => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes'  => array(
					'login' => array(
						'type'    => 'Literal',
						'options' => array(
							'route'    => '/login',
							'defaults' => array(
								'controller' => 'User',
								'action'     => 'login',
							),
						),
					),
					'logout' => array(
						'type'    => 'Literal',
						'options' => array(
							'route'    => '/logout',
							'defaults' => array(
								'controller' => 'User',
								'action'     => 'logout',
							),
						),
					),
				),
			),
		),
	),
	'service_manager' => array(
		'factories' => array(
			'theme_manager' => 'WebHemi\ServiceFactory\ThemeManagerServiceFactory'
		),
	),
	'controllers' => array(
		'invokables' => array(
			'WebHemi\Controller\Website' => 'WebHemi\Controller\WebsiteController'
		),
	),
	'module_layouts' => array(
		'WebHemi' => 'layout/default',
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'website' => __DIR__ . '/../view',
		),
		'template_map' => array(
			'layout/layout' => __DIR__ . '/../view/layout/default.phtml',
		),
	),
	'access_control' => array(
		'resources' => array(
			'Controller-Website/*',
			'Controller-User/*',
		),
		'rules' => array(
			'Controller-Website/*' => 'guest',
			'Controller-User/*'    => 'guest',
		),
	),
);