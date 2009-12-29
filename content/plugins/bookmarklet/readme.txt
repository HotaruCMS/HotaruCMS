Bookmarklet Plugin for Hotaru CMS
---------------------------------
Created by: Nick Ramsay

Description
-----------
A simple plugin that displays a bookmarklet link for users to drag to their toolbar or add to their favorites. They can click that link when browsing other webpages to open a new window at step 2 of the post submission process. In other words, a bookmarklet is a tool for making it easier to use your site.

Instructions
------------
1. Upload the "bookmarklet" folder to your plugins folder. Install it from Plugin Management in Admin.
2. Add the following plugin hook in a template, wherever you want to display the link:

<?php $h->pluginHook('hotaru_bookmarklet'); ?>

By default, the language used for the link is "Submit to YOUR SITE". You can change that in the language file.

It's advisable to add instructions around the bookmarklet link in the /templates/bookmarklet.php file so your users can understand how to use it. E.g.

You can easily submit articles to SITE NAME with the link below. Just bookmark it in your browser and click it whenever you are reading something you want to share!
Firefox: Drag the link to your Bookmarks Toolbar
Opera: Right click the link, and choose "Bookmark Link"
Internet Explorer: Right click the link, and choose "Add to favorites"

Changelog
---------
v.0.2 2009/12/28 - Nick - Updated for compatibility with Hotaru 1.0
v.0.1 2009/11/28 - Nick - Released first version
