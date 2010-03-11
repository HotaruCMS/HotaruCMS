RSS Show Plugin for Hotaru CMS
--------------------------------
Created by: Nick Ramsay

Description
-----------
Add multiple RSS feeds to your templates. Show titles, summaries or full content. Combine with the Sidebar plugin for widgets.

Instructions
------------
1. Upload the "rss_show" folder to your plugins folder.
2. Install it from Plugin Management in Admin.
3. Add new feeds from the RSS Show settings page.
4. Enable them as widgets in the Widgets settings page.

Alternatively, instead of widgets, you could add yor plugin hooks like this:

To show the first feed, paste this into your template:

<?php $h->pluginHook('rss_show'); ?>

To show another feed, use this with the feed id in the array:

<?php $h->pluginHook('rss_show', '', array(2)); ?>

To show two feeds back to back, paste this into your template with the ids in the array:

<?php $h->pluginHook('rss_show', '', array(1, 2)); ?>



Changelog
---------
v.0.8 2010/03/11 - Nick - Added some sanitation to feed output
v.0.7 2010/01/04 - Nick - Updates for compatibility with Hotaru 1.0
v.0.6 2009/11/04 - Nick - Updated install function for easier upgrading
v.0.5 2009/10/13 - Nick - Bug fix for deleting feeds
v.0.4 2009/10/07 - Nick - Updates for compatibility with Hotaru 0.7
v.0.3 2009/10/01 - Nick - Updates for compatibility with Hotaru 0.6
v.0.2 2009/08/28 - Nick - Updates for compatibility with Hotaru 0.5
v.0.1 2009/08/13 - Nick - Released first version
