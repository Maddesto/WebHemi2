WebHemi2
========

Version 2.0.0.3 (a.k.a. 2.0 alpha 3) Created by Gixx.

**THIS PROJECT IS UNDER DEVELOPMENT YET. DO NOT USE!**

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
- Privilege system with IP blacklist [PARTIAL]
- Secure authentication [PLANNING]
- Secure Form solution [PLANNING]
- 'Virtual folder structure'-like content management [PLANNING]
- Support for 3rd-party plugins and modules [PLANNING]
- Install script [PLANNING]

Installation
------------

1. Clone this project's `public` folder into your website's document root. It is recommended that the target folder to be empty before you put WebHemi's contents into it.
2. Clone the [Zend Framework 2](https://github.com/zendframework/zf2) project into `wh_application/venor` folder

_In future_

- Import the SQL schema located in `schema/install.sql`.
- Call `http://yourdomain.org/install` to configure your site