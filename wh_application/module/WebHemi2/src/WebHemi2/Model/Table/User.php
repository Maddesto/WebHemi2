<?php

/**
 * WebHemi2
 *
 * PHP version 5.4
 *
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
 * @category  WebHemi2
 * @package   WebHemi2_Model_Table
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Model\Table;

use WebHemi2\Model\Acl as AclModel;
use WebHemi2\Model\User as UserModel;
use WebHemi2\Model\UserMeta as UserMetaModel;
use WebHemi2\Model\UserAcl as UserAclModel;
use WebHemi2\Model\Table\UserMeta as UserMetaTable;
use WebHemi2\Model\Table\UserAcl as UserAclTable;
use Zend\Db\Exception;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Driver\Pdo\Connection as PdoConnection;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

/**
 * WebHemi2
 *
 * User Table
 *
 * @category  WebHemi2
 * @package   WebHemi2_Model_Table
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class User extends AbstractTableGateway
{
    /** @var string $table The name of the database table */
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
     * @param string $application
     *
     * @return UserModel
     */
    public function getUserById($userId, $application = null)
    {
        $rowSet = $this->select(['user_id' => (int)$userId]);
        /** @var UserModel $userModel */
        $userModel = $rowSet->current();

        if ($userModel) {
            $this->initUser($userModel, $application);
        }

        return $userModel;
    }

    /**
     * Retrieve UserModel by Username
     *
     * @param string $username
     * @param string $application
     *
     * @return UserModel
     */
    public function getUserByName($username, $application = null)
    {
        /** @var ResultSet $rowSet */
        $rowSet = $this->select(['username' => $username]);
        /** @var UserModel $userModel */
        $userModel = $rowSet->current();

        if ($userModel) {
            $this->initUser($userModel, $application);
        }

        return $userModel;
    }

    /**
     * Retrieve UserModel by Username
     *
     * @param string $email
     * @param string $application
     *
     * @return UserModel
     */
    public function getUserByEmail($email, $application = null)
    {
        /** @var ResultSet $rowSet */
        $rowSet = $this->select(['email' => $email]);
        /** @var UserModel $userModel */
        $userModel = $rowSet->current();

        if ($userModel) {
            $this->initUser($userModel, $application);
        }

        return $userModel;
    }

    /**
     * Retrieve UserModel by Hash
     *
     * @param string $hash
     * @param string $application
     *
     * @return UserModel
     */
    public function getUserByHash($hash, $application = null)
    {
        /** @var ResultSet $rowSet */
        $rowSet = $this->select(['hash' => $hash]);
        /** @var UserModel $userModel */
        $userModel = $rowSet->current();

        if ($userModel) {
            $this->initUser($userModel, $application);
        }

        return $userModel;
    }

    /**
     * Init UserModel: set attached data
     *
     * @param UserModel &$userModel
     * @param string $application
     *
     * @return UserModel
     */
    public function initUser(UserModel &$userModel, $application = null)
    {
        if (!$userModel instanceof UserModel) {
            throw new Exception\InvalidArgumentException('Given parameter is not a valid UserModel');
        }

        if (empty($application)) {
            $application = APPLICATION_MODULE;
        }

        $this->loadUserRole($userModel, $application);
        $this->loadUserMeta($userModel);
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
        $users = [];

        $select = $this->sql->select();

        if (!empty($offset) && !empty($limit)) {
            $select->offset($offset)
                ->limit($limit);
        }

        /** @var ResultSet $rowSet */
        $rowSet = $this->selectWith($select);

        do {
            /** @var UserModel $userModel */
            $userModel = $rowSet->current();
            if ($userModel) {
                $this->loadUserMeta($userModel);
                $index = $userModel->getDisplayName();
                $users[$index] = $userModel;
                $rowSet->next();
            }
        } while ($userModel);
        ksort($users);
        return $users;
    }

    /**
     * Load user role into user model.
     *
     * @param UserModel &$userModel
     * @param string $application
     */
    protected function loadUserRole(UserModel &$userModel, $application)
    {
        $userAclTable = new UserAclTable($this->adapter);
        $userAclModel = $userAclTable->getUserAcl($userModel->getUserId(), $application);

        if ($userAclModel) {
            $userModel->setRole($userAclModel);
        } else {
            $guestAclModel = new UserAclModel();
            $guestAclModel->setUserId($userModel->getUserId());
            $guestAclModel->setApplication($application);
            $guestAclModel->setRole(AclModel::ROLE_GUEST);

            $userModel->setRole($guestAclModel);
        }
    }

    /**
     * Save user role into user model.
     *
     * @param string $role
     * @param int $userId
     * @param string $application
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return bool|int
     */
    protected function saveUserRole($role, $userId, $application)
    {
        $userAclTable = new UserAclTable($this->adapter);
        $userAclModel = $userAclTable->getUserAcl($userId, $application);

        if ($userAclModel instanceof UserAclModel) {
            $userAclModel->setRole($role);
        } else {
            $userAclModel = new UserAclModel();
            $userAclModel->setUserId($userId)
                ->setApplication($application)
                ->setRole($role);
        }

        return $userAclTable->save($userAclModel);
    }

    /**
     * Load user meta into user model.
     *
     * @param UserModel &$userModel
     */
    protected function loadUserMeta(UserModel &$userModel)
    {
        $userMetaTable = new UserMetaTable($this->adapter);
        $userMeta = $userMetaTable->getUserMetaAll($userModel->getUserId());
        $userModel->setUserMetaData($userMeta);
    }

    /**
     * Save all user meta data from the user model
     *
     * @param array $userMeta
     * @param int $userId
     *
     * @throws Exception\InvalidArgumentException
     *
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
            } else {
                $affectedRows += $metaResult;
            }
        }

        return !$result ? false : $affectedRows;
    }

    /**
     * Insert new user record
     *
     * @param  UserModel $userModel
     *
     * @return int
     *
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
            $userId = $this->lastInsertValue;
            $userMeta = $userModel->getUserMetaData();
            // @TODO: save user role
            try {
                $metaResult = $this->saveUserMeta($userMeta, $userId);
                if ($metaResult === false) {
                    throw new Exception\UnexpectedValueException('Cannot save User Meta in ' . __METHOD__);
                }
                // if everything is correct, we apply the changes
                $connection->commit();
            } catch (\Exception $ex) {
                // on failure, we rollback the whole transaction
                $connection->rollback();
                $result = false;
            }
        } else {
            $connection->rollback();
        }

        return $result;
    }

    /**
     * Update user record
     *
     * @param UserModel $userModel
     * @param  string|array|\Closure $where
     *
     * @return int
     *
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


        $result = parent::update($userModel->toArray(), ['user_id' => $userModel->getUserId()]);
        // if the update was successful, we may go on
        if ($result !== false) {
            $userMeta = $userModel->getUserMetaData();
            // @TODO: save user role
            try {
                $metaResult = $this->saveUserMeta($userMeta, $userModel->getUserId());
                if ($metaResult === false) {
                    throw new Exception\UnexpectedValueException('Cannot save User Meta in ' . __METHOD__);
                }
                // if everything is correct, we apply the changes
                $connection->commit();
            } catch (\Exception $ex) {
                // on failure, we rollback the whole transaction
                $connection->rollback();
                $result = false;
            }
        } else {
            $connection->rollback();
        }
        return $result;
    }
}
