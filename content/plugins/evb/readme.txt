External Vote Button Plugin for Hotaru CMS
--------------------------------
Created by: Nick Ramsay

Description
-----------
Ths plugin allows an external vote button (EVB) to be embedded in a remote site. When clicked, the user is redirected to the Hotaru CMS site to submit the page they came from or if its already in the database, they can vote for it (if logged in). 

Instructions
------------
1. Upload the "evb" folder to your plugins folder. 
2. Install it from Plugin Management in Admin. 
3. Instruct your site's users to add this code snippet to their pages:

To add a button on a page with a single post, just copy and paste this:

    <script type="text/javascript" src="http://www.YOURSITE.com/index.php?page=evb"></script>

If you have a page with multiple posts, you can separate each instance of the button by using the two lines of code below and providing a url for each post.

    <script type="text/javascript">submit_url = "URL OF THE POST";</script>

    <script type="text/javascript" src="http://www.YOURSITE.com/index.php?page=evb"></script>

REDIRECTING OLD BUTTONS:
------------------------
If you have migrated from Pligg or SWCMS and need your old EVB to work with Hotaru, you can add a redirect to your htaccess file if you are using friendly urls:

##### EVB REDIRECT #####
RewriteRule ^evb/check_url.js.php?$ index.php?page=evb [L]

If you aren't using friendly urls, you'll need to put an evb folder in you root directory, create an empty file called check_url.js.php (or whatever file you previously used). In that file, paste and edit the HTML redirect code described at http://www.instant-web-site-tools.com/html-redirect.html so it points to http://www.YOURSITE.com/index.php?page=evb


Changelog
---------
v.0.3 2010/01/03 - Nick - Updated for compatibility with Hotaru 1.0
v.0.2 2009/11/27 - Nick - Fix for non-object error
v.0.1 2009/11/23 - Nick - Released first version