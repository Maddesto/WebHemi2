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
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

namespace WebHemi\View\Helper;

use Zend\View\Helper\AbstractHelper,
	HTMLPurifier;

/**
 * View helper for HTML Purufier
 *
 * @category   WebHemi
 * @package    WebHemi_View_Helper
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class Purify extends AbstractHelper
{
	/** @var HTMLPurifier $purifier */
	protected $purifier;

	/**
	 * Class constructor.
	 * 
	 * @param HTMLPurifier $purifier
	 */
	public function __construct(HTMLPurifier $purifier)
	{
		$this->purifier = $purifier;
	}

	/**
	 * Retrieves the HTML Purifier object
	 * 
	 * @return HTMLPurifier
	 */
	protected function getPurifier()
	{
		return $this->purifier;
	}

	/**
	 * Purify HTML content
	 * 
	 * @param string $html
	 * @return string
	 */
	public function __invoke($html = null)
	{
		if (empty($html)) {
			return '';
		}

		return $this->getPurifier()->purify($html);
	}
}