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
 * @package    WebHemi_Theme_Adapter
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
namespace WebHemi\Theme\Adapter;

use WebHemi\Theme\Adapter\AbstractAdapter;

/**
 * WebHemi Adapter
 *
 * @category   WebHemi
 * @package    WebHemi_Theme_Adapter
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2012, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */
class ConfigurationAdapter extends AbstractAdapter
{
	/**
     * Get the name of the theme from the adapter
	 *
     * @return string
     */
    public function getTheme()
    {
        $config = $this->serviceLocator->get('Configuration');

        return isset($config['wh_themes']['current_theme']) ? $config['wh_themes']['current_theme'] : null;
    }
}