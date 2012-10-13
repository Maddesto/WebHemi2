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
 * @package    WebHemi_ServiceFactory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\ServiceFactory;

use Zend\ServiceManager\FactoryInterface,
	Zend\ServiceManager\ServiceLocatorInterface,
	WebHemi\Auth\Adapter\Adapter;

/**
 * WebHemi auth adapter factory
 *
 * @category   WebHemi
 * @package    WebHemi_ServiceFactory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class AuthAdapterServiceFactory implements FactoryInterface
{

	/**
	 * Factory method for WebHemi auth adapter service
	 *
	 * @param  ServiceLocatorInterface $serviceLocator
	 * @return Adapter
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$authAdapter = new Adapter(
						$serviceLocator->get('Zend\Db\Adapter\Adapter'),
						'webhemi_user',
						'username',
						'password',
						'MD5(?)'
		);

		return $authAdapter;
	}

}
