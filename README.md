WebHemi2
========

Version 2.0.1 Created by Gixx.

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
- Subdomain support for applications except 'Website' (it is always on 'www') [PARTIAL]
- Secure authentication and privilege system [STARTED]
- Secure Form solution [PLANNING]
- 'Virtual folder structure'-like content management [PLANNING]
- Support for 3rd-party plugins and modules [PLANNING]
- Install script [PLANNING]

Installation
------------

1. Clone this project's `public` folder into your website's document root. It is recommended that the target folder to be empty before you put WebHemi's contents into it.
2. Clone the [Zend Framework 2](https://github.com/zendframework/zf2) project into `wh_application/venor` folder
3. Import the SQL schema located in `schema/install.sql`.
4. Call `http://yourdomain.org/install` to configure your site