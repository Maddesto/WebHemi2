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
 * @package   WebHemi2_Acl_Assert
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Acl\Assert;

use DateTime;
use WebHemi2\Model\Table\Lock as UserLockTable;
use Zend\ServiceManager;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Assertion\AssertionInterface;

/**
 * WebHemi2
 *
 * ACL Assertion
 *
 * @category  WebHemi2
 * @package   WebHemi2_Acl_Assert
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class CleanIPAssertion implements AssertionInterface
{
    /** @var ServiceManager\ServiceLocatorInterface $serviceLocator */
    protected $serviceLocator;

    /**
     * Class constructor
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Return true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Acl $acl
     * @param  RoleInterface $role
     * @param  ResourceInterface $resource
     * @param  string $privilege
     *
     * @return boolean
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        /** @var \Zend\Db\Adapter\Adapter $adapter */
        $adapter = $this->serviceLocator->get('database');
        $lockTable = new UserLockTable($adapter);

        // determine the current timestamp according to the UTC time
        $currentTime = new DateTime(gmdate('Y-m-d H:i:s'));
        $currentTimestamp = $currentTime->getTimestamp();

        // determine the lock timestamp
        $lockTime = $lockTable->getLock()->getTimeLock();
        $lockTimestamp = $lockTime instanceof DateTime ? $lockTime->getTimestamp() : $currentTimestamp;

        // determine the timeout in seconds
        $timeout = UserLockTable::LOCK_TIME * 60;

        // if the lock times out, it should be released
        if ($timeout < $currentTimestamp - $lockTimestamp) {
            $lockTable->releaseLock();
        }

        return $lockTable->getLock()->getTryings() >= UserLockTable::MAX_TRYINGS ? false : true;
    }
}
