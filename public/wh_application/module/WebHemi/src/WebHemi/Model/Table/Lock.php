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

	/** @staticvar LockModel|boolean $lockModel */
	static $lockModel;

	/** @var string $table   The name of the database table */
	protected $table = 'webhemi_lock';

	/**
	 * Class constructor
	 *
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new LockModel());
		$this->initialize();
	}

	/**
	 * Retrieve lock data for current IP. It creates a new record if none found
	 *
	 * @return LockModel
	 */
	public function getLock()
	{
		if (!isset(self::$lockModel)) {
			$rowset = $this->select(array('client_ip' => $_SERVER['REMOTE_ADDR']));
			$lockModel = $rowset->current();

			// if no record, we create one
			if (!$lockModel) {
				// instantiate the return object and save the new record
				$lockModel = new LockModel();
				// if we can't save
				if (!$this->insert($lockModel->toArray())) {
					$lockModel = false;
				}
			}

			self::$lockModel = $lockModel;
		}

		return self::$lockModel;
	}

	/**
	 * Set lock data for current IP
	 *
	 * @return int
	 */
	public function setLock()
	{
		$lockModel = $this->getLock();

		// only if the data is valid
		if ($lockModel instanceof LockModel) {
			// count the tryings
			$tryings = (int)$lockModel->getTryings() + 1;
			// set the new value
			$lockModel->setTryings($tryings);

			// if reached the maximum
			if ($tryings >= self::MAXTRYINGS) {
				$lockModel->setTimeLock(gmdate('Y-m-d H:i:s'));
			}
			return $this->update($lockModel->toArray(), array('lock_id' => $lockModel->getLockId()));
		}
		// on error
		return 0;
	}

	/**
	 * Release (reset) lock data for current IP
	 *
	 * @return int
	 */
	public function releaseLock()
	{
		$lockModel = $this->getLock();

		// only if the data is valid
		if ($lockModel instanceof LockModel) {
			$lockModel->setTryings(0)
					->setTimeLock();
			return $this->update($lockModel->toArray(), array('lock_id' => $lockModel->getLockId()));
		}
		// on error
		return 0;
	}

}
