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
 * @package   WebHemi2_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Controller;

use Exception;
use WebHemi2\Model\Table\User as UserTable;
use WebHemi2\Model\User as UserModel;
use WebHemi2\Auth\Adapter\Adapter as AuthAdapter;
use WebHemi2\Form\AbstractForm;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\MvcEvent;

/**
 * WebHemi2
 *
 * Admin Controller
 *
 * @category  WebHemi2
 * @package   WebHemi2_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 *
 * @method AbstractForm getForm($formName, $name = null) Instantiate a WebHemi2 form
 */
class UserManagementController extends AdminController
{
    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     *
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->layout()->setVariable('sectionTitle', 'Control Panel');
        $this->layout()->setVariable('sectionClass', 'control-panel');

        return parent::onDispatch($e);
    }

    /**
     * User index page
     *
     * @return array
     */
    public function userListAction()
    {
        $userTable = new UserTable($this->getDatabaseAdapter());
        $userList = $userTable->getUserList();

        return ['userList' => $userList];
    }

    /**
     * Add new User
     *
     * @return array
     */
    public function userAddAction()
    {
        $userTable = new UserTable($this->getDatabaseAdapter());
        $userModel = new UserModel();
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        /* @var $editForm \WebHemi2\Form\UserForm */
        $editForm = $this->getForm('UserForm', 'adduser');

        if ($request->isPost()) {
            $postData = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $editForm->setData($postData);

            if ($editForm->isValid()) {
                $userData = $editForm->getData();

                // user data
                $userModel->setUsername($userData['accountInfo']['username']);
                $userModel->setEmail($userData['accountInfo']['email']);

                $hash = md5($userModel->getUsername() . '-' . $userModel->getEmail());
                $userModel->setHash($hash);

                $bcrypt = new Bcrypt();
                $bcrypt->setCost(AuthAdapter::PASSWORD_COST);
                $userModel->setPassword($bcrypt->create($userData['securityInfo']['password']));

                $userModel->setRegisterIp($_SERVER['REMOTE_ADDR']);
                $userModel->setTimeRegister(new \DateTime(gmdate('Y-m-d H:i:s')));
                $userModel->setActive(false);
                $userModel->setEnabled(false);
                $userModel->setUserId(null);

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
                    $result = $userTable->insert($userModel);

                    if ($result !== false) {
                        return $this->redirect()->toRoute(
                            'index/control-panel/user/view',
                            ['userName' => $userModel->getUsername()]
                        );
                    }
                } catch (Exception $e) {
                    $editForm->setMessages(
                        [
                            'submit' => $e->getMessage()
                        ]
                    );
                }
            }
        }

        return [
            'editForm' => $editForm,
        ];
    }

    /**
     * Disable User
     *
     * @return array
     */
    public function userDisableAction()
    {
        $userName = $this->params()->fromRoute('userName');
        $userTable = new UserTable($this->getDatabaseAdapter());
        $userModel = $userTable->getUserByName($userName);

        if ($userModel) {
            // if it is NOT me, then allow the action
            if ($this->getUserAuth()->getIdentity()->getUserId() != $userModel->getUserId()) {
                $userModel->setEnabled(false);
                $userTable->update($userModel);
            }
        }
        return $this->redirect()->toRoute('index/control-panel/user/view', ['userName' => $userName]);
    }

    /**
     * Enable User
     *
     * @return array
     */
    public function userEnableAction()
    {
        $userName = $this->params()->fromRoute('userName');
        $userTable = new UserTable($this->getDatabaseAdapter());
        $userModel = $userTable->getUserByName($userName);

        if ($userModel) {
            // if it is NOT me, then allow the action
            if ($this->getUserAuth()->getIdentity()->getUserId() != $userModel->getUserId()) {
                // Enabling a user also set it to activate.
                $userModel->setActive(true);
                $userModel->setEnabled(true);
                $userTable->update($userModel);
            }
        }
        return $this->redirect()->toRoute('index/control-panel/user/view', ['userName' => $userName]);
    }

    /**
     * Activate User
     *
     * @return array
     */
    public function userActivateAction()
    {
        $userName = $this->params()->fromRoute('userName');
        $userTable = new UserTable($this->getDatabaseAdapter());
        $userModel = $userTable->getUserByName($userName);

        if ($userModel) {
            // if it is NOT me, then allow the action
            if ($this->getUserAuth()->getIdentity()->getUserId() != $userModel->getUserId()) {
                $userModel->setActive(true);
                $userTable->update($userModel);
            }
        }
        return $this->redirect()->toRoute('index/control-panel/user/view', ['userName' => $userName]);
    }

    /**
     * Delete User
     *
     * @return array
     */
    public function userDeleteAction()
    {
        $userName = $this->params()->fromRoute('userName');
        $userTable = new UserTable($this->getDatabaseAdapter());
        $userModel = $userTable->getUserByName($userName);

        if ($userModel) {
            // if it is NOT me, then allow the action
            if ($this->getUserAuth()->getIdentity()->getUserId() != $userModel->getUserId()) {
                $userTable->delete(['user_id' => $userModel->getUserId()]);
            }
        }
        return $this->redirect()->toRoute('index/control-panel/user');
    }
}
