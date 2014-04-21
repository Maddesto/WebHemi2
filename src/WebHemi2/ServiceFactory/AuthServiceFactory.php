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
 * @package    WebHemi2_ServiceFactory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\ServiceFactory;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    WebHemi2\Auth\Auth;

/**
 * WebHemi2 authentication service factory
 *
 * @category   WebHemi2
 * @package    WebHemi2_ServiceFactory
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class AuthServiceFactory implements FactoryInterface
{
    /**
     * Factory method for WebHemi2 authentication service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Auth
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $storage = $serviceLocator->get('authStorageDb');
        $adapter = $serviceLocator->get('authAdapter');

        return new Auth($storage, $adapter);
    }
}
