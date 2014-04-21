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
 * @package    WebHemi2_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi2\Form;

use Zend\ServiceManager\ServiceManager,
    Zend\ServiceManager\Exception,
    Zend\ServiceManager\ServiceManagerAwareInterface;

/**
 * WebHemi2 Form Service
 *
 * @category   WebHemi2
 * @package    WebHemi2_Form
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2014, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class FormService implements ServiceManagerAwareInterface
{
    /** @var array $form */
    protected static $form;
    /** @var ServiceManager $serviceManager */
    protected $serviceManager;

    /**
     * Class constructor
     */
    public function __construct()
    {
        // set form container
        self::$form = array();
    }

    /**
     * Magic method for instantiate and retrieve From objects
     *
     * @param string $name
     * @param array $arguments
     * @throws Exception\InvalidArgumentException
     */
    public function __call($name, $arguments)
    {
        $formName   = preg_replace('/^get/i', '', $name);
        $formName = '\\WebHemi2\\Form\\' . $formName;
        
        $formId = isset($arguments[0]) ? $arguments[0] : null;

        if (!class_exists($formName)) {
            throw new Exception\InvalidArgumentException(sprintf('%s doesn\'t seem to be a valid class.', $formName));
        }
        
        if (!isset(self::$form[$formName])) {
            $form = new $formName($formId);
            $form->setServiceManager($this->serviceManager);
            self::$form[$formName] = $form;
        }

        return self::$form[$formName];
    }

    /**
     * Retrieve ServiceManager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set ServiceManager instance
     *
     * @param ServiceManager $serviceManager
     * @return UserAuth
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
