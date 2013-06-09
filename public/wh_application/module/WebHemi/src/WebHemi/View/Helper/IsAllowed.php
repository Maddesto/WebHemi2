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
	WebHemi\Acl\Acl;

/**
 * View helper for ACL
 *
 * @category   WebHemi
 * @package    WebHemi_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class IsAllowed extends AbstractHelper
{
	/** @var Acl $authService */
	protected $aclService;

	/**
	 * Check privilege
	 *
	 * @param type $resource
	 * @param type $privilege
	 * @return type
	 */
	public function __invoke($resource, $privilege = null)
	{
		$acl                = $this->getAclService();
		$controllerResource = 'Controller-' . ucfirst(strtolower($resource));
		if (strpos($resource, '/') === false) {
			$controllerResource .= '/*';
		}
		$routeResource      = 'Route-' . $resource;

		return $acl->isAllowed($resource, $privilege)
				&& $acl->isAllowed($controllerResource, $privilege)
				&& $acl->isAllowed($routeResource, $privilege);
	}

	/**
	 * Retrieve ACL service object
	 *
	 * @return WebHemi\Acl\Acl
	 */
	public function getAclService()
	{
		return $this->aclService;
	}

	/**
	 * Set ACL service object
	 *
	 * @param WebHemi\Acl\Acl $aclService
	 * @return IsAllowed
	 */
	public function setAclService(Acl $aclService)
	{
		$this->aclService = $aclService;
		return $this;
	}
}
