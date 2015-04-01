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
 * @package   WebHemi2_Acl_Provider
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Acl\Provider;

use WebHemi2\Acl\Role;

/**
 * WebHemi2
 *
 * Role Container and Provider
 *
 * @category  WebHemi2
 * @package   WebHemi2_Acl_Provider
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class RoleProvider
{
    /** @var array $roles */
    protected $roles = [];

    /**
     * Class constructor
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        /** @var Role[] $roles */
        $roles = [];

        foreach ($config as $roleName) {
            // if it is a new role, we set it
            if (!isset($roles[$roleName])) {
                $roles[$roleName] = new Role($roleName);
            }
        }

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
