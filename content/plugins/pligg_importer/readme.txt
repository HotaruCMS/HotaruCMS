Pligg Importer Plugin for Hotaru CMS
--------------------------------
Created by: Nick Ramsay

Description
-----------
Import a Pligg database into Hotaru CMS. Imports the following tables: Links, Comments, Users, Categories, Tags and Votes. 

Instructions
------------
1. Export these tables as non-zipped XML files from your Pligg database: categories, comments, links, users, tags and votes.
2. If you have access to php.ini, check that upload_max_filesize is greater than the largest XML file you exported. Contact your webhost for help.
3. Give the Uploads folder in /content/plugins/pligg_importer/ is writable (chmod 777 in FTP).
4. Create an account with the same login, email and password your god or admin user had on your Pligg site. This ensures you are still logged in as an administrator even after you overwrite the current users table during the import. See this comment about changing the username "god" to a four character name: http://hotarucms.org/showpost.php?p=473&postcount=10
5. Click Pligg Importer in the Admin sidebar and when ready, click "Import a Pligg Database" and follow the steps.

Note
----
Depending on the size of your Pligg database, importing each file can take a long time. This isn't a straight import. Almost every aspect of your Pligg database is changed. For example, all users will get a new id number, so every table that has a user_id field needs updating with this new id. The same goes for link ids, category ids etc. Your server's CPU/database will be pushed to its limits! If you are coming from SWCMS, consider using the SWCMS DB Cleanup module before importing your site.

Help Needed
-----------
This plugin has been developed using a Social Web CMS database, which is almost identical to Pligg 0.9.9.5. If you have a non-empty Pligg 1.0 database and can test it with this plugin, that would be greatly appreciated.

Changelog
---------
v.0.8 2010/01/03 - Nick - Updates for compatibility with Hotaru 1.0
v.0.7 2009/11/22 - Nick - Bug fixes for illegal offset warnings
v.0.6 2009/11/01 - Nick - Updated to copy a user's IP address from Pligg to Hotaru
v.0.5 2009/10/17 - Nick - Renamed CSS 'next' as 'pliggimp_next' to avoid clashes
v.0.4 2009/10/01 - Nick - Updates for compatibility with Hotaru 0.7
v.0.3 2009/10/01 - Nick - Updates for compatibility with Hotaru 0.6
v.0.2 2009/08/28 - Nick - Added Comments and changed "Character Cleaner"
v.0.1 2009/08/16 - Nick - Released first version
