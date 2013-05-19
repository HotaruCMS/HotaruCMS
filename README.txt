HOTARU CMS
==========
Version 1.5.0 RC3 - May 19th 2013

Version: 1.4.2 - November 20th 2010

INSTALLATION
------------

Please visit [the appropriate documentation page][1] for the most up-to-date version of these instructions.

Instructions last updated: May 19th 2013

Requirements
------------

Hotaru CMS has been tested using PHP 5.3.2~  and MySQL 5.0~.

Upgrading
---------

  1. Backup your database.
  2. Download the latest version of Hotaru CMS.
  3. Turn off all your plugins.
  4. Overwrite ALL the old files. If you've made any customizations, read up on the [Hotaru File Organization][2]
  5. Go to /install/upgrade.php and follow the steps
  6. Turn your plugins back on
  7. Reactivate your widgets
  8. When finished, delete the install folder.

First-time Installation
-----------------------

  1. Download the latest version of Hotaru CMS.
  2. Create a database called `hotaru` in you MySQL (using phpMyAdmin or similar).
  3. Rename `/settings_default.php` to `/config/settings.php`.
  4. Open `/config/settings.php` and edit the top section with your database details and path to Hotaru, e.g. `http://example.com/`
  5. Upload the contents of the `hotaru` folder to your server.
  6. Files should have permissions set to 644 and folders should be set to 755, except...
  7. Set `/cache` and its sub-folders to 777
  8. Go to `http://example.com/hotaru/install/install.php` and follow the steps
  9. When finished, delete the `install` folder.

Setting up your site
--------------------

  1. Log into Admin and go to Admin -> Settings
  2. Change the settings as appropriate, but leave `DB_CACHE` off for now.
  3. Download plugins from the [Plugin Downloads forum][3], unzip and upload them to the `/content/plugins/` directory.
  4. Go to Admin -> Plugin Management and install the plugins one by one.
  5. Edit settings for each plugin listed in the sidebar under Plugin Settings.
  6. Click the site title/banner to view your changes.
  7. When finished, return to Admin -> Settings and set `DB_CACHE` to true.


Friendly URLS
-------------

If you want to use friendly urls, rename `htaccess_default` to `.htaccess`, and edit it according to the instructions within 
the htaccess file. Then go to Admin -> Settings and change the *friendly urls* setting to true.

Troubleshooting
---------------

If you're having trouble installing Hotaru, please post your questions with as much detail as possible [in the forums][4]. Thanks.


  [1]: http://docs.hotarucms.org/index.php/Getting_Started#Installing_and_Upgrading
  [2]: http://docs.hotarucms.org/index.php/File_Organization
  [3]: http://forums.hotarucms.org/forumdisplay.php?18-Plugin-Downloads
  [4]: http://forums.hotarucms.org/forum.php
