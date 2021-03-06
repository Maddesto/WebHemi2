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
 * @package   WebHemi2_Controller_Plugin
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Controller\Plugin;

use WebHemi2\Controller\AbstractController;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager;
use WebHemi2\Form\AbstractForm;

/**
 * Controller plugin for WebHemi2 Form
 *
 * @category  WebHemi2
 * @package   WebHemi2_Controller_Plugin
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class GetForm extends AbstractPlugin
{
    /**
     * Retrieve specific WebHemi2 Form
     *
     * @param string $formName
     * @param string $name
     *
     * @return AbstractForm
     */
    public function __invoke($formName, $name = null)
    {
        return $this->getServiceLocator()->get('formService')->$formName($name);
    }

    /**
     * Retrieve ServiceLocatorInterface instance
     *
     * @return ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        /** @var AbstractController $controller */
        $controller = $this->getController();
        return $controller->getServiceLocator();
    }
}
