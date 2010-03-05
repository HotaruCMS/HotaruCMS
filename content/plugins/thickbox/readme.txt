ThickBox Plugin for Hotaru CMS
---------------------------------
Created by: Nick Ramsay

Description
-----------
This plugin includes ThickBox 3.1

"ThickBox is a webpage UI dialog widget written in JavaScript on top of the jQuery library. Its function is to show a single image, multiple images, inline content, iframed content, or content served through AJAX in a hybrid modal."

By itself, this plugin doesn't do anything, but other plugins can use it to display images, videos and other media.

Instructions
------------
1. Upload the "thickbox" folder to your plugins folder. Install it from Plugin Management in Admin.

To test it, put an image in your theme's images folder and place this somewhere in your theme, changing IMAGENAME.JPG to match your image:
<a href="<?php ehco BASEURL; ?>content/themes/<?php echo THEME; ?>images/IMAGENAME.JPG" class="thickbox">CLICK HERE</a>

If it doesn't work, clear the css/js cache from Admin -> Maintenance, then view the page again and do a hard refresh (usually CTRL+F5) to force inclusion of the javascript and css files.


Changelog
---------
v.0.3 2010/03/04 - Nick - Removed IE6 hacks which caused IE problems when CSS is minified
v.0.2 2010/01/02 - Nick - Updated for compatibility with Hotaru 1.0
v.0.1 2009/11/28 - Nick - Released first version