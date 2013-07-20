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

use WebHemi\Controller\UserController,
	WebHemi\Model\Table\User as UserTable,
	Zend\View\Model\ViewModel,
	Zend\Mvc\MvcEvent;

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
		$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
		$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		$userModel = $userTable->getUserByName($userName);
		$request   = $this->getRequest();
		
		/* @var $editForm \WebHemi\Form\UserForm */
		$editForm = $this->getForm('UserForm', 'adduser');
		
		if ($request->isPost()) {
			$postData = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			
			$editForm->setData($postData);
dump($postData, 'Post');
dump($editForm->isValid(), 'Is Valid?');
			if ($editForm->isValid()) {
dump($editForm->getData(), 'Data');
			}
			else {
dump($editForm->getMessages(), 'Error Messages');
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
		$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
		$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
		$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
		$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
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
