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
 * @category  WebHemi2Test
 * @package   WebHemi2Test_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2Test\Controller;

use WebHemi2Test\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;
use WebHemi2\Controller\UserController;
use WebHemi2\Model\User as UserModel;

/**
 * WebHemi2
 *
 * User Controller Test
 *
 * @category  WebHemi2Test
 * @package   WebHemi2Test_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class UserControllerTest extends PHPUnit_Framework_TestCase
{
    /** @var  UserController $controller */
    protected $controller;
    /** @var  Request $request */
    protected $request;
    /** @var  Response $response */
    protected $response;
    /** @var  RouteMatch $routeMatch */
    protected $routeMatch;
    /** @var  MvcEvent $event */
    protected $event;

    /**
     * General setup for the tests
     */
    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->controller = new UserController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(['controller' => 'User']);
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : [];
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
        $this->controller->getPluginManager()
            ->setInvokableClass('UserAuth', 'WebHemi2\Controller\Plugin\UserAuth');
    }

    /**
     * Test if the controller can be accessed without login
     */
    public function testUserProfileActionCanBeAccessedWithoutLogin()
    {
        // Without login you should not have access and get redirected
        $this->routeMatch->setParam('action', 'userProfile');

        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertInstanceOf('Zend\Http\PhpEnvironment\Response', $result);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test if the controller can be accessed WITH login
     */
    public function testUserProfileActionCanBeAccessedWithLogin()
    {
        // Mock the system to be logged in.
        $this->mockLogin();

        $e = 6;

        $this->routeMatch->setParam('action', 'userProfile');
        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertInternalType('array', $result);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Mock the Auth Service to lie that the user is logged in
     *
     * @param string $role Set the user with the given role
     */
    protected function mockLogin($role = 'member')
    {
        $userModel = new UserModel();
        $userModel->setUserId(1)
            ->setUsername('Tester')
            ->setRole($role)
            ->setActive(true)
            ->setEnabled(true);

        $authService = $this->getMock('WebHemi2\Auth\Auth');
        $authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userModel));

        $authService->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $authServiceFactory = $this->getMock('WebHemi2\ServiceFactory\AuthServiceFactory');
        $authServiceFactory->expects($this->any())
            ->method('createService')
            ->will($this->returnValue($authService));

        Bootstrap::getServiceManager()->setAllowOverride(true)
            ->setFactory('auth', $authServiceFactory);
    }
}
