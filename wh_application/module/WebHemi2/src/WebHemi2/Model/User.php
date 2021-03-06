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

use WebHemi2\Model\Acl as AclModel;
use WebHemi2\Model\UserMeta as UserMetaModel;
use WebHemi2\Model\UserAcl as UserAclModel;

/**
 * WebHemi2
 *
 * User Model
 *
 * @category  WebHemi2
 * @package   WebHemi2_Model
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 *
 * @method string getAvatar() Retrieve user meta avatar.
 * @method string setAvatar($string) Set user meta avatar.
 * @method string getHeadLine() Retrieve user meta headline.
 * @method string setHeadLine($string) Set user meta headline.
 * @method string setDisplayName($string) Set user meta display name.
 * @method string getDisplayEmail() Retrieve user meta display email.
 * @method string setDisplayEmail($string) Set user meta display email.
 * @method string getDetails() Retrieve user meta details.
 * @method string setDetails($string) Set user meta details.
 * @method string getPhoneNumber() Retrieve user meta phone number.
 * @method string setPhoneNumber($string) Set user meta phone number.
 * @method string getLocation() Retrieve user meta location.
 * @method string setLocation($string) Set user meta location.
 * @method string getInstantMessengers() Retrieve user meta instant messenger data (JSON).
 * @method string setInstantMessengers($string) Set user meta instant messenger data (JSON).
 * @method string getSocialNetworks() Retrieve user meta social network data (JSON).
 * @method string setSocialNetworks($string) Set user meta social network data (JSON).
 * @method string getWebsites() Retrieve user meta websites (JSON).
 * @method string setWebsites($string) Set user meta websites (JSON).
 */
class User extends \ArrayObject
{
    /* User avatar type: No avatar */
    const USER_AVATAR_TYPE_NONE = 0;
    /* User avatar type: GR Avatar */
    const USER_AVATAR_TYPE_GRAVATAR = 1;
    /* User avatar type: base64 encoded image file content */
    const USER_AVATAR_TYPE_BASE64 = 2;
    /* User avatar type: link */
    const USER_AVATAR_TYPE_URL = 4;

    /** @var int $userId */
    protected $userId;
    /** @var string $username */
    protected $username;
    /** @var string $email */
    protected $email;
    /** @var string $password */
    protected $password;
    /** @var string $hash */
    protected $hash;
    /** @var string $lastIp */
    protected $lastIp;
    /** @var string $registerIp */
    protected $registerIp;
    /** @var bool $isActive */
    protected $isActive;
    /** @var bool $isEnabled */
    protected $isEnabled;
    /** @var \DateTime $timeLogin */
    protected $timeLogin;
    /** @var \DateTime $timeRegister */
    protected $timeRegister;
    /** @var UserAclModel $userRole */
    protected $userRole;
    /** @var UserMetaModel[] $userMeta */
    protected $userMeta;

    /**
     * Set or Retrieve a user meta data
     *
     * @param string $name Method name.
     * @param array $arguments Method parameter list.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $data = [];

        // if getter or setter
        if (preg_match('/^(?<method>(get|set))(?<key>.*)$/', $name, $data)) {
            $key = lcfirst($data['key']);

            // getting meta data
            if ($data['method'] == 'get') {
                $meta = null;

                if (isset($this->userMeta[$key])
                    && $this->userMeta[$key] instanceof UserMetaModel
                ) {
                    $meta = $this->userMeta[$key]->getMeta();
                }
                return $meta;
            } else {
                // setting meta data
                $value = (string)current($arguments);

                if (!isset($this->userMeta[$key])
                    || !$this->userMeta[$key] instanceof UserMetaModel
                ) {
                    $this->userMeta[$key] = new UserMeta();
                }

                $this->userMeta[$key]->setUserId($this->userId);
                $this->userMeta[$key]->setMetaKey($key);
                $this->userMeta[$key]->setMeta($value);
            }
        }
        return null;
    }

    /**
     * Retrieve all user meta data.
     *
     * @return UserMetaModel[]
     */
    public function getUserMetaData()
    {
        return $this->userMeta;
    }

