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
 * @package    WebHemi2_Auth_Storage
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Auth\Storage;

use Zend\Authentication\Storage\Session,
    Zend\Authentication\Storage\StorageInterface,
    Zend\ServiceManager\ServiceManagerAwareInterface,
    Zend\ServiceManager\ServiceManager,
    WebHemi2\Model\Table\User as UserTable;

/**
 * WebHemi2 Authentication Database Storage
 *
 * @category   WebHemi2
 * @package    WebHemi2_Auth_Storage
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Db implements StorageInterface, ServiceManagerAwareInterface
{
    /** @var StorageInterface $storage */
    protected $storage;
    /** @var UserTable $userTable */
    protected $userTable;
    /** @var mixed $resolvedIdentity */
    protected $resolvedIdentity;
    /** @var ServiceManager $serviceManager */
    protected $serviceManager;

    /**
     * Check whether the storage is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->getStorage()->isEmpty();
    }

    /**
     * Retrieve the contents of storage
     *
     * @return mixed
     */
    public function read()
    {
        if (null !== $this->resolvedIdentity) {
            return $this->resolvedIdentity;
        }

        $identity = $this->getStorage()->read();

        if (is_int($identity) || is_scalar($identity)) {
            $identity = $this->getTable()->getUserById($identity);
        }

        if ($identity) {
            $this->resolvedIdentity = $identity;
        } else {
            $this->resolvedIdentity = null;
        }

        return $this->resolvedIdentity;
    }

    /**
     * Write contents to storage
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->write($contents);
    }

    /**
     * Clear contents from storage
     *
     * @return void
     */
    public function clear()
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->clear();
    }

    /**
     * Retrieve storage
     *
     * @return StorageInterface
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Session());
        }
        return $this->storage;
    }

    /**
     * Set storage
     *
     * @param StorageInterface $storage
     * @return Db
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Retrieve User Table instance
     *
     * @return UserTable
     */
    public function getTable()
    {
        if (!isset($this->userTable)) {
            $this->userTable = new UserTable($this->getServiceManager()->get('database'));
        }
        return $this->userTable;
    }

    /**
     * Set User Table instance
     *
     * @param UserTable $userTable
     * @return Db
     */
    public function setTable(UserTable $userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}
