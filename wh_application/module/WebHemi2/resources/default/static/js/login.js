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
 * @category  Script
 * @package   Script
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */


$(document).ready(function() {
    var videoTag = '<div id="wrapper"><div id="videoContainer"><video autoplay="autoplay" muted="muted" loop="loop">' +
        '<source src="http://' + STATIC_DOMAIN + '/resources/theme/default/video/minty-fresh.webm" type="video/webm">' +
        //'<source src="http://' + STATIC_DOMAIN + '/resources/theme/default/video/minty-fresh.ogv" type="video/ogg">' +
        '</video></div></div>';

    $('body').append(videoTag);
});