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
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Model;

/**
 * WebHemi User Meta Model
 *
 * @category   WebHemi
 * @package    WebHemi_Model
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserMeta
{
	/** @var int  $userId */
	protected $userId;
	/** @var string $metaKey */
	protected $metaKey;
	/** @var string $meta */
	protected $meta;

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
	 * @return UserMeta
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
		return $this;
	}

	/**
	 * Retrieve meta key
	 *
	 * @return string
	 */
	public function getMetaKey()
	{
		return $this->metaKey;
	}

	/**
	 * Set meta key
	 *
	 * @param string $metaKey
	 * @return UserMeta
	 */
	public function setMetaKey($metaKey)
	{
		$this->metaKey = $metaKey;
		return $this;
	}

	/**
	 * Retrieve meta data
	 *
	 * @return mixed
	 */
	public function getMeta()
	{
		return $this->meta;
	}

	/**
	 * Set meta data
	 *
	 * @param mixed $meta
	 * @return UserMeta
	 */
	public function setMeta($meta)
	{
		$this->meta = $meta;
		return $this;
	}

	/**
	 * Exchange array values into object properties
	 *
	 * @param array $data
	 */
	public function exchangeArray(array $data)
	{
		$this->userId  = (isset($data['user_id']))  ? (int) $data['user_id'] : null;
		$this->metaKey = (isset($data['meta_key'])) ? $data['meta_key'] : null;
		$this->meta    = (isset($data['meta']))     ? $data['meta'] : null;
	}

	/**
	 * Exchange object properties into array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'user_id'  => $this->userId,
			'meta_key' => $this->metaKey,
			'meta'     => $this->meta,
		);
	}
}
