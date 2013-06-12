<?php
/* **************************************************************************************************** 
 * MAIN LANGUAGE
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

/* header */
$lang["header_meta_description"] = "Hotaru CMS is an open source content management system, written in PHP.";
$lang["header_meta_keywords"] = "hotaru, cms, platform, content, open source";

/* Forms */
$lang['main_form_submit'] = "Submit";
$lang['main_form_update'] = "Update";
$lang['main_form_save'] = "Save";
$lang['main_form_confirm'] = "Confirm";
$lang['main_form_ok'] = "OK";
$lang['main_form_next'] = "Next";
$lang['main_form_edit'] = "Edit";

/* Denied Access */
$lang['main_access_denied'] = "Sorry, you don't have permission to view this page.";

/* Settings */
$lang["main_settings_saved"] = "Settings Saved.";
$lang["main_settings_not_saved"] = "Error! Settings not saved";

/* Users */
$lang['main_userbase_cookie_error'] = "Error setting cookie. Username not provided.";

/* Debug / Maintenance */
$lang['main_hotaru_db_queries'] = "Database queries: ";
$lang['main_hotaru_page_load_time'] = "Page load time: ";
$lang['main_hotaru_memory_usage'] = "Memory usage: ";
$lang['main_hotaru_php_version'] = "PHP v.";
$lang['main_hotaru_mysql_version'] = "MySQL v.";
$lang['main_hotaru_hotaru_version'] = "<a href='http://hotarucms.org' title='HotaruCMS.org'>Hotaru CMS</a> v.";
$lang['main_hotaru_site_closed'] = SITE_NAME . " is undergoing maintenance.<br />Please come back later.";

/* Announcements */
$lang['main_announcement_users_disabled'] = "Login and registration is currently disabled.";
$lang['main_announcement_plugins_disabled'] = "All plugins are currently disabled.";
$lang['main_announcement_site_closed'] = SITE_NAME . " is currently closed!";

/* Times */
$lang['main_times_days'] = "days";
$lang['main_times_day'] = "day";
$lang['main_times_hours'] = "hrs";
$lang['main_times_hour'] = "hr";
$lang['main_times_minutes'] = "mins";
$lang['main_times_minute'] = "min";
$lang['main_times_seconds'] = "a few seconds";
$lang['main_times_secs'] = "s";

/* Pagination */
$lang['pagination_first'] = "First";
$lang['pagination_last'] = "Last";
$lang['pagination_previous'] = "Previous";
$lang['pagination_next'] = "Next";

/* Errors */
$lang["main_theme_page_not_found"] = "Page not found";
$lang['error_csrf'] = "CSRF error. Please try again.";

/* navigation */
$lang["main_theme_navigation_home"] = "Home";
$lang["main_theme_navigation_admin"] = "Admin";
$lang["main_theme_navigation_login"] = "Login";
$lang["main_theme_navigation_logout"] = "Logout";

/* login */
$lang["main_theme_login_username"] = "Username";
$lang["main_theme_login_password"] = "Password";
$lang["main_theme_login_form_submit"] = "Submit";
$lang["main_theme_login_forgot_password"] = "Forgotten Password";
$lang["main_theme_navigation_logout"] = "Logout";
$lang["main_theme_button_admin_login"] = "Admin Login";

/* breadcrumbs */
$lang['main_theme_breadcrumbs_home'] = "Home";

/* footer */
$lang["main_theme_footer_hotaru_link"] = "HotaruCMS.org";

/* Forgotten Password */
$lang['main_user_email_password_conf_sent'] = "An email has been sent to the address provided";
$lang['main_user_email_password_conf_subject'] = SITE_NAME . " Password Reset Request"; 
$lang['main_user_email_new_password_subject'] = "Your New Password for " . SITE_NAME; 
$lang['main_user_email_password_conf_body_hello'] = "Hi";
$lang['main_user_email_password_conf_body_welcome'] = "If you have requested a new password at " . SITE_NAME . ", ";
$lang['main_user_email_password_conf_body_click'] = "click this url or copy it into your browser:";
$lang['main_user_email_password_conf_body_no_request'] = "If you have NOT requested a new password, please ignore this email.";
$lang['main_user_email_password_conf_body_regards'] = "Regards,";
$lang['main_user_email_password_conf_body_sign'] = SITE_NAME . " Admin";
$lang['main_user_email_password_conf_success'] = "A new password has been sent to your email address.";
$lang['main_user_email_password_conf_fail'] = "Unable to confirm your email address. Please try the link again.";
$lang['main_user_email_password_conf_body_requested'] = "Here is the new password you requested at " . SITE_NAME . ": ";
$lang['main_user_email_password_conf_body_remember'] = "Please make a note of it and use it next time you log in.";
$lang['main_user_email_password_conf_body_pass_change'] = "You can change it to something more memorable from your account page.";

/* Account */
$lang["main_user_account_update_success"] = "Updated successfully";
$lang["main_user_account_update_password_success"] = "Updated password successfully";
$lang["main_user_account_update_unexpected_error"] = "Sorry, there has been an unexpected error";
$lang["main_user_account_update_password_error_old"] = "Your old password doesn't match our records";
$lang["main_user_account_update_password_error_new"] = "The new password must be at least 8 characters and can only contain letters, numbers and these symbols: ! @ * # - _";
$lang["main_user_account_update_password_error_match"] = "The two \"New password\" fields don't match";
$lang["main_user_account_update_password_error_not_provided"] = "Please fill in all the password fields with at least 8 letters, numbers and these symbols: ! @ * # - _";
$lang["main_user_account_update_username_error"] = "Your username must be at least 4 characters and can contain letters, dashes and underscores only";
$lang["main_user_account_update_password_error"] = "The password must be at least 8 characters and can only contain letters, numbers and these symbols: ! @ * # - _";
$lang["main_user_account_username_requirements"] = "At least 4 characters, using only letters, dashes and underscores";
$lang["main_user_account_password_requirements"] = "At least 8 characters, using only letters, numbers and these symbols: ! @ * # - _";
$lang['main_user_account_update_password_match_error'] = "The password fields don't match";
$lang["main_user_account_update_email_error"] = "That doesn't parse as a valid email address";
$lang["main_user_account_update_username_exists"] = "Sorry, that username is already being used";
$lang["main_user_account_update_email_exists"] = "Sorry, that email address is already being used";
$lang["main_user_theme_account"] = "Account";
$lang["main_user_theme_account_instructions"] = "Update your account information:";
$lang["main_user_theme_update_username"] = "Username:";
$lang["main_user_theme_update_email"] = "Email:";
$lang["main_user_theme_update_password_instruct"] = "Change your password?";
$lang["main_user_theme_update_old_password"] = "Old password:";
$lang["main_user_theme_update_new_password"] = "New password:";
$lang["main_user_theme_update_new_password_verify"] = "New password (again):";
$lang["main_user_theme_update_form_submit"] = "Update";

/* Avatar */
$lang["main_anonymous"] = "Anonymous";

/* Startup */
$lang["main_welcome"] = "Welcome to Hotaru CMS";
$lang["main_welcome_looking_forward"] = "We are looking forward to seeing what you create with your new website";
$lang["main_welcome_getting_started"] = "It looks like you are just getting started with your new website";
$lang["main_welcome_install_plugins"] = "Time to install some plugins and open your site";

?>