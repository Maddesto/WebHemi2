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
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Controller;

use Zend\Mvc\Controller\AbstractActionController,
	Zend\View\Model\ViewModel,
	Zend\Authentication\Result;

/**
 * WebHemi User Controller
 *
 * @category   WebHemi
 * @package    WebHemi_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserController extends AbstractActionController
{

	/**
	 * User page
	 */
	public function indexAction()
	{
		if (!$this->userAuth()->hasIdentity()) {
			return $this->redirect()->toRoute('user/login');
		}
		return new ViewModel();
	}

	/**
	 * Login page
	 */
	public function loginAction()
	{
		$form = $this->getForm('LoginForm');
		$request = $this->getRequest();

		if ($request->isPost()) {
			$error = false;
			$form->setData($request->getPost());

			$username = $form->get('username')->getValue();
			$password = $form->get('password')->getValue();

			if (empty($username)) {
				$form->get('username')->setMessages(array('No username given.'));
				$error = true;
			}

			if (empty($password)) {
				$form->get('password')->setMessages(array('No password given.'));
				$error = true;
			}

			if (!$error && $form->isValid()) {
				$authAdapter = $this->userAuth()->getAuthAdapter();
				$authAdapter->setIdentity($username);
				$authAdapter->setCredential($password);

				$authResult = $this->userAuth()->getAuthService()->authenticate($authAdapter);

				if (Result::SUCCESS == $authResult->getCode()) {

					$userModel = $authResult->getIdentity();
					

					return $this->redirect()->toRoute('index');
				}
				$form->get('username')->setMessages($authResult->getMessages());
			}


		}

		return array('loginForm' => $form);
	}

	/**
	 * Logout and clear the identity
	 */
	public function logoutAction()
	{
		$this->userAuth()->clearIdentity();

		return $this->redirect()->toRoute('index');
	}
}
