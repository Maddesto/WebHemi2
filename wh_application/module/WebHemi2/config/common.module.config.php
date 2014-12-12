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
    // Configuration keys: \Zend\Mvc\Service\ModuleManagerFactory
    'service_manager' => array(
        'invokables' => array(
            'authAdapterDb' => 'WebHemi2\Auth\Adapter\Db',
            'authStorageDb' => 'WebHemi2\Auth\Storage\Db',
            'authAdapter' => 'WebHemi2\Auth\Adapter\Adapter',
            'formService' => 'WebHemi2\Form\FormService',
        ),
        'factories' => array(
            'acl' => 'WebHemi2\ServiceFactory\AclServiceFactory',
            'auth' => 'WebHemi2\ServiceFactory\AuthServiceFactory',
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'WebHemi2\Controller\User' => 'WebHemi2\Controller\UserController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/500',
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'getForm' => 'WebHemi2\Controller\Plugin\GetForm',
            'userAuth' => 'WebHemi2\Controller\Plugin\UserAuth',
            'isAllowed' => 'WebHemi2\Controller\Plugin\IsAllowed',
            'redirect ' => 'WebHemi2\Controller\Plugin\Redirect',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'avatar' => 'WebHemi2\View\Helper\Avatar',
            'url' => 'WebHemi2\View\Helper\Url',
            'headScript' => 'WebHemi2\View\Helper\HeadScript',
            'headLink' => 'WebHemi2\View\Helper\HeadLink',
            'formElement' => 'WebHemi2\Form\View\Helper\FormElement',
            'formPlainText' => 'WebHemi2\Form\View\Helper\FormPlainText',
            'formLocation' => 'WebHemi2\Form\View\Helper\FormLocation',
        ),
        'factories' => array(
            'isAllowed' => 'WebHemi2\View\Helper\Factory\IsAllowedFactory',
            'getIdentity' => 'WebHemi2\View\Helper\Factory\GetIdentityFactory',
        ),
    ),
    'access_control' => array(
        'template' => 'error/403',
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'router' => array(
        'routes' => array(),
    ),
);
