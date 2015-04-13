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
 * @package   WebHemi2_Form
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Form;

use Zend\ServiceManager;

/**
 * WebHemi2
 *
 * Form Service
 *
 * @category  WebHemi2
 * @package   WebHemi2_Form
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class FormService implements ServiceManager\ServiceLocatorAwareInterface
{
    /** @var array $form */
    protected static $form;
    /** @var  ServiceManager\ServiceLocatorInterface $serviceLocator */
    protected $serviceLocator;

    /**
     * Class constructor
     */
    public function __construct()
    {
        // set form container
        self::$form = [];
    }

    /**
     * Magic method for instantiate and retrieve From objects
     *
     * @param string $name
     * @param array $arguments
     * @throws ServiceManager\Exception\InvalidArgumentException
     */
    public function __call($name, $arguments)
    {
        $formName   = preg_replace('/^get/i', '', $name);
        $formName = '\\WebHemi2\\Form\\' . $formName;

        $formId = isset($arguments[0]) ? $arguments[0] : null;

        if (!class_exists($formName)) {
            throw new ServiceManager\Exception\InvalidArgumentException(sprintf('%s doesn\'t seem to be a valid class.', $formName));
        }

        if (!isset(self::$form[$formName])) {
            /** @var AbstractForm $form */
            $form = new $formName($formId);
            $form->setServiceLocator($this->serviceLocator);
            self::$form[$formName] = $form;
        }

        return self::$form[$formName];
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
     * @return FormService
     */
    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
