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

namespace WebHemi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
	Zend\ServiceManager\ServiceLocatorInterface,
	Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\Authentication\Result,
	WebHemi\Application,
	WebHemi\Auth\Auth as AuthService,
	WebHemi\Auth\Adapter\Adapter as AuthAdapter,
	WebHemi\Model\Table\User as UserTable,
	WebHemi\Model\User as UserModel;

use \Exception as Exp;

/**
 * Controller plugin for Authentication
 *
 * @category   WebHemi
 * @package    WebHemi_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserAuth extends AbstractPlugin implements ServiceLocatorAwareInterface
{
	/** @var AuthAdapter */
	protected $authAdapter;
	/** @var AuthService */
	protected $authService;
	/** @var ServiceLocator */
	protected $serviceLocator;

	/**
	 * Proxy convenience method
	 *
	 * @return bool
	 */
	public function hasIdentity()
	{
		$identity = $this->getAuthService()->hasIdentity();

		// if not already logged in and has autologin cookie for the module which is not the ADMIN module
		if (!$identity
				&& isset($_COOKIE['atln-' . bin2hex(APPLICATION_MODULE)])
				&& APPLICATION_MODULE !== Application::ADMIN_MODULE
		) {
			$encryptedHash = $_COOKIE['atln-' . bin2hex(APPLICATION_MODULE)];

			// decrypting the hash for this module
			$decryptedHash = trim(rtrim(mcrypt_decrypt(
					MCRYPT_RIJNDAEL_256,
					md5(APPLICATION_MODULE),
					base64_decode($encryptedHash),
					MCRYPT_MODE_CBC,
					md5(md5(APPLICATION_MODULE))
			), "\0"));

			// chech for the hash
			$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
			$userModel = $userTable->getUserByHash($decryptedHash);

			if ($userModel instanceof UserModel) {
				$authAdapter = $this->getAuthAdapter();
				$authAdapter->setVerifiedUser($userModel);

				$authResult = $this->getAuthService()->authenticate($authAdapter);

				// if user is authenticated
				if (Result::SUCCESS == $authResult->getCode()) {
					$identity = true;
				}
			}
		}

		return $identity;
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
	 * Retrieve AuthAdapter instance
	 *
	 * @return AuthAdapter
	 */
	public function getAuthAdapter()
	{
		if (!isset($this->authAdapter)) {
			$this->authAdapter = $this->getServiceLocator()->get('authAdapter');
		}
		return $this->authAdapter;
	}

	/**
	 * Set AuthAdapter instance
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
	 * Retrieve AuthService instance
	 *
	 * @return AuthService
	 */
	public function getAuthService()
	{
		if (!isset($this->authService)) {
            $this->authService = $this->getServiceLocator()->get('auth');
        }
		return $this->authService;
	}

	/**
	 * Set AuthService instance
	 *
	 * @param AuthService $authService
	 * @retrun UserAuth
	 */
	public function setAuthService(AuthService $authService)
	{
		$this->authService = $authService;
		return $this;
	}

	/**
     * Retrieve ServiceLocator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
	{
		return $this->serviceLocator->getController()->getServiceLocator();
	}

	/**
     * Set ServiceLocator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->serviceLocator = $serviceLocator;
		return $this;
	}

}
