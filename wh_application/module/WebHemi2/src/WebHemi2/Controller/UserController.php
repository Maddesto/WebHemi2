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
 * @package    WebHemi2_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Controller;

use WebHemi2\Model\Table\User as UserTable;
use WebHemi2\Auth\Adapter\Adapter as AuthAdapter;
use Zend\Crypt\Password\Bcrypt;
use Zend\Authentication\Result;
use Zend\View\Model\ViewModel;
use WebHemi2\Component\Cipher\Cipher;

/**
 * WebHemi2 User Controller
 *
 * @category   WebHemi2
 * @package    WebHemi2_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserController extends AbstractController
{
    /**
     * Default action
     *
     * @return array
     */
    public function indexAction()
    {
        // if the user is not authenticated
        if (!$this->getUserAuth()->hasIdentity()) {
            // redirect to login page
            return $this->redirect()->toRoute('index/user/login');
        }

        return array();
    }

    /**
     * user profile action
     *
     * @return array
     */
    public function profileAction()
    {
        return array();
    }

    /**
     * View User info
     *
     * @return array
     */
    public function viewuserAction()
    {
        if (!$this->isAllowed('admin/viewuser')) {
            $this->redirect()->toRoute('index/user');
        }

        $userName = $this->params()->fromRoute('userName');
        $userTable = new UserTable($this->getServiceLocator()->get('database'));
        $userModel = $userTable->getUserByName($userName);

        // redirect to MyProfile when view own
        if ($this->getUserAuth()->getIdentity()->getUserId() == $userModel->getUserId()) {
            $this->redirect()->toRoute('index/user/profile');
        }

        return array('userModel' => $userModel);
    }

    /**
     * Edit User info
     *
     * @return array
     */
    public function edituserAction()
    {
        $userAuth     = $this->getUserAuth();
        $userName     = $this->params()->fromRoute('userName');
        $userTable    = new UserTable($this->getServiceLocator()->get('database'));
        $userModel    = $userTable->getUserByName($userName);
        $request      = $this->getRequest();
        $isOwnProfile = $userAuth->getIdentity()->getUserId() == $userModel->getUserId();

        if (!$userModel
            || !($isOwnProfile || $userAuth->getIdentity()->getRole() == 'admin')
        ) {
            return $this->redirect()->toRoute('index/user/view', array('userName' => $userName));
        }

        /* @var $editForm \WebHemi2\Form\UserForm */
        $editForm = $this->getForm('UserForm');

        if ($request->isPost()) {
            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $editForm->setData($postData);

            if ($editForm->isValid()) {
                $userData = $editForm->getData();
                // only the admin can edit some data
                if ($userAuth->getIdentity()->getRole() == 'admin') {
                    if (!empty($userData['accountInfo']['username'])) {
                        $userModel->setUsername($userData['accountInfo']['username']);
                    }

                    if (!empty($userData['accountInfo']['email'])) {
                        $userModel->setEmail($userData['accountInfo']['email']);
                    }

                    // renew the hash (for autologin)
                    $hash = md5($userModel->getUsername() . '-' . $userModel->getEmail());
                    $userModel->setHash($hash);

                    // it is not allowed for an admin to change his/her own privilege
                    // imagine what will happen if no more admin left...
                    if (!$isOwnProfile) {
                        if (!empty($userData['accountInfo']['role'])) {
                            $userModel->setRole($userData['accountInfo']['role']);
                        }
                    }
                }

                // encrypt the password
                if (!empty($userData['securityInfo']['password'])) {
                    $bcrypt = new Bcrypt();
                    $bcrypt->setCost(AuthAdapter::PASSWORD_COST);
                    $userModel->setPassword($bcrypt->create($userData['securityInfo']['password']));
                }

                // user meta data
                $userModel->setAvatar($userData['personalInfo']['avatarInfo']['avatar']);
                $userModel->setDisplayName($userData['personalInfo']['displayname']);
                $userModel->setHeadLine($userData['personalInfo']['headline']);
                $userModel->setDisplayEmail($userData['personalInfo']['displayemail']);
                $userModel->setDetails($userData['personalInfo']['details']);
                $userModel->setPhoneNumber($userData['contactInfo']['phonenumber']);
                $userModel->setLocation($userData['contactInfo']['location']);
                $userModel->setInstantMessengers($userData['contactInfo']['instantmessengers']);
                $userModel->setSocialNetworks($userData['contactInfo']['socialnetworks']);
                $userModel->setWebsites($userData['contactInfo']['websites']);

                unset($userData);

                try {
                    $result = $userTable->update($userModel);

                    if ($result !== false) {
                        // if save was success and own data hase been changed, then update the session
                        if ($isOwnProfile) {
                            $userAuth->updateIdentity($userModel);
                        }
                        return $this->redirect()->toRoute('index/user/view', array('userName' => $userModel->getUsername()));
                    }
                } catch (Exception $e) {
                    $editForm->setMessages(
                        array(
                            'submit' => $e->getMessage()
                        )
                    );
                }
            }
        } else {
            $editForm->bind($userModel);
        }

        return array(
            'editForm'  => $editForm,
            'userModel' => $userModel,
        );
    }

    /**
     * Login action
     *
     * @return array
     */
    public function loginAction()
    {
        /* @var $form \WebHemi2\Form\UserForm */
        $form = $this->getForm('LoginForm');
        $request = $this->getRequest();
        $userAuth  = $this->getUserAuth();

        // upon login attempt
        if ($request->isPost()) {
            $error = false;
            $form->setData($request->getPost());

            $identification = $form->get('identification')->getValue();
            $password = $form->get('password')->getValue();

            // if no identification present
            if (empty($identification)) {
                $form->get('identification')->setMessages(array('No identification given.'));
                $error = true;
            }

            // if no password present
            if (empty($password)) {
                $form->get('password')->setMessages(array('No password given.'));
                $error = true;
            }

            // it everything seems to be valid
            if (!$error && $form->isValid()) {
                $authAdapter = $userAuth->getAuthAdapter();
                $authAdapter->setIdentity($identification);
                $authAdapter->setCredential($password);

                $authResult = $userAuth->getAuthService()->authenticate($authAdapter);

                switch($authResult->getCode()) {
                    // if user is authenticated
                    case Result::SUCCESS:
                        if ($form->has('remember', true)) {
                            $rememberMe = $form->get('remember');
                            if ($rememberMe) {
                                // if there's such element and checked we save the flag into cookie
                                if ($rememberMe->isChecked()) {
                                    /* @var $userModel \WebHemi2\Model\User */
                                    $userModel = $authResult->getIdentity();
                                    $hash = $userModel->getHash();

                                    // if no hash has been set yet
                                    if (empty($hash)) {
                                        $userTable = $userAuth->getAuthAdapter()->getUserTable();
                                        $hash      = md5($userModel->getUsername() . '-' . $userModel->getEmail());

                                        $userModel->setHash($hash);
                                        $userTable->update($userModel);
                                    }

                                    // encrypting the hash for this module
                                    $encryptedHash = base64_encode(
                                        Cipher::encode(
                                            md5(APPLICATION_MODULE),
                                            $hash,
                                            md5(md5(APPLICATION_MODULE))
                                        )
                                    );

                                    // set cookie for this module
                                    setcookie(
                                        'atln-' . bin2hex(APPLICATION_MODULE),
                                        $encryptedHash,
                                        time() + (60 * 60 * 24 * 14),
                                        '/',
                                        $_SERVER['SERVER_NAME'],
                                        false,
                                        true
                                    );
                                }
                            }
                        }

                        // redirect to main page
                        // @TODO: implement redirect to referer if needed
                        return $this->redirect()->toRoute('index');
                        break;

                    case Result::FAILURE_CREDENTIAL_INVALID:
                        // attach error message to the form
                        $form->get('password')->setMessages($authResult->getMessages());
                        break;

                    default:
                        // attach error message to the form
                        $form->get('identification')->setMessages($authResult->getMessages());
                        break;
                }
            }
        }
        $view = new ViewModel(array('loginForm' => $form));
        return $view;
    }

    /**
     * Logout and clear the identity
     */
    public function logoutAction()
    {
        $this->getUserAuth()->clearIdentity();
        // if there was autologin cookie, we remove it
        if (isset($_COOKIE['atln-' . bin2hex(APPLICATION_MODULE)])) {
            setcookie(
                'atln-' . bin2hex(APPLICATION_MODULE),
                'exit',
                time() - 1,
                '/',
                $_SERVER['SERVER_NAME'],
                false,
                true
            );
        }

        return $this->redirect()->toRoute('index');
    }
}
