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
 * @package    WebHemi2_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    WebHemi2\Form\AbstractForm;

/**
 * Controller plugin for WebHemi2 Form
 *
 * @category   WebHemi2
 * @package    WebHemi2_Controller_Plugin
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class GetForm extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /** @var ServiceLocatorInterface $serviceLocator */
    protected $serviceLocator;

    /**
     * Retrieve specific WebHemi2 Form
     *
     * @param string $formName
     * @param string $name
     * @return AbstractForm
     */
    public function __invoke($formName, $name = null)
    {
        return $this->getServiceLocator()->get('formService')->$formName($name);
    }

    /**
     * Retrieve ServiceLocatorInterface instance
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator->getController()->getServiceLocator();
    }

    /**
     * Set ServiceLocatorInterface instance
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return GetForm
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
