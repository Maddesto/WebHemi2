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
	'service_manager' => array(
		'invokables' => array(
			'authAdapterDb' => 'WebHemi\Auth\Adapter\Db',
			'authStorageDb' => 'WebHemi\Auth\Storage\Db',
			'authAdapter'   => 'WebHemi\Auth\Adapter\Adapter',
			'formService'   => 'WebHemi\Form\FormService',
		),
		'factories' => array(
			'acl'         => 'WebHemi\ServiceFactory\AclServiceFactory',
			'auth'        => 'WebHemi\ServiceFactory\AuthServiceFactory',
			'translator'  => 'Zend\I18n\Translator\TranslatorServiceFactory',
		),
	),
	'controllers' => array(
		'invokables' => array(
			'WebHemi\Controller\User' => 'WebHemi\Controller\UserController'
		),
	),
	'view_manager' => array(
		'display_not_found_reason' => true,
		'display_exceptions'       => true,
		'doctype'                  => 'HTML5',
		'not_found_template'       => 'error/404',
		'exception_template'       => 'error/index',
	),
	'controller_plugins' => array(
		'invokables' => array(
			'getForm'   => 'WebHemi\Controller\Plugin\GetForm',
			'userAuth'  => 'WebHemi\Controller\Plugin\UserAuth',
			'isAllowed' => 'WebHemi\Controller\Plugin\IsAllowed',
		),
	),
	'view_helpers' => array(
		'invokables' => array(
			'avatar'      => 'WebHemi\View\Helper\Avatar',
		),
		'factories' => array(
			'isAllowed'   => 'WebHemi\View\Helper\Factory\IsAllowedFactory',
			'getIdentity' => 'WebHemi\View\Helper\Factory\GetIdentityFactory',
		),
	),
	'access_control' => array(
		'default_role' => 'guest',
		'template'     => 'error/403',
		'roles'        => array(
			'guest'     => array(
				'parent'   => null,
			),
			'member'    => array(
				'parent'   => 'guest',
			),
			'moderator' => array(
				'parent'   => 'member',
			),
			'editor'    => array(
				'parent'   => 'moderator',
			),
			'publisher' => array(
				'parent'   => 'editor',
			),
			'admin'     => array(
				'parent'   => 'publisher',
			),
		),
		'resources' => array(
			'view',
			'comment',
			'moderate',
			'edit',
			'publish',
			'revoke',
			'delete',
			'manage',
		),
		// only handles 'ALLOWED' rules
		'rules' => array(
			'view'              => 'guest',
			'comment'           => 'member',
			'moderate'          => 'moderator',
			'edit'              => 'editor',
			'publish'           => 'publisher',
			'revoke'            => 'publisher',
			'delete'            => 'publisher',
			'manage'            => 'admin',
		),
	),
	'translator' => array(
		'locale'               => 'en_US',
		'translation_patterns' => array(
			array(
				'type'     => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.mo',
			),
		),
	),
	'router' => array(
		'routes' => array(

		),
	),
);
