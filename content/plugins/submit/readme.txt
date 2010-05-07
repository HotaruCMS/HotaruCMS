Social Bookmarking Submit Plugin for Hotaru CMS
---------------------------------------------
Created by: Nick Ramsay

Description
-----------
This plugin provides the submission steps for entering a new post. It also includes the "edit_post" template. 

Instructions
------------
1. Upload the "submit" folder to your plugins folder. Install it from Plugin Management in Admin.

Changelog
---------
v.2.6 2010/05/07 - Damon - Fix for clearing the temporary data table during post submission
v.2.5 2010/04/26 - Nick - If the story being submitted already exists, the user is redirected to it
                   Nick - Renamed httprequest file and class to avoid clash with a PHP extension
v.2.4 2010/04/01 - Nick - Split theme_index_top into multiple functions for easier extending (no templates/css/js changes) 
v.2.3 2010/03/28 - Nick - New plugin hook in Submit step 1 error checking 
v.2.2 2010/03/21 - Nick - Updated for Hotaru 1.1.3 (no templates/css/js changes) 
v.2.1 2010/02/15 - Nick - Removed CSRF check from submission step 1 
v.2.0 2010/02/11 - Nick - Bug fixes for character problems and stripped HTML
v.1.9 2009/12/16 - Nick - Updated for compatibility with Hotaru 1.0
v.1.8 2009/11/29 - Nick - Added plugin hooks in post descriptions
v.1.7 2009/11/27 - Nick - Bug fix for wrong order of sidebar posts. Ability to block domain extensions, e.g. .ru
v.1.6 2009/11/26 - Nick - Bug fix for default user settings plus user option for links opening source or post urls
v.1.5 2009/11/25 - Nick - New hook in submit_edit_post.php (used by Comments 1.1)
v.1.4 2009/11/03 - Nick - Changes to allow post sorting
v.1.3 2009/10/28 - Nick - Bug fix for sending trackbacks
v.1.2 2009/10/24 - Nick - Bug fix for strange symbols in fetched titles in Submit step 2
v.1.1 2009/10/23 - Nick - Users under moderation get their posts sent to 'pending'
v.1.0 2009/10/22 - Nick - New plugin hook for Akismet compatibility
v.0.9 2009/10/17 - Nick - Updates for compatibility with Post Manager plugin
v.0.8 2009/10/15 - Nick - Bug fixes for missing post url and slashed apostrophes
v.0.7 2009/10/04 - Nick - Updates for compatibility with Hotaru 0.7
v.0.6 2009/10/01 - Nick - Updates for compatibility with Hotaru 0.6
v.0.5 2009/08/31 - Nick - Made changes to accomodate the Search plugin 
v.0.4 2009/08/30 - Nick - Combined settings into a single database record 
v.0.3 2009/08/28 - Nick - Updates for compatibility with Hotaru 0.5
v.0.2 2009/08/19 - Nick - Added breadcrumb navigation, plus various changes for compatibility with other plugins
v.0.1 2009/07/29 - Nick - Released first version
