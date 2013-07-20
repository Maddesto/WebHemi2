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
	WebHemi\Model\Table\User as UserTable,
	Zend\Mvc\Controller\AbstractActionController,
	Zend\Authentication\Result,
	Zend\View\Model\ViewModel,
	Zend\Mvc\MvcEvent;

/**
 * WebHemi User Controller
 *
 * @category   WebHemi
 * @package    WebHemi_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserController extends AbstractActionController
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

		if (Application::ADMIN_MODULE == APPLICATION_MODULE) {
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
	}

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
			$this->redirect()->toRoute('user');
		}

		$userName = $this->params()->fromRoute('userName');
		$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		$userModel = $userTable->getUserByName($userName);

		// if it is me, then redirect to MyProfile
		if ($this->userAuth()->getIdentity()->getUserId() == $userModel->getUserId()) {
			$this->redirect()->toRoute('user/profile');
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
		/* @var $userAuth \WebHemi\Controller\Plugin\UserAuth */
		$userAuth  = $this->userAuth();
		$userName  = $this->params()->fromRoute('userName');
		$userTable = new UserTable($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
		$userModel = $userTable->getUserByName($userName);
		$request   = $this->getRequest();

		if (
			!$userModel
			|| !(
				$userAuth->getIdentity()->getUserId() == $userModel->getUserId()
				|| $userAuth->getIdentity()->getRole() == 'admin'
			)
		) {
			return $this->redirect()->toRoute('user/view', array('userName' => $userName));
		}

		/* @var $editForm \WebHemi\Form\UserForm */
		$editForm = $this->getForm('UserForm');

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

			/* @var $details Zend\Form\Element */
		}
		else {
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
		/* @var $userAuth \WebHemi\Controller\Plugin\UserAuth */
		$userAuth  = $this->userAuth();
		/* @var $form \WebHemi\Form\UserForm */
		$form = $this->getForm('LoginForm');
		$request = $this->getRequest();

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
									/* @var $userModel \WebHemi\Model\User */
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
		$this->userAuth()->clearIdentity();
		// if there was autologin cookie, we remove it
		if (isset($_COOKIE['atln-' . bin2hex(APPLICATION_MODULE)])) {
			setcookie('atln-' . bin2hex(APPLICATION_MODULE), 'exit', time() - 1, '/', $_SERVER['SERVER_NAME'], false, true);
		}

		return $this->redirect()->toRoute('index');
	}
}
