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
 * @package   WebHemi2_Form_Filter
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Form\Filter;

use Zend\Filter\AbstractFilter as ZendAbstractFilter;
use Zend\Filter\FilterInterface;
use Zend\Filter\Exception;
use Zend\ServiceManager;

/**
 * WebHemi2
 *
 * Form Filter Abstraction
 *
 * @category  WebHemi2
 * @package   WebHemi2_Form_Filter
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
abstract class AbstractFilter extends ZendAbstractFilter implements
    ServiceManager\ServiceLocatorAwareInterface,
    FilterInterface
{
    /** @var  ServiceManager\ServiceLocatorInterface $serviceLocator */
    protected $serviceLocator;

    /**
     * Class constructor.
     *
     * @param array $optionArray
     */
    public function __construct($optionArray = [])
    {
        $filterOptions = [];

        foreach ($optionArray as $key => $option) {
            if ($option instanceof ServiceManager\ServiceLocatorInterface) {
                /** @var ServiceManager\ServiceLocatorInterface $option */
                $this->setServiceLocator($option);
            } else {
                $filterOptions[] = $option;
            }
        }

        $this->setOptions($filterOptions);
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
     * @return AbstractFilter
     */
    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
