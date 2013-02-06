Change log
==========

----------------
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