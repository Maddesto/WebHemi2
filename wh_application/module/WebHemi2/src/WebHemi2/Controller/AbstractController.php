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
 * @package   WebHemi2_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use WebHemi2\Controller\Plugin\UserAuth;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * WebHemi2
 *
 * Application Controller
 *
 * @category  WebHemi2
 * @package   WebHemi2_Controller
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 *
 * @method UserAuth
 */
abstract class AbstractController extends AbstractActionController
{
    /**
     * Execute the request
     *
     * @param  MvcEvent $event
     *
     * @return mixed
     */
    public function onDispatch(MvcEvent $event)
    {
        $config = $this->getServiceLocator()->get('Configuration');

        $headerBlock = new ViewModel();
        $headerBlock->setTemplate('block/header');

        $menuBlock = new ViewModel();
        $menuBlock->setTemplate('block/menu');

        $footerBlock = new ViewModel();
        $footerBlock->setTemplate('block/footer');

        $this->layout()
            ->addChild($headerBlock, 'headerBlock')
            ->addChild($menuBlock, 'menuBlock')
            ->addChild($footerBlock, 'footerBlock')
            ->setVariable('themeSettings', $config['view_manager']['theme_settings']);

        return parent::onDispatch($event);
    }

    /**
     * Retrieve UserAuth controller plugin
     *
     * @return UserAuth
     */
    protected function getUserAuth()
    {
        return $this->UserAuth();
    }
}
