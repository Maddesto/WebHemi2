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
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\Model\Table;

use WebHemi\Model\User as UserModel,
	WebHemi\Model\UserMeta as UserMetaModel,
	WebHemi\Model\Table\UserMeta as UserMetaTable,
	Zend\Db\Exception,
	Zend\Db\TableGateway\AbstractTableGateway,
	Zend\Db\Adapter\Driver\Pdo\Connection as PdoConnection,
	Zend\Db\Adapter\Adapter,
	Zend\Db\ResultSet\ResultSet;

/**
 * WebHemi User Table
 *
 * @category   WebHemi
 * @package    WebHemi_Model_Table
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class User extends AbstractTableGateway
{
	/** @var string $table   The name of the database table */
	protected $table = 'webhemi_user';

	/**
	 * Class constructor
	 *
	 * @param \Zend\Db\Adapter\Adapter $adapter
	 */
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new UserModel());
		$this->initialize();
	}

	/**
	 * Retrieve UserModel by Id
	 *
	 * @param int $userId
	 * @return UserModel
	 */
	public function getUserById($userId)
	{
		$rowset    = $this->select(array('user_id' => (int)$userId));
		$userModel = $rowset->current();
		if ($userModel) {
			$this->loadUserMeta($userModel);
		}

		return $userModel;
	}

	/**
	 * Retrieve UserModel by Username
	 *
	 * @param string $username
	 * @return UserModel
	 */
	public function getUserByName($username)
	{
		$rowset    = $this->select(array('username' => $username));
		$userModel = $rowset->current();
		if ($userModel) {
			$this->loadUserMeta($userModel);
		}

		return $userModel;
	}

	/**
	 * Retrieve UserModel by Username
	 *
	 * @param string $email
	 * @return UserModel
	 */
	public function getUserByEmail($email)
	{
		$rowset    = $this->select(array('email' => $email));
		$userModel = $rowset->current();
		if ($userModel) {
			$this->loadUserMeta($userModel);
		}

		return $userModel;
	}

	/**
	 * Retrieve UserModel by Hash
	 *
	 * @param int $userId
	 * @return UserModel
	 */
	public function getUserByHash($hash)
	{
		$rowset    = $this->select(array('hash' => $hash));
		$userModel = $rowset->current();
		if ($userModel) {
			$this->loadUserMeta($userModel);
		}

		return $userModel;
	}

	/**
	 * Retrieve the user list
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getUserList($offset = null, $limit = null)
	{
		$users = array();

		$select = $this->sql->select();

		if (!empty($offset) && !empty($limit)) {
			$select->offset($offset)
				->limit($limit);
		}

		$rowset = $this->selectWith($select);

		while($userModel = $rowset->current()) {
			$this->loadUserMeta($userModel);
			$index = $userModel->getDisplayName();
			$users[$index] = $userModel;
			$rowset->next();
		}
		ksort($users);
		return $users;
	}

	/**
	 *	Load user meta into user model.
	 *
	 * @param UserModel $userModel
	 */
	protected function loadUserMeta(UserModel &$userModel)
	{
		$userMetaTable  = new UserMetaTable($this->adapter);
		$userMeta       = $userMetaTable->getUserMetaAll($userModel->getUserId());
		$userModel->setUserMetaData($userMeta);
	}

	/**
	 * Save all user meta data from the user model
	 *
	 * @param array $userMeta
	 * @param int   $userId
	 * @throws Exception\InvalidArgumentException
	 * @return bool|int
	 */
	protected function saveUserMeta(array $userMeta, $userId)
	{
		$result = true;
		$affectedRows = 0;

		foreach ($userMeta as $userMetaModel) {
			if (!$userMetaModel instanceof UserMetaModel) {
				throw new Exception\InvalidArgumentException('Given parameter is not a valid UserMetaModel');
			}

			// If the User is inserted before this, then the user meta got no userId by this time
			if (!$userMetaModel->getUserId()) {
				$userMetaModel->setUserId($userId);
			}
			$metaTable = new UserMetaTable($this->adapter);
			$metaResult = $metaTable->save($userMetaModel);
			if ($metaResult === false) {
				$result = false;
			}
			else {
				$affectedRows += $metaResult;
			}
		}

		return !$result ? false : $affectedRows;
	}

	/**
	 * Insert new user record
	 *
	 * @param  UserModel $userModel
	 * @return int
	 * @throws Exception\InvalidArgumentException
	 * @throws Exception\UnexpectedValueException
	 */
	public function insert($userModel)
	{
		if (!$userModel instanceof UserModel) {
			throw new Exception\InvalidArgumentException('Given parameter is not a valid UserModel');
		}

		$userId = $userModel->getUserId();
		if (!empty($userId) && $this->getUserById($userId)) {
			throw new Exception\UnexpectedValueException('Record already exists!');
		}

		// the DB connection
		/* @var $connection PdoConnection */
		$connection = $this->getAdapter()->getDriver()->getConnection();

		// start the transaction
		$connection->beginTransaction();

		$result = parent::insert($userModel->toArray());

		// if insertion was succesful, we may go on
		if ($result !== false) {
			$userId      = $this->lastInsertValue;
			$userMeta    = $userModel->getUserMetaData();
			try {
				$metaResult = $this->saveUserMeta($userMeta, $userId);
				if ($metaResult === false) {
					throw new Exception\UnexpectedValueException('Cannot save User Meta in ' . __METHOD__);
				}
				// if everything is correct, we apply the changes
				$connection->commit();
			}
			catch (Exception $ex) {
				// on failure, we rollback the whole transaction
				$connection->rollback();
				$result = false;
			}
		}
		else {
			$connection->rollback();
		}

		return $result;
	}

	/**
	 * Update user record
	 *
	 * @param UserModel $userModel
	 * @return int
	 * @throws Exception\InvalidArgumentException
	 */
	public function update($userModel, $where = null)
	{
		if (!$userModel instanceof UserModel) {
			throw new Exception\InvalidArgumentException('Given parameter is not a valid UserModel');
		}

		// the DB connection
		/* @var $connection PdoConnection */
		$connection = $this->getAdapter()->getDriver()->getConnection();

		// start the transaction
		$connection->beginTransaction();
		$result = parent::update($userModel->toArray(), array('user_id' => $userModel->getUserId()));
		// if the update was successful, we may go on
		if ($result !== false) {
			$userMeta = $userModel->getUserMetaData();
			try {
				$metaResult = $this->saveUserMeta($userMeta, $userModel->getUserId());
				if ($metaResult === false) {
					throw new Exception\UnexpectedValueException('Cannot save User Meta in ' . __METHOD__);
				}
				// if everything is correct, we apply the changes
				$connection->commit();
			}
			catch (Exception $ex) {
				// on failure, we rollback the whole transaction
				$connection->rollback();
				$result = false;
			}
		}
		else {
			$connection->rollback();
		}
		return $result;
	}
}
