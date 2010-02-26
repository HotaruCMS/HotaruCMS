RPX Plugin for Hotaru CMS
---------------------------------
Created by: Nick Ramsay

Description
-----------
This plugin allows your users to register and login via third party providers such as Twitter, Facebook, Google and Yahoo.

Instructions
------------
1. Read up about RPX the different plans at http://rpxnow.com. This plugin works with Basic accounts, but if you have a Plus or Pro account, your users can log in with multiple providers. 
2. Create an account at http://rpxnow.com, set up Twitter, Facebook, etc, and make a note of your application's name and your API key.
3. Upload the "rpx" folder to your plugins folder. Install it from Plugin Management in Admin.
4. Access it from the Admin sidebar
5. Enter your application's name (lowercase with hyphens instead of spaces), your RPX API key and desired language for the RPX sign in widget.
6. Select your account and display type. The display type lets you either (1) embed the RPX widget in the login and registration pages, (2) overlay the screen with the widget from links in the login and registration pages, or (3) replace the login and registration pages completely, and use a "Sign in" link in the navigation bar to overlay the screen with the widget.

Changelog
---------
v.0.5 2010/02/25 - Nick - New plugin hook in register template (used by TOS AntiSpam plugin)
v.0.4 2010/01/16 - Nick - Fixes for RPX change from GET to POST method
v.0.3 2010/01/04 - Nick - Updates for compatibility with Hotaru 1.0
v.0.2 2009/11/26 - Nick - Bug fixes for RPX Basic login
v.0.1 2009/11/25 - Nick - Released first version