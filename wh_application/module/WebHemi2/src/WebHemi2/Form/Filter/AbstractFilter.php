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
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

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
abstract class AbstractFilter extends ZendAbstractFilter implements ServiceManagerAwareInterface, FilterInterface
{
    /** @var ServiceManager $serviceManager */
    protected $serviceManager;

    /**
     * Class constructor.
     *
     * @param array $optionArray
     */
    public function __construct($optionArray = [])
    {
        $filterOptions = [];

        foreach ($optionArray as $key => $option) {
            if ($option instanceof ServiceManagerAwareInterface) {
                /** @var \Zend\ServiceManager\ServiceManager $option */
                $this->setServiceManager($option);
            } else {
                $filterOptions[] = $option;
            }
        }

        $this->setOptions($filterOptions);
    }

    /**
     * Retrieve ServiceManager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        if (!isset($this->serviceManager)) {
            throw new Exception\RuntimeException('Service manager is not provided!');
        }
        return $this->serviceManager;
    }

    /**
     * Set ServiceManager instance
     *
     * @param ServiceManager $serviceManager
     *
     * @return AbstractFilter
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
