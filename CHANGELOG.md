Change log
==========

---------------
Version 2.0.1.1
---------------
- Finished to change code style to validate against PSR-1 and PSR-2
- Minor bug fixes

Version 2.0.1.0
---------------
- Minimum required ZF version is 2.3.1
- Full application refactoring: no more hacking with Zend Framework Bootstrap
- Provide a valid Zend Framework 2 module
- Started to change code style to PSR-2
- Major bugfixes: eliminate every PHP STRICT errors
- New git branch worklows: feature/tickets, release

Version 2.0.0.21
----------------
- Finished the development of user settings module 
- Fixed auth data refresh bug after editing own profile
- Fixed user model transaction problem
- Minor bugfixes

Version 2.0.0.20
----------------
- Form enhancements: extended methods - getData(), setData(), isValid()
- Implementing PlainText form element
- Setting avatar in several different ways: GR Avatar ID, Internet URI to an image file, uploading image. Each has its own validation process.

Version 2.0.0.19
----------------
- Compatibilty fixes for Zend Framework version 2.2.1
- Fixes and improvements in WebHemi\Form
- Added form element filtering prior to validation
- Implementing [ezyang](https://github.com/ezyang/htmlpurifier)'s HTML Purifier library

Version 2.0.0.18
----------------
- Minimum required ZF version is 2.2.0+
- Major improvements in ZF2's form validation
- User settings module development
- Fixed source code indentions to Tabs

Version 2.0.0.17
----------------
- Minimum required ZF version is 2.1.5+
- Implemented User Meta Model to make user data handling more flexible
- Fixed some theme rendering issues
- Fine tuned the privilege system

Version 2.0.0.16
----------------
- Porting code base from ZF 2.0.6 to ZF 2.1.1
- Major fixes in error handling

Version 2.0.0.15
----------------
- Custom admin login page
- Basic menu structure in the admin area
- Basic JavaScript component loader solution
- Fixes in 403 and 404 error handling
- Separating the layout to common blocks

Version 2.0.0.14
----------------
- Minor fixes in Authentication
- Minor bug fixes

Version 2.0.0.13
----------------
- Major fixes in WebHemi Form
- Fixed and secured the module-specific auto-login process
- Minor bug fixes

Version 2.0.0.12
----------------
- Unified HTML Form rendering solution with the __toString() magic method.
- Minor bug fixes

Version 2.0.0.11
----------------
- Improvements in authentication: BCrypt credential hashing, DDos-proof banning system (ALC-assert)
- Fixed ACL's Clean IP Assertion function (auto-release lock after timeout)
- Changes in webhemi_user schema (no update file presented)
- Code optimization
- Minor bug fixes

Version 2.0.0.10
----------------
- Added basic authentication
- Added basic form solution
- Refactored event management
- Added authentication-related helpers
- Code optimization
- Minor bug fixes

Version 2.0.0.1 - 2.0.0.9
--------------------------
- Logical separation of 'Admin' and 'Website' applications
- Support for custom themes in 'Website' application
- Subdomain support for applications except 'Website' which is always on 'www'
- Privilege system with IP blacklist
