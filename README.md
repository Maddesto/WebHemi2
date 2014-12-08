WebHemi2
========

Version 2.0.2.4 Created by Gixx.

**THIS PROJECT IS UNDER DEVELOPMENT. EXPERIMENTAL USE ONLY!**

Introduction
------------

A Zend Framework 2 based blog engine. By comparision, it will be something like the Wordpress, but in a far more friendlier way, I hope.

Requirements
------------

- *nix operating system
- PHP 5.4+
- MySQL 5.5+
- Apache 2.4+ with 'mod_rewrite' enabled
- [Zend Framework 2.3+](https://github.com/zendframework/zf2)

Features / Goals
----------------

- Logical separation of 'Admin' and 'Website' applications [DONE]
- Support for custom themes in 'Website' application and for 'Admin' login page [DONE]
- Subdomain support for applications except 'Website' which is always on 'www' [DONE]
- Application-based privilege system with IP blacklist [DONE]
- Secure authentication [DONE]
- Secure Form solution [DONE]
- Form enhancements [DONE]
- Easy-to-use 'Admin' application [IN PROGRESS]
- 'Virtual folder structure'-like content management [PLANNING]
- Support for 3rd-party plugins [PLANNING]
- Language support [PLANNING]
- User friendly design for 'Admin' application [PLANNING]
- Responsive design for 'Admin' application to use the page from mobile devices [PLANNING]
- Default website design [PLANNING]
- First-time Setup script [PLANNING]
- Create Composer install script for vendor libraries [DONE]

Installation
------------

- Download and unzip the source files or clone the repository into your document root
- [Install composer](https://getcomposer.org/doc/00-intro.md#installation-nix) and run `php composer.phar install --no-dev` to get the required Zend Framework 2 packages
- Edit the `wh_application\config\autoload\db.global.php` and add the database connection data.
- Rename the `wh_application\module\WebHemi2\config\application.config.php.template` to `application.config.php`.
- Import the `wh_application\module\WebHemi2\schema\dump.sql` into your database
- Login to the http://yourdomain.com/wh_admin with admin / admin (l/p).

Credits
-------

- WebHemi Theme solution is based on [ZendExperts/ZeTheme](https://github.com/ZendExperts/ZeTheme).
- WebHemi ACL solution is based on [ZF-Commons/ZfcAcl](https://github.com/ZF-Commons/ZfcAcl).
- WebHemi Authentication solution is based on [Polo's blog](http://p0l0.binware.org/index.php/2012/02/18/zend-framework-2-authentication-acl-using-eventmanager/),[EvanDotPro/EdpUser](https://github.com/EvanDotPro/EdpUser) and [ZF-Commons/ZfcUser](https://github.com/ZF-Commons/ZfcUser).

Change Log
----------

Check [Change log](CHANGELOG.md)