    /**
     * Set user meta data.
     *
     * @param UserMetaModel[] $userMeta
     *
     * @return User
     */
    public function setUserMetaData(Array $userMeta)
    {
        $this->userMeta = $userMeta;

        return $this;
    }

    /**
     * Retrieve user ID
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set user ID
     *
     * @param int $userId
     *
     * @return User
     */
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
        return $this;
    }

    /**
     * Retrieve username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Retrieve email address
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email address
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Retrieve displayname
     *
     * @return string
     */
    public function getDisplayName()
    {
        // if exists as meta data
        if (isset($this->userMeta['displayName'])
            && $this->userMeta['displayName'] instanceof UserMetaModel
        ) {
            $displayName = $this->userMeta['displayName']->getMeta();

            if (!empty($displayName)) {
                return $displayName;
            }
        }

        // otherwise username
        if (!empty($this->username)) {
            return $this->username;
        }

        // otherwise email address
        if (!empty($this->email)) {
            return $this->email;
        }

        // otherwise no name
        return 'Noname';
    }

    /**
     * Retrieves the type of the user avatar
     *
     * @return int
     */
    public function getAvatarType()
    {
        $avatar = $this->getAvatar();
        $content = null;

        if (strpos($avatar, 'data:image') === 0) {
            $matches = [];
            if (preg_match('/^data\:image\/(?:jpeg|gif|png);base64,(?P<content>.*)$/', $avatar, $matches)) {
                $content = base64_decode($matches['content']);

                if ($content) {
                    $content = imagecreatefromstring($content);

                    if ($content) {
                        imagedestroy($content);
                        unset($content);
                        return self::USER_AVATAR_TYPE_BASE64;
                    }
                }
            }
        } elseif (strpos($avatar, 'http:') === 0) {
            // if the avatar is an URL, then we check if it's an image.'
            $content = getimagesize($avatar);
            if ($content && in_array($content[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
                unset($content);
                return self::USER_AVATAR_TYPE_URL;
            }
        } elseif (!empty($avatar)) {
            return self::USER_AVATAR_TYPE_GRAVATAR;
        }

        unset($content);
        return self::USER_AVATAR_TYPE_NONE;
    }

    /**
     * Retrieve password (BCrypt)
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Retrieve hash (md5)
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return User
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Retrieve role
     *
     * @return string
     */
    public function getRole()
    {
        if (isset($this->userRole)
            && $this->userRole instanceof UserAclModel
        ) {
            return $this->userRole->getRole();
        }

        return AclModel::ROLE_GUEST;
    }

    /**
     * Set role
     *
     * @param UserAclModel $role
     *
     * @return User
     */
    public function setRole(UserAclModel $role)
    {
        $this->userRole = $role;
        return $this;
    }

    /**
     * Retrieve time of last login
     *
     * @return \DateTime timeLogin
     */
    public function getTimeLogin()
    {
        return $this->timeLogin;
    }

    /**
     * Set time of last login
     *
     * @param \DateTime|string $timeLogin
     *
     * @return User
     */
    public function setTimeLogin($timeLogin)
    {
        if ($timeLogin instanceof \DateTime) {
            $this->timeLogin = $timeLogin;
        } else {
            $this->timeLogin = new \DateTime($timeLogin);
        }
        return $this;
    }

    /**
     * Retrieve the IP address of the last login
     *
     * @return string
     */
    public function getLastIp()
    {
        return $this->lastIp;
    }

    /**
     * Set the IP address of the last login
     *
     * @param string $lastIp
     *
     * @return User
     */
    public function setLastIp($lastIp)
    {
        $this->lastIp = $lastIp;
        return $this;
    }

    /**
     * Retrieve time of registration
     *
     * @return \DateTime
     */
    public function getTimeRegister()
    {
        return $this->timeRegister;
    }

    /**
     * Set time of registration
     *
     * @param DateTime /string $timeRegister
     *
     * @return User
     */
    public function setTimeRegister($timeRegister)
    {
        if ($timeRegister instanceof \DateTime) {
            $this->timeRegister = $timeRegister;
        } else {
            $this->timeRegister = new \DateTime($timeRegister);
        }
        return $this;
    }

    /**
     * Retrieve the IP address of the registration
     *
     * @return string
     */
    public function getRegisterIp()
    {
        return $this->registerIp;
    }

    /**
     * Set the IP address of the registration
     *
     * @param string $registerIp The value to be set
     *
     * @return User
     */
    public function setRegisterIp($registerIp)
    {
        $this->registerIp = $registerIp;
        return $this;
    }

    /**
     * Retrieve the user active status
     *
     * @return bool
     */
    public function getActive()
    {
        return (bool)$this->isActive;
    }

    /**
     * Set user active status
     *
     * @param bool $active TRUE is active, FALSE otherwise
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->isActive = (bool)$active;
        return $this;
    }

    /**
     * Retrieve user enabled status
     *
     * @return bool
     */
    public function getEnabled()
    {
        return (bool)$this->isEnabled;
    }

    /**
     * Set user enabled status
     *
     * @param bool $enabled
     *
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->isEnabled = (bool)$enabled;
        return $this;
    }

    /**
     * Exchange array values into object properties.
     *
     * @param array $data
     *
     * @return array
     */
    public function exchangeArray($data)
    {
        $this->userId = (isset($data['user_id'])) ? (int)$data['user_id'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->hash = (isset($data['hash'])) ? $data['hash'] : null;
        $this->lastIp = (isset($data['last_ip'])) ? $data['last_ip'] : null;
        $this->registerIp = (isset($data['register_ip'])) ? $data['register_ip'] : null;
        $this->isActive = (isset($data['is_active'])) ? (bool)$data['is_active'] : null;
        $this->isEnabled = (isset($data['is_enabled'])) ? (bool)$data['is_enabled'] : null;
        $this->timeLogin = (isset($data['time_login'])) ? new \DateTime($data['time_login']) : null;
        $this->timeRegister = (isset($data['time_register'])) ? new \DateTime($data['time_register']) : null;

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
            'user_id' => !empty($this->userId) ? $this->userId : null,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'hash' => $this->hash,
            'last_ip' => $this->lastIp,
            'register_ip' => $this->registerIp,
            'is_active' => $this->isActive ? 1 : 0,
            'is_enabled' => $this->isEnabled ? 1 : 0,
            'time_login' => $this->timeLogin ? $this->timeLogin->format('Y-m-d H:i:s') : null,
            'time_register' => $this->timeRegister ? $this->timeRegister->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Exchange object properties into array for the ArraySerializable Hydrator
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $formArray = [
            'accountInfo' => [
                'user_id' => $this->userId,
                'username' => $this->username,
                'email' => $this->email,
                'role' => $this->getRole(),
            ],
            'personalInfo' => [
                'displayname' => $this->getDisplayName(),
                'headline' => $this->getHeadLine(),
                'displayemail' => $this->getDisplayEmail(),
                'details' => $this->getDetails(),
                'avatarInfo' => [
                    'avatarimage' => $this->getAvatar(),
                    'avatar' => $this->getAvatar(),
                    'avatartype' => $this->getAvatarType(),
                    'avatargrid' => (
                    self::USER_AVATAR_TYPE_GRAVATAR == $this->getAvatarType()
                        ? $this->getAvatar()
                        : ''
                    ),
                    'avatarurl' => (
                    self::USER_AVATAR_TYPE_URL == $this->getAvatarType()
                        ? $this->getAvatar()
                        : ''
                    ),
                ],
            ],
            'contactInfo' => [
                'phonenumber' => $this->getPhoneNumber(),
                'location' => $this->getLocation(),
                'instantmessengers' => $this->getInstantMessengers(),
                'socialnetworks' => $this->getSocialNetworks(),
                'websites' => $this->getWebsites(),
            ]
        ];
        return $formArray;
    }
}
