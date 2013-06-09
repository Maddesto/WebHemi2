WebHemi2
========

Version 2.0.0.18 Created by Gixx.

**THIS PROJECT IS UNDER DEVELOPMENT. EXPERIMENTAL USE ONLY!**

Introduction
------------

A Zend Framework 2 based full value module that provides a complete CMS and website solution. By comparision, it will be something like the Wordpress, but in a far more friendlier way, I hope.

Requirements
------------

- PHP 5.3+
- MySQL 5.1+
- Apache 2.2 with 'mod_rewrite' enabled
- [Zend Framework 2](https://github.com/zendframework/zf2)

Features / Goals
----------------

- Logical separation of 'Admin' and 'Website' applications [DONE]
- Support for custom themes in 'Website' application [DONE]
- Subdomain support for applications except 'Website' which is always on 'www' [DONE]
- Privilege system with IP blacklist [DONE]
- Secure authentication [DONE]
- Secure Form solution [DONE]
- Easy-to-use 'Admin' application [IN PROGRESS]
- 'Virtual folder structure'-like content management [IN PROGRESS]
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

1. Clone this project's `public` folder into your website's document root. It is recommended that the target folder to be empty before you put WebHemi's contents into it.
2. Clone the [Zend Framework 2](https://github.com/zendframework/zf2) project into `wh_application/vendor` folder
3. Import the SQL schema located in `schema/install.sql` into your MySQL DBMS and set credentials in the `config/autoload/db.global.php` file.


_Soon_

- Call `http://yourdomain.org/install` to configure your site


Change Log
----------

Check [Change log](CHANGELOG.md)
