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
 * @package    WebHemi_Model
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Model;

/**
 * WebHemi User Model
 *
 * @category   WebHemi
 * @package    WebHemi_Model
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class User
{
	/** @var int      $userId */
	protected $userId;
	/** @var string   $username */
	protected $username;
	/** @var string   $email */
	protected $email;
	/** @var string   $displayName */
	protected $displayName;
	/** @var string   $password */
	protected $password;
	/** @var string   $role */
	protected $role;
	/** @var string   $lastIp */
	protected $lastIp;
	/** @var string   $registerIp */
	protected $registerIp;
	/** @var bool     $isActive */
	protected $isActive;
	/** @var bool     $isEnabled */
	protected $isEnabled;
	/** @var DateTime $timeLogin */
	protected $timeLogin;
	/** @var DateTime $timeRegister */
	protected $timeRegister;

	/**
	 * Get userId
	 *
	 * @return int
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * Set userId
	 *
	 * @param int $userId
	 * @return User
	 */
	public function setUserId($userId)
	{
		$this->userId = (int)$userId;
		return $this;
	}

	/**
	 * Get username
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
	 * @return User
	 */
	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 * @return User
	 */
	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	/**
	 * Get displayName
	 *
	 * @return string
	 */
	public function getDisplayName()
	{
		if ($this->displayName !== null) {
			return $this->displayName;
		}
		elseif ($this->username !== null) {
			return $this->username;
		}
		elseif ($this->email !== null) {
			return $this->email;
		}
		return null;
	}

	/**
	 * Set displayName
	 *
	 * @param string $displayName
	 * @return User
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
		return $this;
	}

	/**
	 * Get password
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
	 * @return User
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * Get role
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
	 * @param string $role
	 * @return User
	 */
	public function setRole($role)
	{
		$this->role = $role;
		return $this;
	}

	/**
	 * Get timeLogin
	 *
	 * @return DateTime timeLogin
	 */
	public function getTimeLogin()
	{
		return $this->timeLogin;
	}

	/**
	 * Set timeLogin
	 *
	 * @param DateTime/string $timeLogin
	 * @return User
	 */
	public function setTimeLogin($timeLogin)
	{
		if ($timeLogin instanceof DateTime) {
			$this->timeLogin = $timeLogin;
		}
		else {
			$this->timeLogin = new DateTime($timeLogin);
		}
		return $this;
	}

	/**
	 * Get lastIp
	 *
	 * @return string
	 */
	public function getLastIp()
	{
		return $this->lastIp;
	}

	/**
	 * Set lastIp.
	 *
	 * @param $lastIp
	 * @return User
	 */
	public function setLastIp($lastIp)
	{
		$this->lastIp = $lastIp;
		return $this;
	}

	/**
	 * Get timeRegister
	 *
	 * @return DateTime
	 */
	public function getTimeRegister()
	{
		return $this->timeRegister;
	}

	/**
	 * Set timeRegister
	 *
	 * @param DateTime/string $timeRegister
	 * @return User
	 */
	public function setTimeRegister($timeRegister)
	{
		if ($timeRegister instanceof DateTime) {
			$this->timeRegister = $timeRegister;
		}
		else {
			$this->timeRegister = new DateTime($timeRegister);
		}
		return $this;
	}

	/**
	 * Get registerIp
	 *
	 * @return string
	 */
	public function getRegisterIp()
	{
		return $this->registerIp;
	}

	/**
	 * Set registerIp
	 *
	 * @param $registerIp the value to be set
	 * @return User
	 */
	public function setRegisterIp($registerIp)
	{
		$this->registerIp = $registerIp;
		return $this;
	}

	/**
	 * Get active
	 *
	 * @return bool
	 */
	public function getActive()
	{
		return $this->isActive;
	}

	/**
	 * Set active
	 *
	 * @param bool $active
	 * @return User
	 */
	public function setActive($active)
	{
		$this->isActive = (bool)$active;
		return $this;
	}

	/**
	 * Get enabled
	 *
	 * @return bool
	 */
	public function getEnabled()
	{
		return $this->isEnabled;
	}

	/**
	 * Set enabled
	 *
	 * @param bool $enabled
	 * @return User
	 */
	public function setEnabled($enabled)
	{
		$this->isEnabled = (bool)$enabled;
		return $this;
	}

	/**
	 * Exchange array values into object properties
	 *
	 * @param array $data
	 */
	public function exchangeArray($data)
	{
		$this->userId       = (isset($data['user_id']))       ? (int) $data['user_id'] : null;
		$this->username     = (isset($data['username']))      ? $data['username'] : null;
		$this->email        = (isset($data['email']))         ? $data['email'] : null;
		$this->displayname  = (isset($data['displayname']))   ? $data['displayname'] : null;
		$this->password     = (isset($data['password']))      ? $data['password'] : null;
		$this->role         = (isset($data['role']))          ? $data['role'] : null;
		$this->lastIp       = (isset($data['last_ip']))       ? $data['last_ip'] : null;
		$this->registerIp   = (isset($data['register_ip']))   ? $data['register_ip'] : null;
		$this->isActive     = (isset($data['is_active']))     ? (bool) $data['is_active'] : null;
		$this->isEnabled    = (isset($data['is_enabled']))    ? (bool) $data['is_enabled'] : null;
		$this->timeLogin    = (isset($data['time_login']))    ? new DateTime($data['time_login']) : null;
		$this->timeRegister = (isset($data['time_register'])) ? new DateTime($data['time_register']) : null;
	}

	/**
	 * Exchange object properties into array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'user_id'       => $this->userId,
			'username'      => $this->username,
			'email'         => $this->email,
			'displayname'   => $this->displayName,
			'password'      => $this->password,
			'role'          => $this->role,
			'last_ip'       => $this->lastIp,
			'register_ip'   => $this->registerIp,
			'is_active'     => $this->isActive ? 1 : 0,
			'is_enabled'    => $this->isEnabled ? 1 : 0,
			'time_login'    => $this->timeLogin->format('Y-m-d H:i:s'),
			'time_register' => $this->timeRegister->format('Y-m-d H:i:s')
		);
	}

}
