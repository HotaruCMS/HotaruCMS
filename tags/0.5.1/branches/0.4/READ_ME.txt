HOTARU CMS
Version: Alpha 0.4
Released: August 20th 2009

INSTALLATION
------------

Please visit http://hotarucms.org/showthread.php?t=14 for the most up-to-date version of these instructions.

Instructions last updated: August 8th 2009

Requirements

Hotaru CMS has been developed with PHP 5.2.6, MySQL 5.0.51a and phpMyAdmin 2.11.6. If you find it works on older versions of PHP, MySQL and phpMyAdmin, please let us know so we can update these requirements accordingly.

Installation

   1. Download the latest version of Hotaru CMS.
   2. Create a database called "hotaru" in phpMyAdmin (or equivalent)
   3. Rename /hotaru_settings_default.php to /hotaru_settings.php
   4. Open /hotaru_settings.php and edit the top section with your database details and path to Hotaru, e.g. http://www.myhotarusite.com/
   5. Files should have permissions set to 644 and folders should be set to 755, except...
   6. Set the two cache folders to 777: /3rd_party/ezSQL/cache and /3rd_party/SimplePie/cache
   7. Go to /install/install.php and step through the six steps, following the instructions in each.

Friendly URLS

If you want to use friendly urls, rename htaccess_default to .htaccess, and edit it according to the instructions within the htaccess file. Then go to Admin -> Settings and change the "friendly urls" setting to true.

Troubleshooting

If you're having trouble installing Hotaru, please post your questions with as much detail as possible in the Getting Started forum. Thanks.


Report any problems in the forums at http://hotarucms.org