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
	/** @var int    $lock_id  */
	public $lock_id;
	/** @var string $client_ip  */
	public $client_ip;
	/** @var int    $tryings  */
	public $tryings;
	/** @var date   $lock_time  */
	public $lock_time;

	/**
	 * Exchange array values into object properties
	 *
	 * @param array $data
	 */
	public function exchangeArray($data)
	{
		$this->lock_id   = (isset($data['lock_id']))   ? (int)$data['lock_id'] : null;
		$this->client_ip = (isset($data['client_ip'])) ? $data['client_ip'] : null;
		$this->tryings   = (isset($data['tryings']))   ? (int)$data['tryings'] : null;
		$this->lock_time = (isset($data['lock_time'])) ? $data['lock_time'] : null;
	}

	/**
	 * Exchange object properties into array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array(
			'lock_id'   => $this->lock_id,
			'client_ip' => $this->client_ip,
			'tryings'   => $this->tryings,
			'lock_time' => $this->lock_time
		);
	}

}
