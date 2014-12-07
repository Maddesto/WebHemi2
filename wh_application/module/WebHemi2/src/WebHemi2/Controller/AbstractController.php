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
 * @package    WebHemi2_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use WebHemi2\Controller\Plugin\UserAuth;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * WebHemi2 Application Controller
 *
 * @category   WebHemi2
 * @package    WebHemi2_Controller
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
abstract class AbstractController extends AbstractActionController
{
    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     *
     * @return mixed
     */
    public function onDispatch(MvcEvent $e)
    {
        if (APPLICATION_MODULE == ADMIN_MODULE) {
            $headerBlock = new ViewModel();
            $headerBlock->setTemplate('block/AdminHeaderBlock');

            $menuBlock = new ViewModel();
            $menuBlock->setVariable('activeMenu', 'application');
            $menuBlock->setTemplate('block/AdminMenuBlock');

            $footerBlock = new ViewModel();
            $footerBlock->setTemplate('block/AdminFooterBlock');

            $this->layout()->addChild($headerBlock, 'headerBlock')
                ->addChild($menuBlock, 'menuBlock')
                ->addChild($footerBlock, 'footerBlock');
        }

        return parent::onDispatch($e);
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
