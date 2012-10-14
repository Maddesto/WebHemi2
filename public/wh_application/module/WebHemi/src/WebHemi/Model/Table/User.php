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

use WebHemi\Model\User as UserModel,
	Zend\Db\Exception,
	Zend\Db\TableGateway\AbstractTableGateway,
	Zend\Db\Adapter\Adapter,
	Zend\Db\ResultSet\ResultSet;

/**
 * WebHemi User Table
 *
 * @category   WebHemi
 * @package    WebHemi_Model_Table
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class User extends AbstractTableGateway
{
	/** @var string $table   The name of the database table */
	protected $table = 'webhemi_user';

	/**
	 * Constructor
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
	 * Get User by Id
	 *
	 * @param int $userId
	 * @return UserModel
	 */
	public function getUserById($userId)
	{
		$rowset    = $this->select(array('user_id' => (int)$userId));
		$userModel = $rowset->current();

		return $userModel;
	}

	/**
	 * Get User by Username
	 *
	 * @param string $username
	 * @return UserModel
	 */
	public function getUserByName($username)
	{
		$rowset    = $this->select(array('username' => $username));
		$userModel = $rowset->current();

		return $userModel;
	}
	/**
	 * Get User by Username
	 *
	 * @param string $email
	 * @return UserModel
	 */
	public function getUserByEmail($email)
	{
		$rowset    = $this->select(array('email' => $email));
		$userModel = $rowset->current();

		return $userModel;
	}


	/**
	 * Insert new user record
     *
     * @param  UserModel $userModel
	 * @return int
	 * @throws Exception\InvalidArgumentException
     */
    public function insert($userModel)
	{
		if (!$userModel instanceof UserModel) {
			throw new Exception\InvalidArgumentException('Given parameter is not a valid UserModel');
		}

		$userId = $userModel->getUserId();
		if (!empty($userId)
				&& $this->getUserById($userId)
		) {
			throw new \Exception('Record already exists!');
		}
		return parent::insert($userModel->toArray());
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

		return parent::update($userModel->toArray(), array('user_id' => $userModel->getUserId()));
	}

}
