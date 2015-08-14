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
    // Configuration keys: \Zend\Mvc\Service\ModuleManagerFactory
    'service_manager' => [
        'invokables' => [
            'authAdapterDb' => 'WebHemi2\Auth\Adapter\Db',
            'authStorageDb' => 'WebHemi2\Auth\Storage\Db',
            'authStorageSession' => 'WebHemi2\Auth\Storage\Session',
            'authAdapter' => 'WebHemi2\Auth\Adapter\Adapter',
            'formService' => 'WebHemi2\Form\FormService',
        ],
        'factories' => [
            'acl' => 'WebHemi2\ServiceFactory\AclServiceFactory',
            'auth' => 'WebHemi2\ServiceFactory\AuthServiceFactory',
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ],
    ],
    'controllers' => [
        'invokables' => [
            'WebHemi2\Controller\User' => 'WebHemi2\Controller\UserController'
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'getForm' => 'WebHemi2\Controller\Plugin\GetForm',
            'userAuth' => 'WebHemi2\Controller\Plugin\UserAuth',
            'isAllowed' => 'WebHemi2\Controller\Plugin\IsAllowed',
            'redirect ' => 'WebHemi2\Controller\Plugin\Redirect',
        ],
    ],
    'view_manager' => [
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/500',
        'display_exceptions'       => true,
        'display_not_found_reason' => true,
        'theme_settings' => [
            'title'       => 'WebHemi2',
            'description' => 'WebHemi2 default theme. Built upon the Google\'s "Material Design Light" library.',
            'version'     => '0.1',
            'author'      => 'Gixx',
            'link'        => 'http://www.gixx-web.com',
            'license'     => 'BSD-3-Clause',
            'mdl_enabled' => true,
            'mdl_primary' => 'blue_grey',
            'mdl_accent'  => 'red'
        ],
        'template_path_stack'      => [
            'website' => __DIR__ . '/../resources/default/view',
        ],
        'template_map' => [
            'layout/layout' => __DIR__ . '/../resources/default/view/layout/default.phtml',
            'error/500'     => __DIR__ . '/../resources/default/view/error/500.phtml',
            'error/403'     => __DIR__ . '/../resources/default/view/error/403.phtml',
            'error/404'     => __DIR__ . '/../resources/default/view/error/404.phtml',
            'block/header'  => __DIR__ . '/../resources/default/view/block/website/header.phtml',
            'block/footer'  => __DIR__ . '/../resources/default/view/block/website/footer.phtml',
            'block/menu'    => __DIR__ . '/../resources/default/view/block/website/menu.phtml',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'avatar' => 'WebHemi2\View\Helper\Avatar',
            'url' => 'WebHemi2\View\Helper\Url',
            'headScript' => 'WebHemi2\View\Helper\HeadScript',
            'headLink' => 'WebHemi2\View\Helper\HeadLink',
            'formElement' => 'WebHemi2\Form\View\Helper\FormElement',
            'formPlainText' => 'WebHemi2\Form\View\Helper\FormPlainText',
            'formLocation' => 'WebHemi2\Form\View\Helper\FormLocation',
            'formToggle' => 'WebHemi2\Form\View\Helper\FormToggle',
            'formFabButton' => 'WebHemi2\Form\View\Helper\FormFabButton',
        ],
        'factories' => [
            'isAllowed' => 'WebHemi2\View\Helper\Factory\IsAllowedFactory',
            'getIdentity' => 'WebHemi2\View\Helper\Factory\GetIdentityFactory',
        ],
    ],
    'view_themes' => [
        'current_theme' => 'default',
        'theme_paths' => [
            APPLICATION_MODULE_PATH . '/resources/themes/'
        ],
    ],
    'access_control' => [
        'template' => 'error/403',
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'router' => [
        'routes' => [],
    ],
];
