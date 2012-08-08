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
			'admin' => array(
				'type'    => 'Segment',
				'options' => array(
					'route'       => '/[:controller[/:action]]',
					'defaults'    => array(
						'__NAMESPACE__' => 'WebHemi\Controller\Admin',
						'controller'    => 'Index',
						'action'        => 'index',
					),
					'constraints' => array(
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
					),
				),
				'may_terminate' => true,
			),
		),
	),
	'controllers' => array(
		'invokables' => array(
			'WebHemi\Controller\Admin\Index' => 'WebHemi\Controller\Admin\IndexController',
		),
	),
	'view_manager' => array(
		'template_path_stack'      => array(
			'admin' => __DIR__ . '/../view/admin',
		),
	),
);
