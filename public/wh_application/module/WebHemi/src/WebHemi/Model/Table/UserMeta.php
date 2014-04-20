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

use WebHemi\Model\UserMeta as UserMetaModel,
	Zend\Db\TableGateway\AbstractTableGateway,
	Zend\Db\Adapter\Adapter,
	Zend\Db\ResultSet\ResultSet;

/**
 * WebHemi User Meta Table
 *
 * @category   WebHemi
 * @package    WebHemi_Model_Table
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class UserMeta extends AbstractTableGateway
{
	/** @var string $table   The name of the database table */
	protected $table = 'webhemi_user_meta';

	/**
	 * Class constructor
	 *
	 * @param Adapter $adapter
	 */
	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new UserMetaModel());
		$this->initialize();
	}

	/**
	 * Get a specific meta data for a user
	 *
	 * @param string $userId
	 * @param string $metaKey
	 * @return UserMetaModel
	 */
	public function getUserMeta($userId, $metaKey)
	{
		$rowset        = $this->select(array('user_id' => $userId, 'meta_key' => $metaKey));
		$userMetaModel = $rowset->current();

		return $userMetaModel;
	}

	/**
	 * Get all meta data for a user
	 *
	 * @param string $userId

	 * @return array
	 */
	public function getUserMetaAll($userId)
	{
		$rowset   = $this->select(array('user_id' => $userId));
		$userMeta = array();
		while ($metaModel = $rowset->current()) {
			$userMeta[$metaModel->getMetaKey()] = $metaModel;
			$rowset->next();
		}

		return $userMeta;
	}

	/**
	 * Update user meta record
	 *
	 * @param UserMetaModel $userMetaModel
	 *
	 * @return int
	 * @throws Exception\InvalidArgumentException
	 */
	public function save($userMetaModel)
	{
		if (!$userMetaModel instanceof UserMetaModel) {
			throw new Exception\InvalidArgumentException('Given parameter is not a valid UserMetaModel');
		}

		$rowSet = $this->getUserMeta($userMetaModel->getUserId(), $userMetaModel->getMetaKey());

		if (!$rowSet instanceof UserMetaModel) {
			return parent::insert($userMetaModel->toArray());
		}
		else {
			return parent::update(
				$userMetaModel->toArray(), array(
					'user_id'  => $userMetaModel->getUserId(),
					'meta_key' => $userMetaModel->getMetaKey()
				)
			);
		}
	}
}
