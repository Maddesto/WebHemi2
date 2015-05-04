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
 * @package   WebHemi2
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
namespace WebHemi2;

use Exception;

/**
 * A super-primitive installer class
 *
 * @category  WebHemi2
 * @package   WebHemi2
 */
class Installer
{
    /** Unique installer key hash */
    const INSTALL_PRIVATE_KEY = '63982e54a7aeb0d89910475ba6dbd3ca6dd4e5a1';

    /** @var Session $session */
    private $session;

    /** @var  Request $request */
    private $request;

    /** @var  Response $response */
    private $response;

    /**
     * Class constructor
     *
     * @param Session  $session
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(Session $session, Request $request, Response $response)
    {
        $this->session = $session;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        // Write the changes back to the session
        $this->session->write();
    }

    /**
     * Handle requests and route to actions
     */
    public function handleRequest()
    {
        if (!$this->request->isAjax()) {
            $this->actionWelcomePage();
        } else {
            $action = $this->request->action;
            $this->response->setAjax();
            if ($this->request->isPost() && $action == 'authenticate') {
                $this->actionVerifyuser();
            } elseif ($this->session->isAuthenticated()) {
                $action = 'action' . ucfirst($action);

                if (!method_exists($this, $action)) {
                    $this->response->render400();
                }

                $this->$action();

            } else {
                $this->response->render400();
            }
        }
    }

    /**
     * Verify credentials
     */
    public function actionVerifyuser()
    {
        $result = array(
            'success' => true,
        );
        $postData = $this->request->getPost();

        // If so, we do a super-primitive authentication
        if (isset($postData['user_private_key'])) {
            $encodedKey = sha1(md5($postData['user_private_key']));
            $isError = !$this->session->isAuthenticated($encodedKey == self::INSTALL_PRIVATE_KEY);

            if ($isError) {
                $result = array(
                    'success' => false,
                    'error' => 'The given credential is invalid.'
                );
            }
        } else {
            $this->response->render400();
        }

        $this->response->render200($result);
    }

    /**
     * Render the welcome page with the Private Key input form
     */
    public function actionWelcomePage()
    {
        if ($this->request->isAjax()) {
            $this->response->render400();
        }
        $data = file_get_contents('welcome.html');
        $this->response->render200($data);
    }
}

/**
 * A super-primitive session handler class
 *
 * @category  WebHemi2
 * @package   WebHemi2
 */
class Session
{
    /** @var array $sessionStorage */
    private $sessionStorage;

    /** @var bool $authenticated */
    private $authenticated = false;

    /** Custom session cookie name */
    const SESSION_NAME = '__whsuid';

    /** The key where the session stores if it is an authenticated session or not */
    const SESSION_AUTHENTICATION_KEY = 'isValid';

    /** Session lifetime in seconds */
    const SESSION_TTL = 240;

    /** Page cache settings */
    const SESSION_PRAGMA = 'nocache';

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->initSession();

        return $this;
    }

    /**
     * Initialize the PHP session
     *
     * @throws Exception
     */
    private function initSession()
    {
        session_name(self::SESSION_NAME);
        session_cache_limiter(self::SESSION_PRAGMA);
        session_set_cookie_params(self::SESSION_TTL);

        if (!session_start()) {
            throw new Exception('Can\'t start PHP session');
        }

        session_regenerate_id();

        $this->read();
    }

    /**
     * Set and/or get if it is an authenticated session
     *
     * @param null|bool $authenticated
     *
     * @return bool
     */
    public function isAuthenticated($authenticated = null)
    {
        if (!is_null($authenticated)) {
            $this->authenticated = (bool)$authenticated;
        }

        return $this->authenticated;
    }

    /**
     * Read the PHP session into storage
     */
    public function read()
    {
        // Read the data from the session
        if (!isset($_SESSION[self::SESSION_NAME])) {
            $this->sessionStorage = array();
        } else {
            $this->sessionStorage = $_SESSION[self::SESSION_NAME];
        }

        $this->authenticated = isset($_SESSION[self::SESSION_AUTHENTICATION_KEY])
            ? $_SESSION[self::SESSION_AUTHENTICATION_KEY]
            : false;
    }

    /**
     * Save the storage into PHP session
     */
    public function write()
    {
        $_SESSION[self::SESSION_NAME] = $this->sessionStorage;
        $_SESSION[self::SESSION_AUTHENTICATION_KEY] = $this->authenticated;
    }
}

/**
 * A super-primitive request parser class
 *
 * @category  WebHemi2
 * @package   WebHemi2
 */
class Request
{
    /** @var string $action */
    public $action;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $this->action = isset($uri[1]) ? $uri[1] : 'renderProgressPage';

        return $this;
    }

    /**
     * Determine if the request is POST
     *
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    /**
     * Retrieve POST data
     *
     * @return array|null
     */
    public function getPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : null;
    }

    /**
     * Determine if the request is Ajax
     *
     * @return bool
     */
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

/**
 * A super-primitive response class
 *
 * @category  WebHemi2
 * @package   WebHemi2
 */
class Response
{
    /** @var string $htmlHeader */
    private $htmlHeader = '<html lang="en"><head><meta charset="utf-8"></head><body>';

    /** @var string $htmlFooter */
    private $htmlFooter = '</body></html>';

    /** @var string $httpHeader */
    private $httpHeader = 'Content-type:text/html;charset:utf-8';

    /** @var bool $isAjax */
    private $isAjax = false;

    /**
     * Set response for the Ajax request
     */
    public function setAjax()
    {
        $this->httpHeader = 'Content-type:application/json';
        $this->isAjax = true;
    }

    /**
     * Render bad request response
     */
    public function render400()
    {
        header('HTTP/1.1 400 Bad request', true, 400);
        echo "<html><head><title>Error 400</title></head><body><h1>Bad Request: Error 400</h1></body></html>";
        exit;
    }

    /**
     * Render valid request
     *
     * @param mixed $data
     *
     */
    public function render200($data = true)
    {
        header($this->httpHeader);
        echo $this->isAjax ? json_encode($data) : $data;
        exit;
    }
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$installer = new Installer(new Session(), new Request(), new Response());
$installer->handleRequest();
