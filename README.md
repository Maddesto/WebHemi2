WebHemi2
========

Version 2.0.1.0 Created by Gixx.

**THIS PROJECT IS UNDER DEVELOPMENT. EXPERIMENTAL USE ONLY!**

Introduction
------------

A Zend Framework 2 based full value module that provides a complete CMS and website solution. By comparision, it will be something like the Wordpress, but in a far more friendlier way, I hope.

Requirements
------------

- PHP 5.5+
- MySQL 5.5+
- Apache 2.4+ with 'mod_rewrite' enabled
- [Zend Framework 2.3+](https://github.com/zendframework/zf2)

Features / Goals
----------------

- Logical separation of 'Admin' and 'Website' applications [DONE]
- Support for custom themes in 'Website' application [DONE]
- Subdomain support for applications except 'Website' which is always on 'www' [DONE]
- Privilege system with IP blacklist [DONE]
- Secure authentication [DONE]
- Secure Form solution [DONE]
- Form enhancements [IN PROGRESS]
- Easy-to-use 'Admin' application [IN PROGRESS]
- 'Virtual folder structure'-like content management [PLANNING]
- Support for 3rd-party plugins [PLANNING]
- Language support [PLANNING]
- User friendly design for 'Admin' application [PLANNING]
- Responsive design for 'Admin' application to use the page from mobile devices [PLANNING]
- Install script [PLANNING]
- Default website design [PLANNING]

Credits
-------

- WebHemi Theme solution is based on [ZendExperts/ZeTheme](https://github.com/ZendExperts/ZeTheme).
- WebHemi ACL solution is based on [ZF-Commons/ZfcAcl](https://github.com/ZF-Commons/ZfcAcl).
- WebHemi Authentication solution is based on [Polo's blog](http://p0l0.binware.org/index.php/2012/02/18/zend-framework-2-authentication-acl-using-eventmanager/),[EvanDotPro/EdpUser](https://github.com/EvanDotPro/EdpUser) and [ZF-Commons/ZfcUser](https://github.com/ZF-Commons/ZfcUser).

Installation
------------

Since the project is in a very early phase, it is not recommended to use it in live environment!

- If you are a developer, then clone this project into your ZF2 project's `module` directory.
- Modify, the *ZF2 project*/config/application.config.php file and add the `WebHemi2` to the `modules` array.
- Import the `module/WebHemi2/schema/dump.sql` into your database.
- Create a folder named `resources` into your public webroot and create symbolik links as follows:
-- mobule/WebHemi2/resources/common/static > public/resources/common
-- mobule/WebHemi2/resources/default/static > public/resources/default
*This will be handled by the CMS in the future*

Change Log
----------

Check [Change log](CHANGELOG.md)
