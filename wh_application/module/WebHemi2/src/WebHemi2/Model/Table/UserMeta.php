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

use WebHemi2\Model\UserMeta as UserMetaModel;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception;
use Zend\Db\ResultSet\ResultSet;

/**
 * WebHemi2
 *
 * User Meta Table
 *
 * @category  WebHemi2
 * @package   WebHemi2_Model_Table
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
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
        $rowSet        = $this->select(['user_id' => $userId, 'meta_key' => $metaKey]);
        $userMetaModel = $rowSet->current();

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
        $rowSet   = $this->select(['user_id' => $userId]);
        $userMeta = [];
        while ($metaModel = $rowSet->current()) {
            /** @var UserMetaModel $metaModel */
            $userMeta[$metaModel->getMetaKey()] = $metaModel;
            $rowSet->next();
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
        } else {
            return parent::update(
                $userMetaModel->toArray(),
                [
                    'user_id'  => $userMetaModel->getUserId(),
                    'meta_key' => $userMetaModel->getMetaKey()
                ]
            );
        }
    }
}
