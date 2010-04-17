Comments Plugin for Hotaru CMS
--------------------------------
Created by: Nick Ramsay

Description
-----------
Enable your registered users to comment on each post. Supports avatars (e.g. Gravatar plugin), replies, unlimited nesting, editing, HTML tags (chosen by the Admin), pagination and comment subscription. 

Instructions
------------
1. Upload the "comments" folder to your plugins folder. 
2. Install it from Plugin Management in Admin. 
3. Click "Comments" in the Admin sidebar to edit the settings.

Changelog
---------
v.2.0 2010/04/17 - Nick - Function changes for earier extending, and changed "comments_wrapper" from an id to a class.
v.1.9 2010/03/26 - shibuya246 - Fix for duplicate comments posted when double clicking "Submit"
		 - Ties - New JavaScript urldecode function that works with Japanese characters
                 - Nick - Change to countComments() function call
v.1.8 2010/03/18 - Nick - Fix for not sending comment subscriptions when using SMTP email authentication.
v.1.7 2010/03/11 - Nick - Fix for a previous change that broke the set pending and delete links within comments.
v.1.6 2010/02/26 - Nick - Adds link to profile navigation; Email changed to go through Hotaru's "email" function 
v.1.5 2010/02/23 - Nick - Shows messages for moderated comments: "Awaiting approval", "Exceeded Daily Limit", etc.
v.1.4 2010/02/09 - Nick - Option to hide comments after X down votes, plus pagination code changes
v.1.3 2010/01/17 - Nick - Added option for avatar size
v.1.2 2009/12/30 - Nick - Updates for compatibility with Hotaru 1.0
v.1.1 2009/11/25 - Nick - Bug fix for comment form closing when a post is edited by a non-admin user
v.1.0 2009/11/01 - Nick - Added "All comments" page, comments RSS, pagination, comments per page, comment order
v.0.9 2009/10/24 - Nick - Added ability to open/close the comment thread on individual posts.
v.0.8 2009/10/21 - Nick - Fix for Akismet overriding the status of comments from "undermod" users
v.0.7 2009/10/21 - Nick - Adds "status" field and option for setting new comments as "pending"
v.0.6 2009/10/18 - Nick - Added functions for deleting comments when a post or user is deleted 
v.0.5 2009/10/15 - Nick - Changed CSS "comments_comments_link" to simply "comment_link" & added a new plugin hook
v.0.4 2009/10/07 - Nick - Updates for compatibility with Hotaru 0.7
v.0.3 2009/10/01 - Nick - Updates for compatibility with Hotaru 0.6
v.0.2 2009/08/30 - Nick - Fixed Reply/Edit bug, newline bug, save settings bug and added email address option
v.0.1 2009/08/26 - Nick - Released first version