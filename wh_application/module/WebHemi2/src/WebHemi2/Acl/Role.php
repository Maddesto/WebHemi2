<?php

/**
 * WebHemi2
 *
 * PHP version 5.4
 *
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
 * @category  WebHemi2
 * @package   WebHemi2_Acl
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Acl;

use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * WebHemi2
 *
 * Role
 *
 * @category  WebHemi2
 * @package   WebHemi2_Acl
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Role implements RoleInterface
{
    /** @var string $roleId */
    protected $roleId;
    /** @var RoleInterface $parentRole */
    protected $parentRole;

    /**
     * Class constructor
     * If no roleId is given, then it can be set with $role->setRoleId($roleId);
     *
     * @param string $roleId
     * @param string|RoleInterface $parentRole
     */
    public function __construct($roleId = null, $parentRole = null)
    {
        // if only the name of the Parent is given we Instantiate it
        if (isset($parentRole) && !($parentRole instanceof RoleInterface)) {
            $parentRole = new Role($parentRole);
        }

        $this->roleId = $roleId;
        $this->parentRole = $parentRole;
    }

    /**
     * Retrieve roleId
     *
     * @return string
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set roleId
     *
     * @param string $roleId
     *
     * @return Role
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
        return $this;
    }

    /**
     * Retrieve parentRole
     *
     * @return RoleInterface
     */
    public function getParentRole()
    {
        return $this->parentRole;
    }

    /**
     * Set parentRole
     *
     * @param RoleInterface $parentRole
     *
     * @return Role
     */
    public function setParentRole(RoleInterface $parentRole)
    {
        $this->parentRole = $parentRole;
        return $this;
    }
}
