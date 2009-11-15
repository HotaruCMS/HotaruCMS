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
You have received this email because you are registered at (NAME OF YOUR SITE).

Note: In the near future, Hotaru will have user-defined settings so users can opt out of receiving emails, etc. 

Changelog
---------
v.0.1 2009/11/15 - Nick - Released first version