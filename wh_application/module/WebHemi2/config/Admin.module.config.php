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
 * @copyright  Copyright (c) 2015, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
return array(
    'controllers' => array(
        'invokables' => array(
            'WebHemi2\Controller\Admin' => 'WebHemi2\Controller\AdminController',
            'WebHemi2\Controller\ControlPanel' => 'WebHemi2\Controller\ControlPanelController',
            'WebHemi2\Controller\UserManagement' => 'WebHemi2\Controller\UserManagementController',
        ),
    ),
    'module_layouts' => array(
        'WebHemi2' => 'layout/admin',
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'admin' => __DIR__ . '/../resources/default/view',
        ),
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../resources/default/view/layout/admin.phtml',
        ),
    ),
    'router' => array(
        'routes' => array(
            // Admin application
            'index' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:mod]',
                    'constraints' => array(
                        'mod' => '(?:' . APPLICATION_MODULE_URI . '/|)'
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
                    'login' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'login/',
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action' => 'login',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'logout/',
                            'defaults' => array(
                                'controller' => 'Admin',
                                'action' => 'logout',
                            ),
                        ),
                    ),
                    'application' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'application/',
                            'defaults' => array(
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Admin',
                                'action' => 'application',
                            ),
                        ),
                    ),
                    'control-panel' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'control-panel/',
                            'defaults' => array(
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Admin',
                                'action' => 'control-panel',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'user' => array(
                                'type' => 'literal',
                                'priority' => 1000,
                                'options' => array(
                                    'route' => 'user-management/',
                                    'defaults' => array(
                                        '__NAMESPACE__' => 'WebHemi2\Controller',
                                        'controller' => 'UserManagement',
                                        'action' => 'userList',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'profile' => array(
                                        'type' => 'literal',
                                        'options' => array(
                                            'route' => 'profile/',
                                            'defaults' => array(
                                                'controller' => 'UserManagement',
                                                'action' => 'userProfile',
                                            ),
                                        ),
                                    ),
                                    'add' => array(
                                        'type' => 'literal',
                                        'options' => array(
                                            'route' => 'add/',
                                            'defaults' => array(
                                                'controller' => 'UserManagement',
                                                'action' => 'userAdd',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => 'edit/[:userName]/',
                                            'constraints' => array(
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'UserManagement',
                                                'action' => 'userEdit',
                                            ),
                                        ),
                                    ),
                                    'view' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => 'view/[:userName]/',
                                            'constraints' => array(
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'UserManagement',
                                                'action' => 'userView',
                                            ),
                                        ),
                                    ),
                                    'disable' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => 'disable/[:userName]/',
                                            'constraints' => array(
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'UserManagement',
                                                'action' => 'userDisable',
                                            ),
                                        ),
                                    ),
                                    'enable' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => 'enable/[:userName]/',
                                            'constraints' => array(
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'UserManagement',
                                                'action' => 'userEnable',
                                            ),
                                        ),
                                    ),
                                    'activate' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => 'activate/[:userName]/',
                                            'constraints' => array(
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'UserManagement',
                                                'action' => 'userActivate',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'Segment',
                                        'options' => array(
                                            'route' => 'delete/[:userName]/',
                                            'constraints' => array(
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ),
                                            'defaults' => array(
                                                'controller' => 'UserManagement',
                                                'action' => 'userDelete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'about' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => 'about/',
                            'defaults' => array(
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Admin',
                                'action' => 'about',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
