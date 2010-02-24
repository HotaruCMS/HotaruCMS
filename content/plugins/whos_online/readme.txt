Who's Online Plugin for Hotaru CMS
--------------------------------
Created by: Nick Ramsay

Description
-----------
With this plugin you can view the number of members and guests online, and show the names and avatars of online members.

Instructions
------------
1. Upload the "whos_online" folder to your plugins folder.
2. Install it from Plugin Management in Admin.
3. Edit the Who's Online settings in Admin -> Who's Online
4. Enable the widget in Admin -> Widgets

About
-----
If you're surprised by a higher than expected number of guests, it's because the plugin counts the number of session files saved on the server. These files are removed periodically according to settings in your server's php.ini file. For technical information, read: http://www.brainbell.com/tutors/php/php_mysql/Garbage_Collection.html. Members are shown if they have been active in the last five minutes. 

Changelog
---------
v.0.2 2010/02/24 - Nick - Fix for undefined $count
v.0.1 2010/01/14 - Nick - Released first version
