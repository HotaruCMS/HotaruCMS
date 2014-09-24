<?php
/* users language */

/* Users Admin Settings */
$lang["users_settings_header"] = "Users Configuration";
$lang["users_settings_instructions"] = "Configure the users plugin with these options:";
$lang["users_settings_registration"] = "Registration Options";
$lang["users_settings_save"] = "Save";
$lang["users_settings_recaptcha_enable"] = "Enable reCaptcha - Get keys from";
$lang["users_settings_recaptcha_public_key"] = "Public Key";
$lang["users_settings_recaptcha_private_key"] = "Private Key";
$lang["users_settings_email_conf"] = "Send new users an email with a link to confirm their registration";
$lang["users_settings_reg_status"] = "Role for new users:";
$lang["users_settings_reg_status_member"] = "Regular Member";
$lang["users_settings_reg_status_undermod"] = "Under Moderation";
$lang["users_settings_reg_status_pending"] = "Pending Approval";
$lang["users_settings_email_notify"] = "When a new user registers, email admins, supermods and moderators with \"access admin\" permissions:";
$lang["users_settings_email_notify_all"] = "All new users";
$lang["users_settings_email_notify_pending"] = "Pending users only";
$lang["users_settings_email_notify_none"] = "None";
$lang["users_settings_no_keys"] = "Settings saved, but reCaptcha won't work without both keys!";
$lang["users_settings_saved"] = "Settings Saved";

/* User Navigation */
$lang["users_all_posts"] = "Posts";
$lang["users_all_comments"] = "Comments";
$lang["users_edit"] = "Edit";
$lang["user_man_link"] = "User Manager";

/* User Account Update */
$lang["users_account"] = "Account";
$lang["users_account_user_role"] = "Role";
$lang["users_account_username"] = "Username:";
$lang["users_account_email"] = "Email:";
$lang["users_account_role"] = "Role:";
$lang["users_account_role_note"] = "<small><b>Important</b>: Changing role will reset user permissions.</small>";
$lang["users_account_password_instruct"] = "Change your password?";
$lang["users_account_old_password"] = "Old password:";
$lang["users_account_new_password"] = "New password:";
$lang["users_account_new_password_verify"] = "New password (again):";
$lang["users_account_success"] = "Updated successfully.";
$lang["users_account_update"] = "Update";
$lang["users_account_deleted"] = "User Deleted";
$lang["users_account_admin_admin"] = "Sorry, only admins can access admin accounts.";
$lang["users_account_username_requirements"] = "At least 4 characters, using only letters, dashes and underscores";
$lang["users_account_password_requirements"] = "At least 8 characters, using only letters, numbers and these symbols: ! @ * # - _";
// Other language is used in content/language_packs/language_default/admin_language

/* User Edit Profile */
$lang["users_profile"] = "Profile";
$lang["users_profile_edit"] = "Edit Profile";
$lang["users_profile_edit_update"] = "Update";
$lang["users_profile_edit_bio"] = "Bio:";
$lang["users_profile_edit_saved"] = "Profile updates saved.";
$lang["users_profile_edit_view_profile"] = "Click here to view your profile.";
$lang['users_profile_default_bio'] = "No introduction yet.";

/* User Settings */
$lang["users_settings"] = "Settings";
$lang["users_settings_update"] = "Update";
$lang['users_settings_yes'] = "Yes";
$lang['users_settings_no'] = "No";
$lang["users_settings_saved"] = "User settings saved.";

/* User Permissions */
$lang["users_permissions"] = "Permissions";
$lang["users_permissions_update"] = "Update";
$lang['users_permissions_updated'] = "Permissions Updated";

/* Admin Stats */
$lang["users_admin_stats_users"] = "Users";
$lang["users_admin_stats_all"] = "Total users";
$lang["users_admin_stats_admin"] = "Admins";
$lang["users_admin_stats_supermod"] = "Super Mods";
$lang["users_admin_stats_moderator"] = "Moderators";
$lang["users_admin_stats_member"] = "Members";
$lang["users_admin_stats_pending"] = "Pending";
$lang["users_admin_stats_undermod"] = "Under moderation";
$lang["users_admin_stats_banned"] = "Banned";
$lang["users_admin_stats_killspammed"] = "Killspammed";

/* UserFunctions CLASS: */

/* NOTIFY MODS BY EMAIL */

/* Posts */
$lang['users_posts'] = "Posts";
$lang['userfunctions_notifymods_subject_post'] = "New post submitted at " . SITE_NAME;
$lang['userfunctions_notifymods_body_about_post'] = "A new post has been submitted at " . SITE_NAME;
$lang['userfunctions_notifymods_body_post_status'] = "Post Status: ";
$lang['userfunctions_notifymods_body_post_title'] = "Post Title: "; 
$lang['userfunctions_notifymods_body_post_content'] = "Post Content: "; 
$lang['userfunctions_notifymods_body_post_status'] = "Post Status: ";
$lang['userfunctions_notifymods_body_post_page'] = "Post Page: "; 
$lang['userfunctions_notifymods_body_post_orig'] = "Original Post: "; 
$lang['userfunctions_notifymods_body_post_edit'] = "Edit Post: "; 
$lang['userfunctions_notifymods_body_post_management'] = "Post Management: ";

/* Users */
$lang['userfunctions_notifymods_subject_user'] = "New user registered at " . SITE_NAME;
$lang['userfunctions_notifymods_body_about_user'] = "A new user has registered at " . SITE_NAME;
$lang['userfunctions_notifymods_body_user_name'] = "User Name: ";
$lang['userfunctions_notifymods_body_user_email'] = "User Email: ";
$lang['userfunctions_notifymods_body_user_role'] = "User Role: ";
$lang['userfunctions_notifymods_body_user_account'] = "User Account: ";
$lang['userfunctions_notifymods_body_user_management'] = "User Management: ";

/* Comments */
$lang['userfunctions_notifymods_subject_comment'] = "New comment posted at " . SITE_NAME;
$lang['userfunctions_notifymods_body_about_comment'] = "A new comment has been posted at " . SITE_NAME; 
$lang['userfunctions_notifymods_body_comment_status'] = "Comment Status: ";
$lang['userfunctions_notifymods_body_comment_content'] = "Comment Content: "; 
$lang['userfunctions_notifymods_body_comment_management'] = "Comment Management: ";

/* Common */
$lang['userfunctions_notifymods_hello'] = "Hi ";
$lang['userfunctions_notifymods_body_click'] = "More details can be found here: ";
$lang['userfunctions_notifymods_body_regards'] = "Thank you,";
$lang['userfunctions_notifymods_body_sign'] = SITE_NAME . " Admin";

/* User Meta Tags */
$lang['users_default_meta_description_before'] = "I'm ";
$lang['users_default_meta_description_after'] = " and this is my profile on " . SITE_NAME;
$lang['users_profile_meta_keywords_more'] = ", profile, activity"; // you need the comma because the user's name is the first keyword 
$lang['users_meta_description_results_before'] = "";
$lang['users_meta_description_results_middle'] = " by ";
$lang['users_meta_description_results_after'] = " on " . SITE_NAME;
$lang['users_meta_description_popular'] = "Popular posts";

/* RSS */
$lang["post_rss_from_user"] = "Stories submitted by";
?>