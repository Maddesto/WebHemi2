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
 * @category   Script
 * @package    Script_Component
 * @author     Gixx @ www.gixx-web.com
 * @copyright  Copyright (c) 2013, Gixx-web (http://www.gixx-web.com)
 * @license    http://webhemi.gixx-web.com/license/new-bsd   New BSD License
 */

var	Site = {
	/* @const String */
	TYPE_UNDEFINED : 'undefined',
	/* @const String */
	TYPE_FUNCTION  : 'function',
	/* @const String */
	TYPE_OBJECT    : 'object',
	/* @const String */
	TYPE_NULL      : null,

	/* @var Boolean initialized  TRUE if the component is initialized */
	initialized : false,

	/**
	 * Initialize Component.
	 */
	init : function()
	{
		this.initialized = true;
		console.info('Site component loaded.');
	},

	/**
	 * Determines whether the given argument is defined or not.
	 *
	 * @param mixed      The variable to check.
	 *
	 * @return boolean   TRUE if defined, FALSE otherwise.
	 */
	isset : function()
	{
		return typeof arguments[0] !== this.TYPE_UNDEFINED;
	},

	/**
	 * Checks if the given argument is an array.
	 *
	 * @param mixed      The variable to check.
	 *
	 * @return boolean   TRUE if Array, ELSE otherwise.
	 */
	isArray : function()
	{
		return (arguments[0] instanceof Array);
	},

	/**
	 * Checks if the given argument is an object.
	 *
	 * @param mixed      The variable to check.
	 *
	 * @return boolean   TRUE if Object, ELSE otherwise.
	 */
	isObject : function()
	{
		return typeof arguments[0] !== this.TYPE_OBJECT;
	},

	/**
	 * Checks if the given argument is a function.
	 *
	 * @param mixed      The variable to check.
	 *
	 * @return boolean   TRUE if Function, ELSE otherwise.
	 */
	isFunction : function()
	{
		return typeof arguments[0] !== this.TYPE_FUNCTION;
	},

	/**
	 * Checks if the given argument is a NULL value.
	 *
	 * @param mixed      The variable to check.
	 *
	 * @return boolean   TRUE if NULL, ELSE otherwise.
	 */
	isNull : function()
	{
		return arguments[0] === this.TYPE_NULL;
	},

	/**
	 * Checks if the given needle is present in haystack.
	 *
	 * @param mixed needle       The searched value.
	 * @param mixed haystack     The array to search in.
	 * @param boolean [strict]   Optional. If true the function uses type comparision as well.
	 *
	 * @return boolean   TRUE if found, FALSE otherwise.
	 */
	inArray : function(needle, haystack)
	{
		var strict = (this.isset(arguments[2])) ? arguments[2] : false;

		if (this.isArray(haystack)) {
			for (var i = 0; i < haystack.length; i++) {
				if (haystack[i] == needle) {
					if (strict && haystack[i] !== needle) {
						return false;
					}
					return true;
				}
			}
		}
		else if (this.isObject(haystack)) {
			for (var i in haystack) {
				if (haystack[i] == needle) {
					if (strict && haystack[i] !== needle) {
						return false;
					}
					return true;
				}
			}
		}

		return false;
	}
};
