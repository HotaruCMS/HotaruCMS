Stop Forum Spam Plugin for Hotaru CMS
---------------------------------
Created by: Nick Ramsay

Description
-----------
This plugin the StopForumSpam.com blacklist (http://www.stopforumspam.com/) to keep spam users out of your site. It does the following:

1. Checks for a new user's username, email address and IP address on the SFS blacklist.
2. Puts users into moderation if they are found on the blacklist. 
3. Includes a note in the User Manager plugin stating whether the user's name, email or IP address was flagged.
4. Provides as option to add any killspammed or deleted users to the blacklist so that they won't be able to register on other Hotaru CMS sites.

Instructions
------------
1. Upload the "stop_spam" folder to your plugins folder. Install it from Plugin Management in Admin.
2. Go to Admin -> Plugin Settings -> Stop Spam and enter your API key, which you can get here: 
http://www.stopforumspam.com/signup

Notes
-----
1. Spammers can only be added to the StopForumSpam.com database when killspamming or deleting them from the User Manager plugin, not from their Account page.
2. So as not to disrupt user registration or use of the User Manager, success or failure messages from the StopForumSpam.com server are not displayed. 
3. StopForumSpam.com has an API limit of 5,000 queries per day, per IP. Each time someone registers on your site, 3 API queries are made to the StopForumsSpam.com server.
4. If you accidentally add a non-spammer to the StopForumSpam.com database, you can request removal here: http://www.stopforumspam.com/removal

Changelog
---------
v.0.2 2010/01/04 - Nick - Updates for compatibility with Hotaru 1.0
v.0.1 2009/11/23 - Nick - Released first version
