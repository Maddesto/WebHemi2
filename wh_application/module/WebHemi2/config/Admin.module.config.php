<?php

/**
 * WebHemi2
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
 * @category   WebHemi2
 * @package    WebHemi2
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            // Admin application
            'index' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:mod]',
                    'constraints' => array(
                        'mod' => '(?:' . APPLICATION_MODULE_URI . '/?|)'
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'WebHemi2\Controller',
                        'controller' => 'Admin',
                        'action' => 'index',
                        'mod' => '',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'user' => array(
                        'type' => 'literal',
                        'priority' => 1000,
                        'options' => array(
                            'route' => 'user',
                            'defaults' => array(
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Admin',
                                'action' => 'userList',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'login' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/login',
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'login',
                                    ),
                                ),
                            ),
                            'logout' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/logout',
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'logout',
                                    ),
                                ),
                            ),
                            'profile' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/profile',
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'userProfile',
                                    ),
                                ),
                            ),
                            'new' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/new',
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'userAdd',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/edit/[:userName]',
                                    'constraints' => array(
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'userEdit',
                                    ),
                                ),
                            ),
                            'view' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/view/[:userName]',
                                    'constraints' => array(
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'userView',
                                    ),
                                ),
                            ),
                            'disable' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/disable/[:userName]',
                                    'constraints' => array(
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'userDisable',
                                    ),
                                ),
                            ),
                            'enable' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/enable/[:userName]',
                                    'constraints' => array(
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'userEnable',
                                    ),
                                ),
                            ),
                            'activate' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/activate/[:userName]',
                                    'constraints' => array(
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'userActivate',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/delete/[:userName]',
                                    'constraints' => array(
                                        'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(
                                        'controller' => 'Admin',
                                        'action' => 'userDelete',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'about' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'about',
                            'defaults' => array(
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'About',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'application' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'application',
                            'defaults' => array(
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Application',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'component' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'component',
                            'defaults' => array(
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Component',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'WebHemi2\Controller\Admin' => 'WebHemi2\Controller\AdminController',
            'WebHemi2\Controller\Application' => 'WebHemi2\Controller\ApplicationController',
            'WebHemi2\Controller\About' => 'WebHemi2\Controller\AboutController',
            'WebHemi2\Controller\Component' => 'WebHemi2\Controller\ComponentController',
            'WebHemi2\Controller\User' => 'WebHemi2\Controller\UserController',
        ),
    ),
    'module_layouts' => array(
        'WebHemi2' => 'layout/admin',
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'admin' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/admin.phtml',
        ),
    ),
);
