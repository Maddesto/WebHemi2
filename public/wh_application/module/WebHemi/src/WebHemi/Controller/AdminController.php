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

use WebHemi\Controller\UserController,
	Zend\View\Model\ViewModel;

/**
 * WebHemi Admin Controller
 *
 * @category   WebHemi
 * @package    WebHemi_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class AdminController extends UserController
{
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
	 * User info page
	 *
	 * @return array
	 */
	public function userAction()
	{
		return array();
	}

	// @TODO: implement further actions
}
