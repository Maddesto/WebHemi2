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

use Zend\Form\View\Helper\FormFile;
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
class FormSingleFileUpload extends FormFile
{
    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes          = $element->getAttributes();
        $attributes['type']  = $this->getType($element);
        $attributes['name']  = $name;

        $value = $element->getValue();
        if (is_array($value) && isset($value['name']) && !is_array($value['name'])) {
            $attributes['value'] = $value['name'];
        } elseif (is_string($value)) {
            $attributes['value'] = $value;
        }

        // If this element is an MDL element
        if (array_key_exists('data-mdl', $attributes)) {
            $output = <<<EOH
                <input class="mdl-textfield__input" type="text" name="{$element->getAttribute('id')}FileName"
                    id="{$element->getAttribute('id')}FileName" readonly>
                <div class="mdl-button mdl-button--primary mdl-button--icon mdl-button--file">
                    <i class="material-icons">attach_file</i>
                    <input type="file" id="{$element->getAttribute('id')}">
                </div>
                <script type="text/javascript">
                    document.getElementById("{$element->getAttribute('id')}").onchange = function () {
                        var element = $('#{$element->getAttribute('id')}FileName');
                        if (!element.parent().hasClass('is-focused') && !element.parent().hasClass('is-dirty')) {
                            element.trigger('focus');
                        }
                        if (this.files.length > 0 && typeof this.files[0].name != 'undefined') {
                            element.val(this.files[0].name);
                            element.parent().addClass('is-dirty');
                        } else {
                            element.val('');
                            element.parent().removeClass('is-dirty');
                        }
                    };
                </script>
EOH;
        } else {
            $output = sprintf(
                '<input %s%s',
                $this->createAttributesString($attributes),
                $this->getInlineClosingBracket()
            );
        }

        return $output;
    }
}
