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
 * @package    WebHemi_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace webHemi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
	Zend\Authentication\AuthenticationService,
	Zend\ServiceManager\ServiceManager,
	Zend\ServiceManager\ServiceManagerAwareInterface;

//use WebHemi\Auth\Adapter\Adapter as AuthAdapter,
//	WebHemi\Auth\Auth;

/**
 * Controller plugin for Authentication
 *
 * @category   WebHemi
 * @package    WebHemi_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserAuth extends AbstractPlugin implements ServiceManagerAwareInterface
{
	/** @var AuthAdapter */
	protected $authAdapter;
	/** @var AuthenticationService */
	protected $authService;
	/** @var ServiceManager */
	protected $serviceManager;

	/**
	 * Proxy convenience method
	 *
	 * @return bool
	 */
	public function hasIdentity()
	{
		return $this->getAuthService()->hasIdentity();
	}

	/**
	 * Proxy convenience method
	 *
	 * @return mixed
	 */
	public function getIdentity()
	{
		return $this->getAuthService()->getIdentity();
	}

	/**
	 * Proxy convenience method
	 *
	 * @return mixed
	 */
	public function clearIdentity()
	{
		return $this->getAuthService()->clearIdentity();
	}

	/**
	 * Get authAdapter
	 *
	 * @return AuthAdapter
	 */
	public function getAuthAdapter()
	{
		if (!isset($this->authAdapter)) {
			$this->authAdapter = $this->getServiceManager()->get('authAdapter');
		}
		return $this->authAdapter;
	}

	/**
	 * Set authAdapter
	 *
	 * @param AuthAdapter $authAdapter
	 * @retrun UserAuth
	 */
	public function setAuthAdapter(AuthAdapter $authAdapter)
	{
		$this->authAdapter = $authAdapter;
		return $this;
	}

	/**
	 * Get authService
	 *
	 * @return Auth
	 */
	public function getAuthService()
	{
		if (!isset($this->authService)) {
            $this->authService = $this->getServiceManager()->get('auth');
        }
		return $this->authService;
	}

	/**
	 * Set authService
	 *
	 * @param AuthenticationService $authService
	 * @retrun UserAuth
	 */
	public function setAuthService(AuthenticationService $authService)
	{
		$this->authService = $authService;
		return $this;
	}

	/**
	 * Retrieve service manager instance
	 *
	 * @return ServiceManager
	 */
	public function getServiceManager()
	{
		return $this->serviceManager->getServiceLocator();
	}

	/**
	 * Set service manager instance
	 *
	 * @param ServiceManager $locator
	 * @return UserAuth
	 */
	public function setServiceManager(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
		return $this;
	}

}
