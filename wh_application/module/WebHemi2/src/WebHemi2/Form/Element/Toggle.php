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
 * @package   WebHemi2_Form_Element
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

namespace WebHemi2\Form\Element;

use Traversable;
use Zend\Form\Element as ZendElement;

/**
 * WebHemi2
 *
 * Form Plain Text element
 *
 * @category  WebHemi2
 * @package   WebHemi2_Form_Element
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */
class Toggle extends ZendElement\Checkbox
{
    /** @var array $attributes */
    protected $attributes = [
        'type' => 'toggle',
    ];

    /**
     * Accepted options for MultiCheckbox:
     * - use_hidden_element: do we render hidden element?
     * - unchecked_value: value for checkbox when unchecked
     * - checked_value: value for checkbox when checked
     *
     * @param  array|Traversable $options
     * @return Toggle
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        $this->setUseHiddenElement($options['use_hidden_element']);
        $this->setUncheckedValue($options['unchecked_value']);
        $this->setCheckedValue($options['checked_value']);

        return $this;
    }
}
