Link Bar Plugin for Hotaru CMS
--------------------------------
Created by: Nick Ramsay (Based on "SocialBar" by Shibuya246 <shibuya246.com>)

Description
-----------
A bare bones, external link bar shown at the top of each post visited from your site. 

Shibuya246 and I had a "He-Man versus Skeletor" battle over what a "bar" plugin should be like, so we agreed that I would make a developer version for people to extend with their own plugins, while he would include goodies such as logo cropping and voting within his own version of the plugin.

My version, "Link Bar" is very basic, but very extendible. By default, it shows your site name, link to the post on your site, and a link to close the bar. The CSS is separate from the usual merged archive file that hotaru uses since most of that is unnecessary for a simple bar, but I've included a way to override the default link_bar.css file if you copy it to your own theme folder and edit it there. The link_bar_top.php template can also be customized in teh usual way, by copying it to your theme.

Plugin Developers
-----------------
Plugin developers can easily extend Link Bar with these hooks:
1. $h->pluginHook('link_bar_pre_template'); // in the code, immediately before showing the template file
2. $h->pluginHook('link_bar_top'); // at the very top of the template, before anything is output to the screen
3. $h->pluginHook('header_meta'); // the same hook that other plugins use to change meta descriptions and keywords
4. $h->pluginHook('link_bar_css_js'); // include your own css or javascript files
5. $h->pluginHook('link_bar'); // after the opening BODY tag
6. $h->pluginHook('link_bar_logo'); // within the logo section
7. $h->pluginHook('link_bar_post'); // within the post section
8. $h->pluginHook('link_bar_more'); // within the more section
9. $h->pluginHook('link_bar_end'); // before the closing BODY tag

Hooks 5-8 return $result. If $result is empty, the default section will show (i.e. whole bar, logo, post, or more). If a positive value is returned to those hooks, then the defaults will not be shown, i.e plugins will override the defaults.

Instructions
------------
1. Upload the "link_bar" folder to your plugins folder.
2. Install it from Plugin Management in Admin.
3. If you want RSS links to use the link bar, you need to enable RSS forwarding in Admin -> Plugin Settings -> SB Base 

Changelog
---------
v.0.1 2010/3/23 - Nick - Released first version (based on "SocialBar" by shibuya246) 
