<?php

/**
 * WebHemi
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
 * @category   WebHemi
 * @package    WebHemi_Form_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
namespace WebHemi\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper,
	Zend\Form\ElementInterface;

/**
 * WebHemi Form view helper for the Plain Text element
 *
 * @category   WebHemi
 * @package    WebHemi_Form_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class FormPlainText extends AbstractHelper 
{
	/**
	 * Retrieves the plain text element data.
	 * 
	 * @param \Zend\Form\ElementInterface $element
	 * @return string
	 */
	public function __invoke(ElementInterface $element = null) {
		return $element->getValue();
	}
}