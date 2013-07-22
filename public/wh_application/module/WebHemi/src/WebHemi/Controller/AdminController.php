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
 * @package    WebHemi_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Controller;

use WebHemi\Application,
	WebHemi\Controller\UserController,
	WebHemi\Model\Table\User as UserTable,
	WebHemi\Model\User as UserModel,
	WebHemi\Auth\Adapter\Adapter as AuthAdapter,
	Zend\Mvc\Controller\AbstractActionController,
	Zend\Mvc\MvcEvent,
	Zend\Crypt\Password\Bcrypt,
	Zend\Authentication\Result,
	Zend\View\Model\ViewModel;

/**
 * WebHemi Admin Controller
 *
 * @category   WebHemi
 * @package    WebHemi_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class AdminController extends UserController
{
	/**
	 * Execute the request
	 *
	 * @param  MvcEvent $e
	 * @return mixed
	 */
	public function onDispatch(MvcEvent $e)
	{
		parent::onDispatch($e);

		$layout = $this->layout();

		$headerBlock = new ViewModel();
		$headerBlock->setTemplate('block/AdminHeaderBlock');

		$menuBlock = new ViewModel();
		$menuBlock->activeMenu = 'application';
		$menuBlock->setTemplate('block/AdminMenuBlock');

		$footerBlock = new ViewModel();
		$footerBlock->setTemplate('block/AdminFooterBlock');

		$layout->addChild($headerBlock, 'HeaderBlock')
			->addChild($menuBlock, 'MenuBlock')
			->addChild($footerBlock, 'FooterBlock');
	}

	/**
	 * Default action
	 *
	 * @return array
	 */
	public function indexAction()
	{
	    return array();
	}

	/**
	 * Login page
	 */
	public function loginAction()
	{
		$view = parent::loginAction();

		// if we display the login page
		if ($view instanceof ViewModel) {
			// TODO: make this an editable config value
			$view->setVariables(array(
				'headerTitle' => 'WebHemi Administration Login',
				'siteTitle'   => 'WH Admin',
			));

			// the login page has its built-in layout
			$view->setTerminal(true);
		}

		return $view;
	}

	/**
	 * User index page
	 *
	 * @return array
	 */
	public function userAction()
	{
		$userTable = new UserTable($this->getServiceLocator()->get('database'));
		$userList = $userTable->getUserList();

		return array('userList' => $userList);
	}

	/**
	 * Add new User
	 *
	 * @return array
	 */
	public function adduserAction()
	{
		/* @var $userAuth \WebHemi\Controller\Plugin\UserAuth */
		$userAuth  = $this->userAuth();
		$userName  = $this->params()->fromRoute('userName');
		$userTable = new UserTable($this->getServiceLocator()->get('database'));
		$userModel = new UserModel();
		$request   = $this->getRequest();

		/* @var $editForm \WebHemi\Form\UserForm */
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
				$userModel->setRole($userData['accountInfo']['role']);

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
						return $this->redirect()->toRoute('user/view', array('userName' => $userModel->getUsername()));
					}
				}
				catch (\Exception $e) {
					$editForm->setMessages(
						array(
							'submit' => $e->getMessage()
						)
					);
				}
			}
		}

		return array(
			'editForm'  => $editForm,
		);
	}

	/**
	 * Disable User
	 *
	 * @return array
	 */
	public function disableuserAction()
	{
		$userName  = $this->params()->fromRoute('userName');
		$userTable = new UserTable($this->getServiceLocator()->get('database'));
		$userModel = $userTable->getUserByName($userName);

		if ($userModel) {
			// if it is NOT me, then allow the action
			if ($this->userAuth()->getIdentity()->getUserId() != $userModel->getUserId()) {
				$userModel->setEnabled(false);
				$userTable->update($userModel);
			}
		}
		return $this->redirect()->toRoute('user/view', array('userName' => $userName));
	}

	/**
	 * Enable User
	 *
	 * @return array
	 */
	public function enableuserAction()
	{
		$userName  = $this->params()->fromRoute('userName');
		$userTable = new UserTable($this->getServiceLocator()->get('database'));
		$userModel = $userTable->getUserByName($userName);

		if ($userModel) {
			// if it is NOT me, then allow the action
			if ($this->userAuth()->getIdentity()->getUserId() != $userModel->getUserId()) {
				$userModel->setEnabled(true);
				$userTable->update($userModel);
			}
		}
		return $this->redirect()->toRoute('user/view', array('userName' => $userName));
	}

	/**
	 * Activate User
	 *
	 * @return array
	 */
	public function activateuserAction()
	{
		$userName  = $this->params()->fromRoute('userName');
		$userTable = new UserTable($this->getServiceLocator()->get('database'));
		$userModel = $userTable->getUserByName($userName);

		if ($userModel) {
			// if it is NOT me, then allow the action
			if ($this->userAuth()->getIdentity()->getUserId() != $userModel->getUserId()) {
				$userModel->setActive(true);
				$userTable->update($userModel);
			}
		}
		return $this->redirect()->toRoute('user/view', array('userName' => $userName));
	}

	/**
	 * Delete User
	 *
	 * @return array
	 */
	public function deleteuserAction()
	{
		$userName  = $this->params()->fromRoute('userName');
		$userTable = new UserTable($this->getServiceLocator()->get('database'));
		$userModel = $userTable->getUserByName($userName);

		if ($userModel) {
			// if it is NOT me, then allow the action
			if ($this->userAuth()->getIdentity()->getUserId() != $userModel->getUserId()) {
				$userModel->setActive(true);
				$userTable->delete(array('user_id' => $userModel->getUserId()));
			}
		}
		return $this->redirect()->toRoute('user');
	}


	/**
	 * WebHemi info page
	 *
	 * @return array
	 */
	public function aboutAction()
	{
		return array();
	}
}
