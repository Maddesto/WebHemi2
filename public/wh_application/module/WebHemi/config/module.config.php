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
			'forbidden'  => 'WebHemi\ServiceFactory\ForbiddenStrategyServiceFactory',
		),
	),
	'controllers' => array(
		// common invokables
	),
	'view_manager' => array(
		'display_not_found_reason' => true,
		'display_exceptions'       => true,
		'doctype'                  => 'HTML5',
		'not_found_template'       => 'error/404',
		'exception_template'       => 'error/index',
		'strategies' => array(
			'forbidden',
        ),
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
			'allow' => array(
				'view'     => 'guest',
				'comment'  => 'member',
				'moderate' => 'moderator',
				'edit'     => 'editor',
				'publish'  => 'publisher',
				'revoke'   => 'publisher',
				'delete'   => 'publisher',
				'manage'   => 'admin',
			)
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
