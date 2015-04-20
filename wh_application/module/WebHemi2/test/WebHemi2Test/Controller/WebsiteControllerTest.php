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

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use WebHemi2\Controller\WebsiteController;

/**
 * WebHemi2
 *
 * Website Controller Test
 *
 * @category  WebHemi2Test
 * @package   WebHemi2Test_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class WebsiteControllerTest extends AbstractHttpControllerTestCase
{
    /** @var  WebsiteController $controller */
    protected $controller;

    /**
     * General setup for the tests
     */
    public function setUp()
    {
        $this->setTraceError(true);
        $this->controller = new WebsiteController;

//        $controller->getPluginManager()
//            ->setInvokableClass('UserAuth', 'WebHemi2\Controller\Plugin\UserAuth');
//
//        $this->controller = $controller;

        parent::setUp();
    }

    /**
     * Test if the controller can be accessed
     */
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('WebHemi2');
        $this->assertControllerName('WebHemi2\Controller\Website');
        $this->assertControllerClass('WebsiteController');
        $this->assertMatchedRouteName('index');
    }
}