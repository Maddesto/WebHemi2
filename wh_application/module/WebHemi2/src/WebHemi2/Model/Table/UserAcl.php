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

use WebHemi2\Model\UserAcl as UserAclModel;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception;
use Zend\Db\ResultSet\ResultSet;

/**
 * WebHemi2
 *
 * User Acl Table
 *
 * @category  WebHemi2
 * @package   WebHemi2_Model_Table
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class UserAcl extends AbstractTableGateway
{
    /** @var string $table   The name of the database table */
    protected $table = 'webhemi_user_acl';

    /**
     * Class constructor
     *
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new UserAclModel());
        $this->initialize();
    }

    /**
     * Get a specific ACL data for a user
     *
     * @param string $userId
     * @param string $application
     *
     * @return UserAclModel
     */
    public function getUserAcl($userId, $application)
    {
        $rowSet = $this->select(['user_id' => $userId, 'application' => $application]);
        if ($rowSet) {
            return $rowSet->current();
        }

        return false;
    }

    /**
     * Insert or Update user ACL record
     *
     * @param UserAclModel $userAclModel
     *
     * @return int
     *
     * @throws Exception\InvalidArgumentException
     */
    public function save($userAclModel)
    {
        if (!$userAclModel instanceof UserAclModel) {
            throw new Exception\InvalidArgumentException('Given parameter is not a valid UserAclModel');
        }

        $rowSet = $this->getUserAcl($userAclModel->getUserId(), $userAclModel->getApplication());

        if (!$rowSet instanceof UserAclModel) {
            return parent::insert($userAclModel->toArray());
        } else {
            return parent::update(
                $userAclModel->toArray(),
                [
                    'user_id'  => $userAclModel->getUserId(),
                    'application' => $userAclModel->getApplication()
                ]
            );
        }
    }
}
