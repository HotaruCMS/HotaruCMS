Admin Email Plugin for Hotaru CMS
-----------------------------------
Created by: Nick Ramsay

Description
-----------
Send an email to all users, user groups or individual users. Each user receives an individually addressed email, and those emails are sent out in batches with an admin-specified time delay between them. Includes a simulation mode.

Instructions
------------
1. Upload the "admin_email" folder to your plugins folder. 
2. Install it from Plugin Management in Admin. 
3. Click Admin Email in the Admin sidebar to access it.

Usage
-----
1. Choose recipients by clicking a user group or name from the list. You can select more than one group or user by holding down the Ctrl key when you click. Use the Shift key to select a range of recipients.
2. Use {username} in the subject or body of the email. This is automatically replaced by the user's name.
3. By default, the following text is attached to the bottom of each email. You can change it in admin_email/languages/admin_email_languages.php

-----------------------
You have received this email because you are registered at YOUR SITE. You can opt out of receiving these emails from your settings page: [link to user's settings page]

Changelog
---------
v.0.3 2009/12/31 - Nick - Compatibility with Hotaru 1.0
v.0.2 2009/11/26 - Nick - Bug fix for default user settings
v.0.1 2009/11/23 - Nick - Released first version