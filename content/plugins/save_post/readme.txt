Save Post Plugin for Hotaru CMS
-----------------------------------
Created by: William Dahlheim

Description
-----------
This plugin adds a link in the sb_base_show_post_extra_fields field, allowing your members to save/flag a post as a favorite. These are shown in a sidebar widget. The sidebar widget is dynamically updated. The saved posts are saved in the user's profile data as "saved_posts". Please note that this plugin is only tested using jQuery 1.4.2.

As of version 0.5, saved posts are *publicly* shown in a "Saved Posts" page accessible from each user's profile.

Instructions
------------
1. Upload the "save_post" folder to your plugins folder.
2. Install the plugin from Plugin Management in Admin as per usual.
3. Turn the Save Post widget on in Plugin Settings > Widgets

Changelog
---------
v.0.5 2010/03/22 - Nick - Added a "Saved Posts" page in the user's profile
v.0.4 2010/02/22 - William - Added icon in widget that removes the post on click
v.0.3 2010/02/22 - William - Made it into its own widget.
v.0.2 2010/02/22 - William - Removed unnecessary plugin hooks. Moved save_post_widget.php to templates folders. Thanks a bunch Nick!
v.0.1 2010/02/21 - William - Released first version