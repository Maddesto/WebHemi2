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
 * @package   WebHemi2_Form_View_Helper
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
namespace WebHemi2\Form\View\Helper;

use Zend\Form\View\Helper\FormButton;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * WebHemi2
 *
 * Form view helper for the FAB Button element
 *
 * @category  WebHemi2
 * @package   WebHemi2_Form_View_Helper
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class FormFabButton extends FormButton
{
    /**
     * Generate an opening button tag
     *
     * @param  null|array|ElementInterface $attributesOrElement
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     * @return string
     */
    public function openTag($attributesOrElement = null)
    {
        $openTag = parent::openTag($attributesOrElement);

        return $openTag . '<i class="material-icons">';
    }

    /**
     * Return a closing button tag
     *
     * @return string
     */
    public function closeTag()
    {
        return '</i></button>';
    }
}
