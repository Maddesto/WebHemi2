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
 * @package    WebHemi_Acl_Provider
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Acl\Provider;

use WebHemi\Acl\Role;

/**
 * WebHemi Role Container and Provider
 *
 * @category   WebHemi
 * @package    WebHemi_Acl_Provider
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class RoleProvider
{
	/** @var array $roles */
	protected $roles = array();

	/**
	 * Contructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = array())
	{
		$roles = array();
		// we process the config and build the Role "tree" (actually it is a list with references to parents)
		foreach ($config as $roleName => $roleOptions) {
			// if it is a new role, we set it
			if (!isset($roles[$roleName])) {
				$roles[$roleName] = new Role($roleName);
			}

			// if there is a parent given in the options
			if (isset($roleOptions['parent']) && !empty($roleOptions['parent'])) {
				$parentRoleName = $roleOptions['parent'];
				// if the parent is a new role, we set it
				if (!isset($roles[$parentRoleName])) {
					$roles[$parentRoleName] = new Role($parentRoleName);
				}

				// we set the parent for the role
				$roles[$roleName]->setParentRole($roles[$parentRoleName]);
			}
		}

		// save the role list
		$this->roles = $roles;
	}

	/**
	 * Retrieve roles
	 *
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}

}