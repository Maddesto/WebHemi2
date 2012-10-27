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
	 * Default action
	 *
	 * @return array
	 */
	public function indexAction()
	{
		// if the user is not authenticated
		if (!$this->userAuth()->hasIdentity()) {
			// redirect to login page
			return $this->redirect()->toRoute('user/login');
		}

		return array();
	}

	/**
	 * Login action
	 *
	 * @return array
	 */
	public function loginAction()
	{
		$form = $this->getForm('LoginForm');
		$request = $this->getRequest();

		// upon login attempt
		if ($request->isPost()) {
			$error = false;
			$form->setData($request->getPost());

			$username = $form->get('username')->getValue();
			$password = $form->get('password')->getValue();

			// if no username present
			if (empty($username)) {
				$form->get('username')->setMessages(array('No username given.'));
				$error = true;
			}

			// if no password present
			if (empty($password)) {
				$form->get('password')->setMessages(array('No password given.'));
				$error = true;
			}

			// it everything seems to be valid
			if (!$error && $form->isValid()) {
				$authAdapter = $this->userAuth()->getAuthAdapter();
				$authAdapter->setIdentity($username);
				$authAdapter->setCredential($password);

				$authResult = $this->userAuth()->getAuthService()->authenticate($authAdapter);

				// if user is authenticated
				if (Result::SUCCESS == $authResult->getCode()) {
					$rememberMe = $form->get('remember');

					if ($rememberMe) {
						// if there's such element and checked we save the flag into cookie
						if ($rememberMe->isChecked()) {
							$userModel = $authResult->getIdentity();
							$hash = $userModel->getHash();

							// if no hash has been set yet
							if (empty($hash)) {
								$userTable = $this->userAuth()->getAuthAdapter()->getUserTable();
								$hash      = md5($userModel->getUsername() . '-' . $userModel->getEmail());

								$userModel->setHash($hash);
								$userTable->update($userModel);
							}

							// encrypting the hash for this module
							$encryptedHash = base64_encode(mcrypt_encrypt(
									MCRYPT_RIJNDAEL_256,
									md5(APPLICATION_MODULE),
									$hash,
									MCRYPT_MODE_CBC,
									md5(md5(APPLICATION_MODULE))
							));

							// set cookie for this module
							setcookie('atln-' . bin2hex(APPLICATION_MODULE), $encryptedHash, time() + (60 * 60 * 24 * 14), '/', $_SERVER['SERVER_NAME'], false, true);
						}
					}

					// redirect to main page
					// @TODO: implement redirect to referer if needed
					return $this->redirect()->toRoute('index');
				}
				// attach error message to the form
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
		// if there was autologin cookie, we remove it
		if (isset($_COOKIE['atln-' . bin2hex(APPLICATION_MODULE)])) {
			setcookie('atln-' . bin2hex(APPLICATION_MODULE), 'exit', time() - 1, '/', $_SERVER['SERVER_NAME'], false, true);
		}

		return $this->redirect()->toRoute('index');
	}

	// @TODO: further actions
}
