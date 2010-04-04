TOS AntiSpam Plugin for Hotaru CMS
---------------------------------
Created by: Nick Ramsay

Description
-----------
This plugin adds a checkbox to the registration form with the text "I agree to the Terms of Service". It also adds a customizable anti-spam question with dropdown list of answers to choose from. These options can also be added to Submit step 2, and limited to a user's first X posts.

Instructions
------------
1. Add the following plugin hook in the user_signin_register.php template above the ReCaptcha code. If you are using the RPX plugin, add the same hook in the same place in the rpx_register.php template. Note: This hook will be added to those plugins by default when they are next upgraded, so it's okay to add them to the original templates.

<?php $h->pluginHook('user_signin_register_register_form'); ?>

2. Upload the "tos_antispam" folder to your plugins folder. Install it from Plugin Management in Admin.
3. Choose your settings in Admin -> Plugin Settings -> TOS AntiSpam

Extras
------
If you'd like to link the "Terms of Service" text to your TOS, you can add the link around the text in the plugin's language file.

Changelog
---------
v.0.2 2010/04/02 - Nick - Fix for being able to skip the captcha question in post submission
v.0.1 2010/02/25 - Nick - Released first version
