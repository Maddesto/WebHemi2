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
	'service_manager' => array(
		'factories' => array(
			'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
			'acl'        => 'WebHemi\ServiceFactory\AclServiceFactory',
		),
	),
	'controllers' => array(
		// common invokables
	),
	'controller_plugins' => array(
		'factories' => array(
			'isAllowed' => 'WebHemi\Controller\Plugin\Factory\IsAllowedFactory',
		),
	),
	'view_helpers' => array(
		'factories' => array(
			'isAllowed' => 'WebHemi\View\Helper\Factory\IsAllowedFactory',
		),
	),
	'access_control' => array(
        'default_role' => 'guest',
		'template'     => 'error/403',
//        'identity_provider'  => 'WebHemi\Provider\Identity\ZfcUserZendDb',
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
        'resources'    => array(
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
        'rules'        => array(
			array(
				'role'       => 'guest',
				'resources'  => 'view',
				'privileges' => null,
				'assertion'  => null,
			),
			array(
				'role'       => 'member',
				'resources'  => 'comment',
				'privileges' => null,
				'assertion'  => null,
			),
			array(
				'role'       => 'moderator',
				'resources'  => 'moderate',
				'privileges' => null,
				'assertion'  => null,
			),
			array(
				'role'       => 'editor',
				'resources'  => 'edit',
				'privileges' => null,
				'assertion'  => null,
			),
			array(
				'role'       => 'publisher',
				'resources'  => array('publish', 'revoke', 'delete'),
				'privileges' => null,
				'assertion'  => null,
			),
			array(
				'role'       => 'admin',
				'resources'  => 'manage',
				'privileges' => null,
				'assertion'  => null,
			),
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
);
