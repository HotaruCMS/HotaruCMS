Search Plugin for Hotaru CMS
---------------------------------
Created by: Nick Ramsay

Description
-----------
Give your users the ability to search yor site.

Instructions
------------
1. Upload the "search" folder to your plugins folder.
2. Install it from Plugin Management in Admin.
3. Activate the Search widget in Admin -> Widgets

Note
----
If you'd like to show the search box without using it in a widget, paste this PHP into your template:

<?php $hotaru->plugins->pluginHook('search_box'); ?>

Changelog
---------
v.0.9 2010/02/18 - Nick - Code changes for pagination
v.0.8 2009/12/31 - Nick - Updates for compatibility with Hotaru 1.0
v.0.7 2009/11/04 - Nick - Updates for compatibility with Hotaru 0.8
v.0.6 2009/10/18 - Nick - Bug fix for search terms less than 4 characters.
v.0.5 2009/10/13 - Nick - CSS and template update.
v.0.4 2009/10/12 - Nick - Refills the search box with the term just searched for.
v.0.3 2009/10/05 - Nick - Updates for compatibility with Hotaru 0.7
v.0.2 2009/10/01 - Nick - Updates for compatibility with Hotaru 0.6
v.0.1 2009/08/31 - Nick - Released first version
