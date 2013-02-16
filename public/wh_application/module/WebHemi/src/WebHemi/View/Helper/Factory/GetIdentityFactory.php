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
 * @package    WebHemi_View_Helper_Factory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\View\Helper\Factory;

use Zend\ServiceManager\FactoryInterface,
	Zend\ServiceManager\ServiceLocatorInterface,
	WebHemi\View\Helper\GetIdentity;

/**
 * WebHemi getIdentity view helper factory
 *
 * @category   WebHemi
 * @package    WebHemi_View_Helper_Factory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class GetIdentityFactory implements FactoryInterface
{
	/**
	 * Factory method for WebHemi's getIdentity view helper
	 *
	 * @param  ServiceLocatorInterface $serviceLocator
	 * @return GetIdentity
	 */
	public function createService(ServiceLocatorInterface $serviceLocator)
	{
		$helper = new GetIdentity();
		$helper->setAuthService($serviceLocator->getServiceLocator()->get('auth'));
		return $helper;
	}
}
