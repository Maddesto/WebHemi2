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
 * @package   WebHemi2_Controller_Plugin
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\Result;
use WebHemi2\Auth\Auth as AuthService;
use WebHemi2\Auth\Adapter\Adapter as AuthAdapter;
use WebHemi2\Model\Table\User as UserTable;
use WebHemi2\Model\User as UserModel;
use WebHemi2\Component\Cipher\Cipher;
use WebHemi2\Controller\AbstractController;

/**
 * Controller plugin for Authentication
 *
 * @category  WebHemi2
 * @package   WebHemi2_Controller_Plugin
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class UserAuth extends AbstractPlugin
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
     * Proxy convenience method
     *
     * @return bool
     */
    public function hasIdentity()
    {
        $identity = $this->getAuthService()->hasIdentity();

        // if not already logged in and has autologin cookie for the module which is not the ADMIN module
        if (!$identity
            && isset($_COOKIE[AUTOLOGIN_COOKIE_PREFIX .'-' . bin2hex(APPLICATION_MODULE)])
            && APPLICATION_MODULE !== ADMIN_MODULE
        ) {
            $encryptedHash = $_COOKIE[AUTOLOGIN_COOKIE_PREFIX .'-' . bin2hex(APPLICATION_MODULE)];

            // decrypting the hash for this module
            $decryptedHash = Cipher::decode(
                $encryptedHash,
                md5(APPLICATION_MODULE),
                md5(md5(APPLICATION_MODULE))
            );

            if ($decryptedHash) {
                // check for the hash
                /** @var \Zend\Db\Adapter\Adapter $adapter */
                $adapter = $this->getServiceLocator()->get('database');
                $userTable = new UserTable($adapter);
                $userModel = $userTable->getUserByHash($decryptedHash);

                if ($userModel instanceof UserModel) {
                    $authAdapter = $this->getAuthAdapter();
                    $authAdapter->setVerifiedUser($userModel);

                    $authResult = $this->getAuthService()->authenticate($authAdapter);

                    // if user is authenticated
                    if (Result::SUCCESS == $authResult->getCode()) {
                        $this->getAuthService()->getStorage()->regenerateStorageId();
                        $identity = true;
                    }
                }
            }
        }

        return $identity;
    }

    /**
     * Updates changes in current session
     *
     * @param UserModel $userModel
     *
     * @return void
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
     * @return UserAuth
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
     * @return UserAuth
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
        /** @var AbstractController $controller */
        $controller = $this->getController();
        return $controller->getServiceLocator();
    }
}
