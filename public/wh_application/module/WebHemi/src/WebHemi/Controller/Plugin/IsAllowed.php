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
 * @package    WebHemi_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace webHemi\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
	WebHemi\Auth\AuthService;

/**
 * Controller plugin for ACL
 *
 * @category   WebHemi
 * @package    WebHemi_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class IsAllowed extends AbstractPlugin
{
    /** @var AuthService $authService */
	protected $authService;

	/**
	 * Checks privilege
	 *
	 * @param type $resource
	 * @param type $privilege
	 * @return type
	 */
    public function __invoke($resource, $privilege = null)
    {
        return $this->getAuthService()->isAllowed($resource, $privilege);
    }

	/**
	 * Retrieve authorization service object
	 *
	 * @return AuthService
	 */
    public function getAuthService()
    {
        return $this->authService;
    }

	/**
	 * Set auhtorization service object
	 *
	 * @param AuthService $authService
	 * @return IsAllowed
	 */
    public function setAuthService(AuthService $authService)
    {
        $this->authService = $authService;
        return $this;
    }
}
