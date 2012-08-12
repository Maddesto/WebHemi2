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
 * @package    WebHemi_Acl_Assert
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Acl\Assert;

use WebHemi\Model\Table\Lock,
	Zend\ServiceManager\ServiceManager,
	Zend\Permissions\Acl\Acl,
	Zend\Permissions\Acl\Resource\ResourceInterface,
	Zend\Permissions\Acl\Role\RoleInterface,
	Zend\Permissions\Acl\Assertion\AssertionInterface;

/**
 * WebHemi ACL Assertion
 *
 * @category   WebHemi
 * @package    WebHemi_Acl_Assert
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class CleanIPAssertion implements AssertionInterface
{
	/** @var Zend\ServiceManager\ServiceManager $serviceManager */
	protected $serviceManager;

	public function __construct(ServiceManager $serviceManager)
	{
		$this->serviceManager = $serviceManager;
	}

	/**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  Acl                 $acl
     * @param  RoleInterface       $role
     * @param  ResourceInterface   $resource
     * @param  string              $privilege
     * @return boolean
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
		return $this->serviceManager->get('lockTable')->getLock()->tryings >= Lock::MAXTRYINGS ? false : true;
    }
}