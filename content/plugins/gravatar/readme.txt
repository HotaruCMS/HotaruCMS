Gravatar Plugin for Hotaru CMS
--------------------------------
Created by: Nick Ramsay

Description
-----------
Enable user avatars with Gravatar, a "global" avatar which has become especially popular since Wordpress adopted it for their avatars. (http://en.gravatar.com/) 

Instructions
------------
1. Upload the "gravatar" folder to your plugins folder. 
2. Install it from Plugin Management in Admin. 

Changing the default Gravatar
-----------------------------
By default, the plugin will fall back on the default_80.png image in the /gravatar/images folder. You can override this by making your own default_80.png image and putting it in your theme's images folder.

Option: Random default avatars
------------------------------
If most of your users don't have Gravatar accounts, you might prefer to enable this feature. Open gravatar.php and change $random_default to near the top of the file as follows:

	0: Off - the default avatar will not be randomized
	
	1: Normal - shows a random avatar per user, per page. E.g. user "max_98" has the same avatar in all places on the page
	
	2: Extreme - shows a random avatar per user per instance. E.g. user "max_98" has different avatars on the same page

The images are only used when a user does *not* have a Gravatar account. The images can be found in the /gravatar/images folder and were taken from http://www.phpbbhacks.com/avatars.php. You can change the images, the number of images, and their filenames. 

Changelog
---------
v.1.0 2010/07/06 - Nick - Added option to randomize the default avatar
v.0.9 2010/04/03 - Nick - Removed requirement to have the Users plugin enabled
v.0.8 2010/02/10 - Nick - Added ability to test if a user has a Gravatar
v.0.7 2009/12/26 - Nick - Updates for compatibility with Hotaru 1.0
v.0.6 2009/10/31 - Nick - Changes to make it easier for other plugins to use Gravatar
v.0.5 2009/10/26 - Nick - Added a "rating" setting (edit in "install_plugin" function)
v.0.4 2009/10/06 - Nick - Updates for compatibility with Hotaru 0.7
v.0.3 2009/10/01 - Nick - Updates for compatibility with Hotaru 0.6
v.0.2 2009/08/28 - Nick - Updates for compatibility with Hotaru 0.5
v.0.1 2009/08/19 - Nick - Released first version