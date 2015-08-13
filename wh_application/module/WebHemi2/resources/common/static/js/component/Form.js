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
 * @package   Script_Component
 * @author    Gabor Ivan <gixx@gixx-web.com>
 * @copyright 2015 Gixx-web (http://www.gixx-web.com)
 * @license   http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 * @link      http://www.gixx-web.com
 */

var Form = {
    /* @var Boolean initialized  TRUE if the component is initialized */
    initialized : false,

    /**
     * Initialize Component
     */
    init : function()
    {
        // Overwrite the browser's default HTML5 validity check
        $('input').on('change invalid input hover', function() {
            var htmlInput = $(this).get(0);

            htmlInput.setCustomValidity('');

            if (!htmlInput.validity.valid) {
                return false;
                //htmlInput.setCustomValidity('empty');
            }
        });

        // Fix chrome's ignore on autocomplete=off
        $('input[autocomplete=off]').each(function(){
            var copy = $(this).clone();
            copy.val('');
            copy.removeAttr('autocomplete');
            copy.insertAfter($(this));
            $(this).hide().removeAttr('required id class tabindex accesskey');
        });

        this.initialized = true;
        console.info('Form component loaded.');
    }
};
