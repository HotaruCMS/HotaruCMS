HOTARU CMS
Version: 1.1.3
Released: Mar 21st 2010

INSTALLATION
------------

Please visit http://hotarucms.org/showthread.php?t=14 for the most up-to-date version of these instructions.

Instructions last updated: Mar 8th 2010

Requirements

Hotaru CMS has been developed with PHP 5.2.6, MySQL 5.0.51a and phpMyAdmin 2.11.6. If you find it works on older versions of PHP, MySQL and phpMyAdmin, please let us know so we can update these requirements accordingly.

Upgrading

   1. Backup your database.
   2. Download the latest version of Hotaru CMS.
   3. Turn off all your plugins.
   4. Overwrite ALL the old files. If you've made any customizations, read this first: http://hotarucms.org/showthread.php?t=46
   5. Go to /install/upgrade.php
   6. Turn your plugins back on
   7. Reactivate your widgets
   8. When finished, delete the install folder.

First-time Installation

   1. Download the latest version of Hotaru CMS.
   2. Create a database called "hotaru" in phpMyAdmin (or equivalent).
   3. Rename /hotaru_settings_default.php to /hotaru_settings.php.
   4. Open /hotaru_settings.php and edit the top section with your database details and path to Hotaru, e.g. http://www.myhotarusite.com/
   5. Upload the contents of the "hotaru" folder to your server.
   6. Files should have permissions set to 644 and folders should be set to 755, except...
   7. Set /cache and its sub-folders to 777
   8. Go to /install/install.php and step through the six steps, following the instructions in each.
   9. When finished, delete the install folder.

Setting up your site

   1. Log into Admin and go to Admin -> Settings
   2. Change the settings as appropriate, but leave DB_CACHE as false for now.
   3. Download plugins from the Plugin Downloads forum, unzip and upload them to the /content/plugins/ directory.
   4. Go to Admin -> Plugin Management and install the plugins one by one.
   5. Edit settings for each plugin listed in the sidebar under Plugin Settings.
   6. Click the site title/banner to view your changes.
   7. When finished, return to Admin -> Settings and set DB_CACHE to true.


Friendly URLS

If you want to use friendly urls, rename htaccess_default to .htaccess, and edit it according to the instructions within the htaccess file. Then go to Admin -> Settings and change the "friendly urls" setting to true.

Troubleshooting

If you're having trouble installing Hotaru, please post your questions with as much detail as possible in the forums at http://hotarucms.org. Thanks.
