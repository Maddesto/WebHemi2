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

var path = '//' + STATIC_DOMAIN + '/resources/theme/' + THEME_NAME + '/img/login/';
var images = [
    '7-themes-com-7028725-green-foliage-branches.jpg',
    '7-themes-com-7033392-autumn-red-leaves.jpg',
    '7-themes-com-7038256-autumn-colors-leaves.jpg',
    '7-themes-com-7041505-tree-red-leaves.jpg',
    '7-themes-com-7041410-magnolia-flowers.jpg'
];
var cache = [];
var min = 0;
var max = images.length - 2;
var loadedIndex = Math.floor(Math.random()*(max - min + 1) + min);
console.log(loadedIndex);
for (var i = 0; i < images.length; i++) {
    cache[i] = document.createElement('img');
    cache[i].src = path + images[i];
}

$(document).ready(function() {
    $('body').css('background-image', 'url(' + path + images[loadedIndex] + ')');

    var switcher = document.createElement('div');
    switcher.setAttribute('id', 'switcher');
    $(switcher).css('background-image', 'url(' + path + images[++loadedIndex] + ')');
    $('body').append(switcher);

    var license = document.createElement('div');
    license.setAttribute('id', 'license');
    $(license).html('Images: <a href="http://7-themes.com" target="_blank">7-themes.com</a>');
    $('body').append(license);

    setInterval(function() {
        $('#switcher').fadeIn(1500, function(){
            $('body').css('background-image', 'url(' + path + images[loadedIndex] + ')');

            if(++loadedIndex >= images.length) {
                loadedIndex = 0;
            }

            $('#switcher').css({
                'display' : 'none',
                'background-image': 'url(' + path + images[loadedIndex] + ')'
            });
        })
    }, 10000);
});