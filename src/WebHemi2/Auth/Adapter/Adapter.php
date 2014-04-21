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
 * @package    WebHemi2_Auth_Adapter
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Auth\Adapter;

use Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Result,
    Zend\ServiceManager\ServiceManagerAwareInterface,
    Zend\ServiceManager\ServiceManager,
    Zend\Crypt\Password\Bcrypt,
    WebHemi2\Model\User as UserModel,
    WebHemi2\Model\Table\User as UserTable,
    WebHemi2\Model\Table\Lock as UserLockTable;

/**
 * WebHemi2 User Authentication Adapter
 *
 * @category   WebHemi2
 * @package    WebHemi2_Auth_Adapter
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Adapter implements AdapterInterface, ServiceManagerAwareInterface
{
    /** @var Default bcrypt password cost */
    const PASSWORD_COST = 14;

    /** @var string $identity */
    public $identity = null;
    /** @var string $credential */
    protected $credential = null;
    /** @var UserTable $userTable */
    protected $userTable;
    /** @var UserLockTable $userLockTable */
    protected $userLockTable;
    /** @var boolean $verifiedUser */
    protected $verifiedUser = null;

    /**
     * This method is called to attempt an authentication.
     *
     * @return Result
     */
    public function authenticate()
    {
        // the autologin may set a user so no further checking will be needed
        if (!$this->verifiedUser) {
            // identified by email
            if (strpos($this->identity, '@') !== false) {
                $userModel = $this->getUserTable()->getUserByEmail($this->identity);
            }
            // identified by username
            else {
                $userModel = $this->getUserTable()->getUserByName($this->identity);
            }

            $bcrypt = new Bcrypt();
            $bcrypt->setCost(self::PASSWORD_COST);

            // if identity not found
            if (!$userModel) {
                $authResult = new Result(
                                Result::FAILURE_IDENTITY_NOT_FOUND,
                                $this->identity,
                                array('A record with the supplied identity could not be found.')
                );
            }
            // else if the identity exists but not activated or disabled
            else if (!$userModel->getActive() || !$userModel->getEnabled()) {
                $authResult = new Result(
                                Result::FAILURE_UNCATEGORIZED,
                                $this->identity,
                                array('A record with the supplied identity is not avaliable.')
                );
            }
            // else if the supplied cretendtial is not valid
            else if (!$bcrypt->verify($this->credential, $userModel->getPassword())) {
                $authResult = new Result(
                                Result::FAILURE_CREDENTIAL_INVALID,
                                $this->identity,
                                array('Supplied credential is invalid.')
                );
            }
        }
        else {
            $userModel = $this->verifiedUser;
        }

        // if authentication was successful
        if (!isset($authResult) && $userModel instanceof UserModel) {
            // update some additional info
            $userModel->setLastIp($_SERVER['REMOTE_ADDR']);
            $userModel->setTimeLogin(gmdate('Y-m-d H:i:s'));
            $hash = $userModel->getHash();

            // if no hash has been set yet
            if (empty($hash)) {
                $hash = md5($userModel->getUsername() . '-' . $userModel->getEmail());
                $userModel->setHash($hash);
            }

            $this->getUserTable()->update($userModel);

            // result success
            $authResult = new Result(
                            Result::SUCCESS,
                            $userModel,
                            array('Authentication successful.')
            );

            // avoid auth process in the same runtime
            $this->setVerifiedUser($userModel);

            // reset the counter
            $this->getUserLockTable()->releaseLock();
        }
        else {
            // increment the counter so the ACL's IP assert can ban for a specific time (LockTable::LOCKTIME)
            $this->getUserLockTable()->setLock();
        }

        return $authResult;
    }

    /**
     * Set a pre-verified user for auto login
     *
     * @param UserModel $verifiedUser
     * @return Adapter
     */
    public function setVerifiedUser(UserModel $verifiedUser)
    {
        $this->verifiedUser = $verifiedUser;
        return $this;
    }

    /**
     * Set the value to be used as the identity
     *
     * @param  string $value
     * @return Adapter
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * Set the credential value to be used
     *
     * @param  string $credential
     * @return Adapter
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

    /**
     * Retrive User Table instance
     *
     * @return UserTable
     */
    public function getUserTable()
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
     * @return Adapter
     */
    public function setUserTable(UserTable $userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }

    /**
     * Retrieve User Lock Table instance
     *
     * @return UserLockTable
     */
    public function getUserLockTable()
    {
        if (!isset($this->userLockTable)) {
            $this->userLockTable = new UserLockTable($this->getServiceManager()->get('database'));
        }
        return $this->userLockTable;
    }

    /**
     * Set User Lock Table instance
     *
     * @param UserTable $userLockTable
     * @return Adapter
     */
    public function setUserLockTable(UserLockTable $userLockTable)
    {
        $this->userLockTable = $userLockTable;
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
     * @return Adapter
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
