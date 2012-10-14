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
 * @package    WebHemi_Auth_Adapter
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Auth\Adapter;

use Zend\Authentication\Adapter\AdapterInterface,
	Zend\Authentication\Result,
	Zend\ServiceManager\ServiceManagerAwareInterface,
	Zend\ServiceManager\ServiceManager,
	Zend\Crypt\Password\Bcrypt,
	WebHemi\Model\Table\User as UserTable,
	WebHemi\Model\Table\Lock as UserLockTable;

/**
 * WebHemi User Authentication Adapter
 *
 * @category   WebHemi
 * @package    WebHemi_Auth_Adapter
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
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

	/**
	 * This method is called to attempt an authentication. Previous to this
	 * call, this adapter would have already been configured with all
	 * necessary information to successfully connect to a database table and
	 * attempt to find a record matching the provided identity.
	 *
	 * @throws Exception\RuntimeException if answering the authentication query is impossible
	 * @return Result
	 */
	public function authenticate()
	{
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
			$authResult =  new Result(
					Result::FAILURE_IDENTITY_NOT_FOUND,
					$this->identity,
					array('A record with the supplied identity could not be found.')
			);
		}
		// else if the identity exists but not activated or disabled
		else if (!$userModel->getActive() || !$userModel->getEnabled()) {
			$authResult =  new Result(
					Result::FAILURE_UNCATEGORIZED,
					$this->identity,
					array('A record with the supplied identity is not avaliable.')
			);
		}
		// else if the supplied cretendtial is not valid
		else if (!$bcrypt->verify($this->credential, $userModel->getPassword())) {
			$authResult =  new Result(
					Result::FAILURE_CREDENTIAL_INVALID,
					$this->identity,
					array('Supplied credential is invalid.')
			);
		}

		// if authentication was successful
		if (!isset($authResult)) {
			// update some additional info
			$userModel->setLastIp($_SERVER['REMOTE_ADDR']);
			$userModel->setTimeLogin(gmdate('Y-m-d H:i:s'));
			$this->getUserTable()->update($userModel);

			$authResult = new Result(
				Result::SUCCESS,
				$userModel,
				array('Authentication successful.')
			);

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
	 * Get User Table
	 *
	 * @return UserTable
	 */
	public function getUserTable()
	{
		if (!isset($this->userTable)) {
			$this->userTable = new UserTable($this->getServiceManager()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->userTable;
	}

	/**
	 * Set User Table
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
	 * Get User Lock Table
	 *
	 * @return UserLockTable
	 */
	public function getUserLockTable()
	{
		if (!isset($this->userLockTable)) {
			$this->userLockTable = new UserLockTable($this->getServiceManager()->get('Zend\Db\Adapter\Adapter'));
		}
		return $this->userLockTable;
	}

	/**
	 * Set User Lock Table
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
