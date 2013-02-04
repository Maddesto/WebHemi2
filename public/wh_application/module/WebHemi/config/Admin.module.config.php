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
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
return array(
	'router' => array(
		'routes' => array(
			// Admin application
			'index' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'       => '/',
					'defaults'    => array(
						'__NAMESPACE__' => 'WebHemi\Controller',
						'controller'    => 'Admin',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				 'child_routes'  => array(
                    'about' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => 'about',
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action'     => 'about',
                            ),
                        ),
                    ),
				),
			),
			'user' => array(
                'type'     => 'Literal',
                'priority' => 1000,
                'options'  => array(
                    'route' => '/user',
                    'defaults' => array(
						'__NAMESPACE__' => 'WebHemi\Controller',
                        'controller' => 'Admin',
                        'action'     => 'user',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'profile' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/profile',
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action'     => 'profile',
                            ),
                        ),
                    ),
					'login' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/login',
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action'     => 'login',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/logout',
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                ),
            ),
			'application' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'       => '/application',
					'defaults'    => array(
						'__NAMESPACE__' => 'WebHemi\Controller',
						'controller'    => 'Application',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
			),
			'component' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'       => '/component',
					'defaults'    => array(
						'__NAMESPACE__' => 'WebHemi\Controller',
						'controller'    => 'Component',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'WebHemi\Controller\Admin'       => 'WebHemi\Controller\AdminController',
			'WebHemi\Controller\Application' => 'WebHemi\Controller\ApplicationController',
			'WebHemi\Controller\Component'   => 'WebHemi\Controller\ComponentController',
			'WebHemi\Controller\User'        => 'WebHemi\Controller\UserController',
		),
	),
	'module_layouts' => array(
        'WebHemi' => 'layout/admin',
    ),
	'view_manager' => array(
		'template_path_stack'      => array(
			'admin' => __DIR__ . '/../view',
		),
		'template_map'               => array(
			'layout/layout'          => __DIR__ . '/../view/layout/admin.phtml',
		),
	),
	'access_control' => array(
		'resources' => array(
			'Controller-Admin/*',
			'!Controller-Admin/login',
			'Controller-Application/*',
			'Controller-Component/*',
		),
		'rules' => array(
			'Controller-Admin/*'       => 'member',
			'!Controller-Admin/login'  => 'guest',
			'Controller-Application/*' => 'moderator',
			'Controller-Component/*'   => 'admin',
		),
	),
);
