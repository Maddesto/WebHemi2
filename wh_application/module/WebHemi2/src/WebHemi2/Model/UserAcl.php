<?php

/**
 * WebHemi2
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
 * @category   WebHemi2
 * @package    WebHemi2_Model
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Model;

use WebHemi2\Model\Acl as AclModel;

/**
 * WebHemi2 User Acl Model
 *
 * @category   WebHemi2
 * @package    WebHemi2_Model
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserAcl extends \ArrayObject
{
    /** @var int $userId */
    protected $userId;
    /** @var string $application */
    protected $application;
    /** @var string $role */
    protected $role;

    /**
     * Retrieve UserId
     *
     * @return User
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set UserId
     *
     * @param int $userId
     *
     * @return UserAcl
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Retrieve application
     *
     * @return string
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set application
     *
     * @param string $application
     *
     * @return UserAcl
     */
    public function setApplication($application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * Retrieve role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param mixed $role
     *
     * @return UserAcl
     *
     * @throws \Exception
     */
    public function setRole($role)
    {
        if (in_array($role, AclModel::$availableRoles)) {
            $this->role = $role;
            return $this;
        }

        throw new \Exception('Role "' . $role . '" is not defined.');
    }

    /**
     * Exchange array values into object properties
     *
     * @param array $data
     *
     * @return array
     */
    public function exchangeArray($data)
    {
        $this->userId = (isset($data['user_id'])) ? (int)$data['user_id'] : null;
        $this->application = (isset($data['application'])) ? $data['application'] : null;
        $this->role = (isset($data['role'])) ? $data['role'] : null;

        return $data;
    }

    /**
     * Exchange object properties into array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'user_id' => $this->userId,
            'application' => $this->application,
            'role' => $this->role,
        );
    }
}
