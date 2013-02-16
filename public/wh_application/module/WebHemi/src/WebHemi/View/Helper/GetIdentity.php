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
 * @package    WebHemi_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\View\Helper;

use Zend\View\Helper\AbstractHelper,
	Zend\Authentication\AuthenticationService,
	WebHemi\Model\User;

/**
 * View helper for User Auhtentication
 *
 * @category   WebHemi
 * @package    WebHemi_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class GetIdentity extends AbstractHelper
{
	/** @var AuthenticationService $authService */
	protected $authService;

	/**
	 * Retrieve user identity
	 *
	 * @return User
	 */
	public function __invoke()
	{
		if ($this->getAuthService()->hasIdentity()) {
			return $this->getAuthService()->getIdentity();
		}
		else {
			return false;
		}
	}

	/**
	 * Retrieve authService
	 *
	 * @return AuthenticationService
	 */
	public function getAuthService()
	{
		return $this->authService;
	}

	/**
	 * Set authService
	 *
	 * @param AuthenticationService $authService
	 */
	public function setAuthService(AuthenticationService $authService)
	{
		$this->authService = $authService;
		return $this;
	}

}
