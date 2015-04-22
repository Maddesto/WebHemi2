Change log
==========

----------------
Version 2.0.3-p1
----------------
- Added the first working UnitTest
- Refactored the autoloading: now it fully depends on the composer
- Continue refactoring code style according to CodeSniffer
- The default favicon is a physical file and not a base64 encoded string any more.
- Minor bug fixes

Version 2.0.3
-------------
- Add Code Sniffer to `dev` and fix several issues that break the PSR-2 standard
- Modify the composer.json file with proper information
- Changed source code to use the `short array declaration` - the minimal PHP version is 5.4
- Get rid of include the `dump.php` in source. Now the composer's autoload-dev will do it
- Standardized code versioning

Version 2.0.2-p5
----------------
- Add error page for Error 500, when no mod_rewrite is enabled
- IP ban counter now also work with invalid form POSTs
- Separate entry point to favicon.ico: now a PHP script will provide the favicon based on the APPLICAITION_MODULE
- Modified view helpers to include static content from the cookie free static domain
- TODO: make the static domain to be set on/off

Version 2.0.2-p4
----------------
- Give up [WH-#] versioning the commits
- Added tools for generating password and creating resource folder links
- Fixed major issues in user management
- Fixed issues with routing
- Refactored default theme resources
- Fixed Chrome's autocomplete ignore bug
- Switched to better CSS reset
- First template designs for the default theme (admin login and admin area)

Version 2.0.2-p3
----------------
- Refactored ACL, to allow the users to have different rules in different applications
- Clean up configuration files from unnecessary ALC data
- Reorganize the action names as well as the routers
- Fixed .htaccess redirect to protect WebHemi2 files
- Minor bug fixes

Version 2.0.2-p2
----------------
- Added Composer install script for the vendor codes (ZF2)
- Changed WebHemi2's View\Helper\Link to View\Helper\Url to cover ZF2's function completely
- Change application constant definitions to call anonymous functions instead of global functions

Version 2.0.2-p1
----------------
- Fix major bug in routing when the module is a directory and not a subdomain
- Fix PHPDoc errors
- Add type hinting for variables
- TODO: fix problem of sharing session when a module is a directory

Version 2.0.2
-------------
- Refactoring: stay with the standalone solution for simplicity and better user experience
- Remove test codes (templates, config)
- Add config template for the application

Version 2.0.1-p1
----------------
- Finished to change code style to validate against PSR-1 and PSR-2
- Added theme support for the 'Admin' login page
- Added new Component: `Cipher` for encoding (even if there is no mcrypt library)
- Added new Component: `Image` for simplify the work with images
- Minor bug fixes

Version 2.0.1
-------------
- Minimum required ZF version is 2.3.1
- Full application refactoring: no more hacking with Zend Framework Bootstrap
- Provide a valid Zend Framework 2 module
- Started to change code style to PSR-2
- Major bug fixes: eliminate every PHP STRICT errors

Version 2.0.0-p12
-----------------
- Finished the development of user settings module 
- Fixed auth data refresh bug after editing own profile
- Fixed user model transaction problem
- Minor bug fixes

Version 2.0.0-p11
-----------------
- Form enhancements: extended methods - getData(), setData(), isValid()
- Implementing PlainText form element
- Setting avatar in several different ways: GR Avatar ID, Internet URI to an image file, uploading image. Each has its own validation process.

Version 2.0.0-p10
-----------------
- Compatibility fixes for Zend Framework version 2.2.1
- Fixes and improvements in WebHemi\Form
- Added form element filtering prior to validation
- Implementing [ezyang](https://github.com/ezyang/htmlpurifier)'s HTML Purifier library

Version 2.0.0-p9
----------------
- Minimum required ZF version is 2.2.0+
- Major improvements in ZF2's form validation
- User settings module development
- Fixed source code intentions to Tabs

Version 2.0.0-p8
----------------
- Minimum required ZF version is 2.1.5+
- Implemented User Meta Model to make user data handling more flexible
- Fixed some theme rendering issues
- Fine tuned the privilege system

Version 2.0.0-p7
----------------
- Porting code base from ZF 2.0.6 to ZF 2.1.1
- Major fixes in error handling

Version 2.0.0-p6
----------------
- Custom admin login page
- Basic menu structure in the admin area
- Basic JavaScript component loader solution
- Fixes in 403 and 404 error handling
- Separating the layout to common blocks

Version 2.0.0-p5
----------------
- Minor fixes in Authentication
- Minor bug fixes

Version 2.0.0-p4
----------------
- Major fixes in WebHemi Form
- Fixed and secured the module-specific auto-login process
- Minor bug fixes

Version 2.0.0-p3
----------------
- Unified HTML Form rendering solution with the __toString() magic method.
- Minor bug fixes

Version 2.0.0-p2
----------------
- Improvements in authentication: BCrypt credential hashing, DDos-proof banning system (ALC-assert)
- Fixed ACL's Clean IP Assertion function (auto-release lock after timeout)
- Changes in webhemi_user schema (no update file presented)
- Code optimization
- Minor bug fixes

Version 2.0.0-p1
----------------
- Added basic authentication
- Added basic form solution
- Refactored event management
- Added authentication-related helpers
- Code optimization
- Minor bug fixes

Version 2.0.0
-------------
- Logical separation of 'Admin' and 'Website' applications
- Support for custom themes in 'Website' application
- Subdomain support for applications except 'Website' which is always on 'www'
- Privilege system with IP blacklist
