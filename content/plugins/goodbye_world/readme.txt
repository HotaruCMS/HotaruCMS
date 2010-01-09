Goodbye World Plugin for Hotaru CMS
---------------------------------
Created by: Nick Ramsay

Description
-----------
A demonstration plugin that shows how you can change or extend the behavior of an existing plugin. Goodbye World overrides the Hello World plugin, changing the text from "Hello" to "Goodbye". It does this by adding an extends parameter to the comment block at the top of the file, adding "extends HelloWorld" to the class definition, and making its own hello_world function to override the original. Please study the file for details.

Instructions
------------
1. Upload the "Goodbye_world" folder to your plugins folder. Install it from Plugin Management in Admin.
2. Make sure you have <?php $h->pluginHook('hello_world'); ?> in your theme so "Goodbye World!" can replace "Hello World!".

Changelog
---------
v.0.1 2009/12/14 - Nick - Released first version
