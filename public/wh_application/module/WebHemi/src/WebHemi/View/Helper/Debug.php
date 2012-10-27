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
 * @package    WebHemi_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\View\Helper;

use Zend\View\Helper\AbstractHelper,
	WebHemi\Application;

/**
 * View helper for Debug
 *
 * @category   WebHemi
 * @package    WebHemi_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Debug extends AbstractHelper
{
	/**
	 * Dump data
	 *
	 * @param  mixed   $var   The variable to dump
	 * @param  boolean $exit  OPTIONAL terminate after output
     * @param  string  $label OPTIONAL Label to prepend to output
     * @param  bool    $echo  OPTIONAL Echo output if true
     * @return string
	 */
	public function __invoke($data, $label = null, $exit = true)
	{
		$output = Application::varDump($data, $label, false);

		if ($exit) {
			echo $output;
			exit();
		}

		return $output;
	}
}
