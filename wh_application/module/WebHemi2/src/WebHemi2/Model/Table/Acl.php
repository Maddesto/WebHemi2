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
 * @package    WebHemi2_Model_Table
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2015, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Model\Table;

use WebHemi2\Model\Acl as AclModel;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception;
use Zend\Db\ResultSet\ResultSet;

/**
 * WebHemi2 Acl Table
 *
 * @category   WebHemi2
 * @package    WebHemi2_Model_Table
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2015, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Acl extends AbstractTableGateway
{
    /** @var string $table The name of the database table */
    protected $table = 'webhemi_acl';

    /**
     * Class constructor
     *
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new AclModel());
        $this->initialize();
    }

    /**
     * Retrieve AclModel by Id
     *
     * @param int $aclId
     *
     * @return AclModel
     */
    public function getAclById($aclId)
    {
        /** @var ResultSet $rowSet */
        $rowSet   = $this->select(array('acl_id' => (int)$aclId));
        /** @var AclModel $aclModel */
        $aclModel = $rowSet->current();

        return $aclModel;
    }

    /**
     * Retrieve AclModel by Resource
     *
     * @param string $resource
     *
     * @return AclModel
     */
    public function getAclByResource($resource)
    {
        /** @var ResultSet $rowSet */
        $rowSet    = $this->select(array('resource' => $resource));
        /** @var AclModel $aclModel */
        $aclModel = $rowSet->current();

        return $aclModel;
    }

    /**
     * Retrieve the ACL resource list
     *
     * @return array
     */
    public function getResources()
    {
        $aclList = $this->getAclList();

        return array_keys($aclList);
    }

    /**
     * Retrieve the ACL role list
     *
     * @return array
     */
    public function getRoles()
    {
        return AclModel::$availableRoles;
    }

    /**
     * Retrieve the ACL list
     *
     * @return AclModel[]
     */
    public function getAclList()
    {
        $aclList = array();

        $select = $this->sql->select();
        /** @var ResultSet $rowSet */
        $rowSet = $this->select($select);

        while ($aclModel = $rowSet->current()) {
            /** @var AclModel $aclModel */
            $aclList[$aclModel->getResource()] = $aclModel;
            $rowSet->next();
        }

        return $aclList;
    }
}
