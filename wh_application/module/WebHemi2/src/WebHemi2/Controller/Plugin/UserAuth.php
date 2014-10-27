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
 * @package    WebHemi2_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Authentication\Result;
use WebHemi2\Auth\Auth as AuthService;
use WebHemi2\Auth\Adapter\Adapter as AuthAdapter;
use WebHemi2\Model\Table\User as UserTable;
use WebHemi2\Model\User as UserModel;
use WebHemi2\Component\Cipher\Cipher;

/**
 * Controller plugin for Authentication
 *
 * @category   WebHemi2
 * @package    WebHemi2_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserAuth extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /**
     * @var AuthAdapter $authAdapter
     */
    protected $authAdapter;
    /**
     * @var AuthService $authService
     */
    protected $authService;
    /**
     * @var ServiceLocatorInterface $serviceLocator
     */
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
            && APPLICATION_MODULE !== ADMIN_MODULE
        ) {
            $encryptedHash = $_COOKIE['atln-' . bin2hex(APPLICATION_MODULE)];

            // decrypting the hash for this module
            $decryptedHash = Cipher::decode(
                md5(APPLICATION_MODULE),
                base64_decode($encryptedHash),
                md5(md5(APPLICATION_MODULE))
            );

            // chech for the hash
            $userTable = new UserTable($this->getServiceLocator()->get('database'));
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
     * Updates changes in current session
     *
     * @param UserModel $userModel
     */
    public function updateIdentity(UserModel $userModel)
    {
        $this->getAuthService()->clearIdentity();
        $this->getAuthService()->getStorage()->write($userModel);
    }

    /**
     * Proxy convenience method
     *
     * @return UserModel
     */
    public function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }

    /**
     * Proxy convenience method
     *
     * @return UserAuth
     */
    public function clearIdentity()
    {
        $this->getAuthService()->clearIdentity();

        return $this;
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
     *
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
     *
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
     *
     * @return UserAuth
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }
}
