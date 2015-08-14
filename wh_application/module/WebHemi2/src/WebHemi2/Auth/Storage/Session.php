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
 * @package   WebHemi2_Auth_Storage
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Auth\Storage;

use Zend\Session\Container as SessionContainer;
use Zend\Session\ManagerInterface as SessionManage;
use Zend\ServiceManager;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\Adapter;
use WebHemi2\Model\Table\User as UserTable;

/**
 * WebHemi2
 *
 * Authentication Database Storage
 *
 * @category  WebHemi2
 * @package   WebHemi2_Auth_Storage
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Session implements StorageInterface, ServiceManager\ServiceLocatorAwareInterface
{
    /** Default session namespace */
    const NAMESPACE_DEFAULT = 'Zend_Auth';
    /** Default session object member name */
    const MEMBER_DEFAULT = 'storage';
    /** Default session id salt */
    const SESSION_SALT_DEFAULT = 'WebHemi 2';

    /** @var UserTable $userTable */
    protected $userTable;
    /** @var mixed $resolvedIdentity */
    protected $resolvedIdentity;
    /** @var ServiceManager\ServiceLocatorAwareInterface $serviceLocator */
    protected $serviceLocator;
    /** @var SessionContainer $session */
    protected $session;
    /**@var mixed $namespace */
    protected $namespace = self::NAMESPACE_DEFAULT;
    /**@var mixed $member */
    protected $member = self::MEMBER_DEFAULT;

    /**
     * Sets session storage options and initializes session namespace object
     *
     * @param  mixed $namespace
     * @param  mixed $member
     * @param  SessionManager $manager
     */
    public function __construct($namespace = null, $member = null, SessionManager $manager = null)
    {
        if ($namespace !== null) {
            $this->namespace = $namespace;
        }
        if ($member !== null) {
            $this->member = $member;
        }

        $this->secureConfigSession();

        $this->session = new SessionContainer($this->namespace, $manager);
    }

    /**
     * Overwrite PHP settings to be more secure
     */
    protected function secureConfigSession()
    {
        ini_set('session.entropy_file', '/dev/urandom');
        ini_set('session.entropy_length', '16');
        ini_set('session.hash_function', 'sha256');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_httponly', '1');

        // hide session name
        session_name(SESSION_COOKIE_PREFIX . '-' . bin2hex(self::SESSION_SALT_DEFAULT));
        // set session lifetime to 1 hour
        session_set_cookie_params(3600);
    }

    /**
     * Regenerate Storage Session Id
     */
    public function regenerateStorageId()
    {
        $this->session->getManager()->regenerateId();
    }

    /**
     * Check whether the storage is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return !isset($this->session->{$this->member});
    }

    /**
     * Retrieve the contents of storage
     *
     * @return mixed
     */
    public function read()
    {
        if (null !== $this->resolvedIdentity) {
            return $this->resolvedIdentity;
        }

        $identity = $this->session->{$this->member};

        if (is_int($identity) || is_scalar($identity)) {
            $identity = $this->getTable()->getUserById($identity);
        }

        if ($identity) {
            $this->resolvedIdentity = $identity;
        } else {
            $this->resolvedIdentity = null;
        }

        return $this->resolvedIdentity;
    }

    /**
     * Write contents to storage
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        $this->resolvedIdentity = null;
        $this->session->{$this->member} = $contents;
    }

    /**
     * Clear contents from storage
     *
     * @return void
     */
    public function clear()
    {
        $this->resolvedIdentity = null;
        unset($this->session->{$this->member});
    }

    /**
     * Retrieve User Table instance
     *
     * @return UserTable
     */
    public function getTable()
    {
        /** @var Adapter $adapter */
        $adapter = $this->getServiceLocator()->get('database');

        if (!isset($this->userTable)) {
            $this->userTable = new UserTable($adapter);
        }
        return $this->userTable;
    }

    /**
     * Set User Table instance
     *
     * @param UserTable $userTable
     *
     * @return Db
     */
    public function setTable(UserTable $userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return Db
     */
    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
