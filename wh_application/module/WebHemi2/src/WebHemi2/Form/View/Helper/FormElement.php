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

use WebHemi2\Form\Element\FabButton;
use WebHemi2\Form\Element\SingleFileUpload;
use WebHemi2\Form\Element\PlainText;
use WebHemi2\Form\Element\Location;
use WebHemi2\Form\Element\Toggle;
use Zend\Form\View\Helper\FormElement as OriginalFormElement;
use Zend\Form\ElementInterface;

/**
 * WebHemi2
 *
 * Form view helper for the basic element
 *
 * @category  WebHemi2
 * @package   WebHemi2_Form_View_Helper
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class FormElement extends OriginalFormElement
{
    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        // If renderer is not a plugin, we return an empty string
        if (!method_exists($renderer, 'plugin')) {
            return '';
        }

        if ($element instanceof PlainText) {
            $helper = $renderer->plugin('form_plain_text');
            return $helper($element);
        }

        if ($element instanceof Location) {
            $helper = $renderer->plugin('form_location');
            return $helper($element);
        }

        if ($element instanceof Toggle) {
            $helper = $renderer->plugin('form_toggle');
            return $helper($element);
        }

        if ($element instanceof FabButton) {
            $helper = $renderer->plugin('form_fab_button');
            return $helper($element);
        }

        if ($element instanceof SingleFileUpload) {
            $helper = $renderer->plugin('form_single_file_upload');
            return $helper($element);
        }

        return parent::render($element);
    }
}
