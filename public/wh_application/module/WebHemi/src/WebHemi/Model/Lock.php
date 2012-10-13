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
 * WebHemi Lock Model
 *
 * @category   WebHemi
 * @package    WebHemi_Model
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Lock
{
	/** @var int       $lockId  */
	protected $lockId;
	/** @var string    $clientIp  */
	protected $clientIp;
	/** @var int       $tryings  */
	protected $tryings;
	/** @var DateTime  $timeLock  */
	protected $timeLock;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->lockId   = null;
		$this->clientIp = $_SERVER['REMOTE_ADDR'];
		$this->tryings  = 0;
		$this->timeLock = null;
	}

	/**
	 * Get lockId
	 *
	 * @return int
	 */
	public function getLockId()
	{
		return $this->lockId;
	}

	/**
	 * Get clientIp
	 *
	 * @return string
	 */
	public function getClientIp()
	{
		return $this->clientIp;
	}

	/**
	 * Set clientIp.
	 *
	 * @param $clientIp
	 * @return Lock
	 */
	public function setClienIp($clientIp)
	{
		$this->clientIp = $clientIp;
		return $this;
	}

	/**
	 * Get tryings
	 *
	 * @return int
	 */
	public function getTryings()
	{
		return $this->tryings;
	}

	/**
	 * Set tryings
	 *
	 * @param int $tryings
	 * @return Lock
	 */
	public function setTryings($tryings)
	{
		$this->tryings = (int)$tryings;
		return $this;
	}

	/**
	 * Get timeLock
	 *
	 * @return DateTime
	 */
	public function getTimeLock()
	{
		return $this->timeLock;
	}

	/**
	 * Set timeLock
	 *
	 * @param DateTime/string $timeLock
	 * @return Lock
	 */
	public function setTimeLock($timeLock = null)
	{
		if (is_null($timeLock) || $timeLock instanceof DateTime) {
			$this->timeLock = $timeLock;
		}
		else {
			$this->timeLock = new \DateTime($timeLock);
		}
		return $this;
	}

	/**
	 * Exchange array values into object properties
	 *
	 * @param array $data
	 */
	public function exchangeArray($data)
	{
		$this->lockId   = (isset($data['lock_id']))   ? (int)$data['lock_id'] : null;
		$this->clientIp = (isset($data['client_ip'])) ? $data['client_ip'] : null;
		$this->tryings  = (isset($data['tryings']))   ? (int)$data['tryings'] : null;
		$this->timeLock = (isset($data['time_lock'])) ? new \DateTime($data['time_lock']) : null;
	}

	/**
	 * Exchange object properties into array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'lock_id'   => $this->lockId,
			'client_ip' => $this->clientIp,
			'tryings'   => $this->tryings,
			'time_lock' => $this->timeLock instanceof \DateTime ? $this->timeLock->format('Y-m-d H:i:s') : null,
		);
	}
}
