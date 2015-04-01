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
 * @package   WebHemi2_Model
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Model;

/**
 * WebHemi2
 *
 * Acl Model
 *
 * @category  WebHemi2
 * @package   WebHemi2_Model
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Acl extends \ArrayObject
{
    /** Define role constants */
    const ROLE_ADMIN = 'admin';
    const ROLE_PUBLISHER = 'publisher';
    const ROLE_EDITOR = 'editor';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_MEMBER = 'member';
    const ROLE_GUEST = 'guest';

    /** @staticvar array $availableRoles */
    public static $availableRoles = [
        self::ROLE_ADMIN,
        self::ROLE_PUBLISHER,
        self::ROLE_EDITOR,
        self::ROLE_MODERATOR,
        self::ROLE_MEMBER,
        self::ROLE_GUEST,
    ];

    /** @var int $aclId */
    protected $aclId;
    /** @var string $resource */
    protected $resource;
    /** @var int $admin */
    protected $admin;
    /** @var int $publisher */
    protected $publisher;
    /** @var int $editor */
    protected $editor;
    /** @var int $moderator */
    protected $moderator;
    /** @var int $member */
    protected $member;
    /** @var int $guest */
    protected $guest;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->aclId = null;
    }

    /**
     * Retrieve the acl ID
     *
     * @return int
     */
    public function getAclId()
    {
        return $this->aclId;
    }

    /**
     * Retrieve resource data.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set resource data.
     *
     * @param string $resource
     *
     * @return Acl
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Retrieve the value of the given role.
     *
     * @param string $role
     *
     * @return int
     *
     * @throws \Exception
     */
    public function getRule($role)
    {
        if (in_array($role, static::$availableRoles)) {
            return $this->$role;
        }

        throw new \Exception('Role "' . $role . '" is not defined.');
    }

    /**
     * Set the value of the given role.
     *
     * @param string $role
     * @param int|bool $value
     *
     * @return Acl
     *
     * @throws \Exception
     */
    public function setRule($role, $value)
    {
        if (in_array($role, static::$availableRoles)) {
            $this->$role = (int)$value ? 1 : 0;
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
        $this->aclId = isset($data['acl_id']) ? (int)$data['acl_id'] : null;
        $this->resource = isset($data['resource']) ? $data['resource'] : null;
        $this->admin = !empty($data[static::ROLE_ADMIN]) ? 1 : 0;
        $this->publisher = !empty($data[static::ROLE_PUBLISHER]) ? 1 : 0;
        $this->editor = !empty($data[static::ROLE_EDITOR]) ? 1 : 0;
        $this->moderator = !empty($data[static::ROLE_MODERATOR]) ? 1 : 0;
        $this->member = !empty($data[static::ROLE_MEMBER]) ? 1 : 0;
        $this->guest = !empty($data[static::ROLE_GUEST]) ? 1 : 0;

        return $data;
    }

    /**
     * Exchange object properties into array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'acl_id' => $this->aclId,
            'resource' => $this->resource,
            static::ROLE_ADMIN => $this->admin,
            static::ROLE_PUBLISHER => $this->publisher,
            static::ROLE_EDITOR => $this->editor,
            static::ROLE_MODERATOR => $this->moderator,
            static::ROLE_MEMBER => $this->member,
            static::ROLE_GUEST => $this->guest,
        ];
    }
}
