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
            'WebHemi2\Controller\Website' => 'WebHemi2\Controller\WebsiteController'
        ],
    ],
    'module_layouts' => [
        'WebHemi2' => 'layout/default',
    ],
    'view_manager' => [
        'template_map' => [
            'block/header'  => __DIR__ . '/../resources/default/view/block/website/header.phtml',
            'block/footer'  => __DIR__ . '/../resources/default/view/block/website/footer.phtml',
            'block/menu'    => __DIR__ . '/../resources/default/view/block/website/menu.phtml',
        ],
    ],
    'router' => [
        'routes' => [
            // website application
            'index' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        '__NAMESPACE__' => 'WebHemi2\Controller',
                        'controller' => 'Website',
                        'action' => 'index',
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'view' => [
                        'type' => 'Regex',
                        'options' => [
                            'regex' => '(?:(?<category>[a-zA-Z0-9_-]+))?/(?<id>[\/a-zA-Z0-9_-]+)'
                                . '(\.(?<format>(json|html|rss)))?',
                            'defaults' => [
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'Website',
                                'action' => 'view',
                                'category' => 'default',
                                'format' => 'html',
                            ],
                            'spec' => '/%category%/%id%.%format%',
                        ],
                    ],
                    'user' => [
                        'type' => 'Literal',
                        'priority' => 1000,
                        'options' => [
                            'route' => 'user/',
                            'defaults' => [
                                '__NAMESPACE__' => 'WebHemi2\Controller',
                                'controller' => 'User',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'profile' => [
                                'type' => 'Literal',
                                'priority' => 1000,
                                'options' => [
                                    'route' => 'my-profile/',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'WebHemi2\Controller',
                                        'controller' => 'User',
                                        'action' => 'userProfile',
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
                                        'controller' => 'User',
                                        'action' => 'userView',
                                    ],
                                ],
                            ],
                            'edit' => [
                                'type' => 'Literal',
                                'priority' => 1000,
                                'options' => [
                                    'route' => 'edit/',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'WebHemi2\Controller',
                                        'controller' => 'User',
                                        'action' => 'userEdit',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'login' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => 'login/',
                            'defaults' => [
                                'controller' => 'User',
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => 'logout/',
                            'defaults' => [
                                'controller' => 'User',
                                'action' => 'logout',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
