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
use WebHemi2Test\Fixture\AuthenticatedUserFixture;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;
use WebHemi2\Controller\UserController;

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
    /** @var  WebsiteController $controller */
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
    public function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();

        $this->controller = new UserController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'User'));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
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
        $this->routeMatch->setParam('action', 'userProfile');

        $mockedController = $this->getMockBuilder('UserController')
            ->getMock();

        $loggedInUserAuth = new AuthenticatedUserFixture();
        $mockedController->method('getUserAuth')
            ->willReturn($loggedInUserAuth);

        $mockedController->setEvent($this->event);
        $mockedController->setServiceLocator(Bootstrap::getServiceManager());
        $mockedController->getPluginManager()
            ->setInvokableClass('UserAuth', 'WebHemi2\Controller\Plugin\UserAuth');

        $result = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertInternalType('array', $result);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
