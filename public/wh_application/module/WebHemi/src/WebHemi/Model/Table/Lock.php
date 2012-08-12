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
 * @package    WebHemi_Model_Table
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Model\Table;

use WebHemi\Model\Lock as LockModel,
	Zend\Db\TableGateway\AbstractTableGateway,
	Zend\Db\Adapter\Adapter,
	Zend\Db\ResultSet\ResultSet;

/**
 * WebHemi Lock Table
 *
 * @category   WebHemi
 * @package    WebHemi_Model_Table
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Lock extends AbstractTableGateway
{
	/** @var int The maximum number of access attempts */
	const MAXTRYINGS = 5;
	/** @var int The number of minutes the login is locked upon reaching the maximum number of access attempts */
	const LOCKTIME = 15;

	protected $table = 'webhemi_lock';

	/**
	 * Constructor
	 *
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 */
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new LockModel());
		$this->initialize();
	}

	/**
	 * Get lock data for current IP
	 * It creates a new record if not found
	 *
	 * @return \WebHemi\Model\Lock
	 */
	public function getLock()
	{
		$rowset = $this->select(array('client_ip' => $_SERVER['REMOTE_ADDR']));
		$lockModel = $rowset->current();

		// if no record, we create one
		if (!$lockModel) {
			// instantiate the return object
			$lockModel = new LockModel();
			$lockModel->lock_id   = null;
			$lockModel->client_ip = $_SERVER['REMOTE_ADDR'];
			$lockModel->tryings   = 0;
			$lockModel->lock_time = null;
			// save the new record
			$this->insert($lockModel->toArray());
		}
		return $lockModel;
	}

	/**
	 * Set lock data for current IP
	 */
	public function setLock()
	{
		$lockModel = $this->getLock();
		$lockModel->tryings = (int)$lockModel->tryings + 1;
		if ($lockModel->tryings >= self::MAXTRYINGS) {
			$lockModel->lock_time = gmdate('Y-m-d H:i:s');
		}
		$this->update($lockModel->toArray(), array('lock_id' => $lockModel->lock_id));
	}

	/**
	 * Release (reset) lock data for current IP
	 */
	public function releaseLock()
	{
		$lockModel = $this->getLock();
		$lockModel->tryings   = 0;
		$lockModel->lock_time = null;
		$this->update($lockModel->toArray(), array('lock_id' => $lockModel->lock_id));
	}

}
