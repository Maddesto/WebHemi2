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


    $('#loginForm').ajaxForm(
        {
            url: '/login/',
            type: 'post',
            success: function(data) {
                if (data.success) {
                    location.href = 'http://' + DOMAIN + '/';
                } else {
                    // remove all previous errors
                    $('div.error').remove();

                    // for all elements with errors
                    for (var i in data.error) {
                        // if we have form error
                        if (i.lastIndexOf('Form') != -1) {
                            var formId = i;

                            // for all form elements with errors
                            for (var j in data.error[i]) {
                                var elementId = j;
                                var errorBlock = null;
                                var message = data.error[i][j];

                                if ($('#' + formId + ' div.element.' + elementId + ' div.error').length < 1) {
                                    $('#' + formId + ' div.element.' + elementId).append('<div class="error"><ul></ul></div>');
                                }

                                if (errorBlock == null) {
                                    errorBlock = $('#' + formId + ' div.element.' + elementId + ' div.error ul');
                                }

                                errorBlock.append('<li>' + message + '</li>');
                            }
                        } else {
                            // it's not a form error, so redirect to index page to see
                            location.href = 'http://' + DOMAIN + '/';
                        }
                    }
                }
            }
        }
    );
});
