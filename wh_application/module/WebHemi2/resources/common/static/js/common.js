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

/* @var {String} componentPath   The relative path of the components */
var componentPath = 'component/';
/* @var {Array} components       The list of the available components. Additional components may be pushed in the template */
var components = [
    'Site',
    'Config',
    'Form'
];

// when all the DOM is loaded
$(document).ready(function() {
    componentPath = $('script[src*="common.js"]').attr('src').replace(/common\.js/, '') + componentPath;
    loadComponent();
});

/**
 *    Loads JavaScript components for the site
 */
function loadComponent()
{
    // there will be index upon recursive call
    var index = typeof arguments[0] != 'undefined' ? arguments[0] : 0;

    // if the component exists in the list
    if (typeof components[index] != 'undefined') {
        componentName = components[index];

        // if the component is not loaded
        if (typeof window[componentName] == 'undefined') {
            // try to load the specified component
            $.getScript(componentPath + componentName + '.js', function(){
                window[componentName].init();
                loadComponent(index + 1);
            });
        }
        // if the component is loaded but not initialized
        else if (!window[componentName].initialized){
            window[componentName].init();
            loadComponent(index + 1);
        }
        // skip to the next component
        else {
            loadComponent(index + 1);
        }
    }
}
