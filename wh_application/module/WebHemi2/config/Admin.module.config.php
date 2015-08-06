<?php

/**
 * WebHemi2
 *
 * PHP version 5.4
 *
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
 * @category  WebHemi2
 * @package   WebHemi2
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
return [
    'controllers' => [
        'invokables' => [
            'WebHemi2\Controller\Admin' => 'WebHemi2\Controller\AdminController',
            'WebHemi2\Controller\ControlPanel' => 'WebHemi2\Controller\ControlPanelController',
            'WebHemi2\Controller\UserManagement' => 'WebHemi2\Controller\UserManagementController',
        ],
    ],
    'module_layouts' => [
        'WebHemi2' => 'layout/admin',
    ],
    'view_manager' => [
        'template_map' => [
            'layout/layout' => __DIR__ . '/../resources/default/view/layout/admin.phtml',
            'layout/login'  => __DIR__ . '/../resources/default/view/layout/login.phtml',
            'block/header'  => __DIR__ . '/../resources/default/view/block/admin/header.phtml',
            'block/footer'  => __DIR__ . '/../resources/default/view/block/admin/footer.phtml',
            'block/menu'    => __DIR__ . '/../resources/default/view/block/admin/menu.phtml',
        ],
    ],
    'router' => [
        'routes' => [
            // Admin application
            'index' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[:mod]',
                    'constraints' => [
                        'mod' => '(?:' . APPLICATION_MODULE_URI . '/|)'
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'WebHemi2\Controller',
                        'controller' => 'Admin',
                        'action' => 'index',
                        'mod' => '',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => 'login/',
                            'defaults' => [
                                'controller' => 'Admin',
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => 'logout/',
                            'defaults' => [
                                'controller' => 'Admin',
                                'action' => 'logout',
                            ],
                        ],
                    ],
                    'application' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => 'application/',
                            'defaults' => [
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Admin',
                                'action' => 'application',
                            ],
                        ],
                    ],
                    'control-panel' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => 'control-panel/',
                            'defaults' => [
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Admin',
                                'action' => 'control-panel',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'user' => [
                                'type' => 'literal',
                                'priority' => 1000,
                                'options' => [
                                    'route' => 'user-management/',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'WebHemi2\Controller',
                                        'controller' => 'UserManagement',
                                        'action' => 'userList',
                                    ],
                                ],
                                'may_terminate' => true,
                                'child_routes' => [
                                    'profile' => [
                                        'type' => 'literal',
                                        'options' => [
                                            'route' => 'profile/',
                                            'defaults' => [
                                                'controller' => 'UserManagement',
                                                'action' => 'userProfile',
                                            ],
                                        ],
                                    ],
                                    'add' => [
                                        'type' => 'literal',
                                        'options' => [
                                            'route' => 'add/',
                                            'defaults' => [
                                                'controller' => 'UserManagement',
                                                'action' => 'userAdd',
                                            ],
                                        ],
                                    ],
                                    'edit' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => 'edit/[:userName]/',
                                            'constraints' => [
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'UserManagement',
                                                'action' => 'userEdit',
                                            ],
                                        ],
                                    ],
                                    'view' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => 'view/[:userName]/',
                                            'constraints' => [
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'UserManagement',
                                                'action' => 'userView',
                                            ],
                                        ],
                                    ],
                                    'disable' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => 'disable/[:userName]/',
                                            'constraints' => [
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'UserManagement',
                                                'action' => 'userDisable',
                                            ],
                                        ],
                                    ],
                                    'enable' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => 'enable/[:userName]/',
                                            'constraints' => [
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'UserManagement',
                                                'action' => 'userEnable',
                                            ],
                                        ],
                                    ],
                                    'activate' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => 'activate/[:userName]/',
                                            'constraints' => [
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'UserManagement',
                                                'action' => 'userActivate',
                                            ],
                                        ],
                                    ],
                                    'delete' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => 'delete/[:userName]/',
                                            'constraints' => [
                                                'userName' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                            ],
                                            'defaults' => [
                                                'controller' => 'UserManagement',
                                                'action' => 'userDelete',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'about' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => 'about/',
                            'defaults' => [
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Admin',
                                'action' => 'about',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
