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
 * @package   WebHemi2_ServiceFactory
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\ServiceFactory;

use WebHemi2\Auth\Auth;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Authentication\Adapter\AdapterInterface;

/**
 * WebHemi2
 *
 * authentication service factory
 *
 * @category  WebHemi2
 * @package   WebHemi2_ServiceFactory
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
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
        /** @var StorageInterface $storage */
        $storage = $serviceLocator->get('authStorageSession');
        /** @var AdapterInterface $adapter */
        $adapter = $serviceLocator->get('authAdapter');

        return new Auth($storage, $adapter);
    }
}
