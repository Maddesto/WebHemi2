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

use Zend\Authentication\Adapter\DbTable,
	Zend\Authentication\Result,
	Zend\ServiceManager\ServiceManagerAwareInterface,
	Zend\ServiceManager\ServiceManager,
	WebHemi\Model\Table\User as UserTable;

/**
 * WebHemi User Authentication Adapter
 *
 * @category   WebHemi
 * @package    WebHemi_Auth_Adapter
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Adapter extends DbTable implements ServiceManagerAwareInterface
{
	 /**
     * This method is called to attempt an authentication. Previous to this
     * call, this adapter would have already been configured with all
     * necessary information to successfully connect to a database table and
     * attempt to find a record matching the provided identity.
     *
     * @throws Exception\RuntimeException if answering the authentication query is impossible
     * @return AuthenticationResult
     */
    public function authenticate()
    {
		$authResult = parent::authenticate();

		if($authResult->isValid())  {
			$userModel = $this->getTable()->getUserByName($authResult->getIdentity());
			$authResult = new Result(
					$authResult->getCode(),
					$userModel,
					$authResult->getMessages()
			);
		}

        return $authResult;
    }

	/**
     * Get User Table
     *
     * @return UserTable
     */
    public function getTable()
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
