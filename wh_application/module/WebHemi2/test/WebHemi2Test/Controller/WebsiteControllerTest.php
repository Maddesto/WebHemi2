<?php

namespace WebHemi2Test\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use WebHemi2\Controller\WebsiteController;

class AlbumControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setTraceError(true);

        $controller = new WebsiteController;
        $controller->getPluginManager()
            ->setInvokableClass('UserAuth', 'WebHemi2\Controller\Plugin\UserAuth');

        $this->controller = $controller;

        $this->setApplicationConfig(
            include APPLICATION_PATH . '/config/application.config.php'
        );
        parent::setUp();
    }

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