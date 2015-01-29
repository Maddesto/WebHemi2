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
 * @copyright  Copyright (c) 2015, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Model;

use DateTime;

/**
 * WebHemi2 Lock Model
 *
 * @category   WebHemi2
 * @package    WebHemi2_Model
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2015, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Lock extends \ArrayObject
{
    /** @var int $lockId */
    protected $lockId;
    /** @var string $clientIp */
    protected $clientIp;
    /** @var int $tryings */
    protected $tryings;
    /** @var DateTime $timeLock */
    protected $timeLock;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->lockId = null;
        $this->clientIp = $_SERVER['REMOTE_ADDR'];
        $this->tryings = 0;
        $this->timeLock = null;
    }

    /**
     * Retrieve the lock ID
     *
     * @return int
     */
    public function getLockId()
    {
        return $this->lockId;
    }

    /**
     * Retrieve client IP
     *
     * @return string
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * Set client IP
     *
     * @param string $clientIp
     *
     * @return Lock
     */
    public function setClienIp($clientIp)
    {
        $this->clientIp = $clientIp;
        return $this;
    }

    /**
     * Retrieve trying counter
     *
     * @return int
     */
    public function getTryings()
    {
        return $this->tryings;
    }

    /**
     * Set trying counter
     *
     * @param int $tryings
     *
     * @return Lock
     */
    public function setTryings($tryings)
    {
        $this->tryings = (int)$tryings;
        return $this;
    }

    /**
     * Retrieve timelock
     *
     * @return DateTime
     */
    public function getTimeLock()
    {
        return $this->timeLock;
    }

    /**
     * Set timelock
     *
     * @param DateTime|string $timeLock
     *
     * @return Lock
     */
    public function setTimeLock($timeLock = null)
    {
        if (is_null($timeLock) || $timeLock instanceof DateTime) {
            $this->timeLock = $timeLock;
        } else {
            $this->timeLock = new DateTime($timeLock);
        }
        return $this;
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
        $this->lockId = (isset($data['lock_id'])) ? (int)$data['lock_id'] : null;
        $this->clientIp = (isset($data['client_ip'])) ? $data['client_ip'] : null;
        $this->tryings = (isset($data['tryings'])) ? (int)$data['tryings'] : null;
        $this->timeLock = (isset($data['time_lock'])) ? new DateTime($data['time_lock']) : null;

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
            'lock_id' => $this->lockId,
            'client_ip' => $this->clientIp,
            'tryings' => $this->tryings,
            'time_lock' => $this->timeLock instanceof DateTime ? $this->timeLock->format('Y-m-d H:i:s') : null,
        );
    }
}
