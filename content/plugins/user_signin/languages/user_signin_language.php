<?php
/**
 * USER SIGNIN LANGUAGE
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

/* Users Admin Settings */
$lang["user_signin_settings_header"] = "User Signin Configuration";
$lang["user_signin_settings_instructions"] = "Configure the users plugin with these options:";
$lang["user_signin_settings_registration"] = "Registration Options";
$lang["user_signin_settings_save"] = "Save";
$lang["user_signin_settings_recaptcha_enable"] = "Enable reCaptcha <small>(Requires the reCaptcha plugin)</small>";
$lang["user_signin_settings_email_conf"] = "Send new users an email with a link to confirm their registration";
$lang["user_signin_settings_reg_status"] = "Role for new users:";
$lang["user_signin_settings_reg_status_member"] = "Regular Member";
$lang["user_signin_settings_reg_status_undermod"] = "Under Moderation";
$lang["user_signin_settings_reg_status_pending"] = "Pending Approval";
$lang["user_signin_settings_email_notify"] = "When a new user registers, email admins, supermods and moderators with \"access admin\" permissions:";
$lang["user_signin_settings_email_notify_all"] = "All new users";
$lang["user_signin_settings_email_notify_pending"] = "Pending users only";
$lang["user_signin_settings_email_notify_none"] = "None";
$lang["user_signin_settings_no_keys"] = "Settings saved, but reCaptcha won't work without both keys!";
$lang["user_signin_settings_saved"] = "Settings Saved";

/* Navigation & General */
$lang["user_signin_home"] = "Home";
$lang["user_signin_account"] = "Account";
$lang["user_signin_login"] = "Login";
$lang["user_signin_logout"] = "Logout";
$lang["user_signin_register"] = "Register";
$lang["user_signin_admin"] = "Admin";
$lang["user_signin_access_denied"] = "Sorry, you don't have permission to view this page.";
$lang['user_signin_please_log_in'] = "Please log in and try again";

/* Login */
$lang["user_signin_login_instructions"] = "Enter your username and password to login:";
$lang["user_signin_login_failed"] = "Login failed";
$lang["user_signin_login_failed_email_not_validated"] = "Sorry, you haven't validated your email yet."; 
$lang["user_signin_login_failed_email_request_sent"] = "Email confirmation request sent!";
$lang["user_signin_login_failed_no_permission"] = "Sorry, you don't have permission to login.";
$lang["user_signin_login_failed_not_approved"] = "Sorry, you can't login until a moderator has approved your account.";
$lang["user_signin_login_form_submit"] = "Login";
$lang["user_signin_login_form_submit_username"] = "Username:";
$lang["user_signin_login_form_submit_password"] = "Password:";
$lang["user_signin_login_form_submit_remember"] = "Remember:";
$lang["user_signin_login_forgot_password"] = "Forgot your password?";
$lang['user_signin_login_forgot_password_submit'] = "Submit";
$lang['user_signin_login_forgot_password_submit_instruct_1'] = "Enter the email address for your " . SITE_NAME . " account:";
$lang['user_signin_login_forgot_password_submit_instruct_2'] = "You will be emailed a confirmation code. Click that confirmation code to be sent a new, random password. Use that to access your account and change it to something you can remember.";

/* Forgotten Password */
$lang['user_signin_email_password_conf_sent'] = "An email has been sent to the address provided";
$lang['user_signin_email_password_conf_success'] = "A new password has been sent to your email address.";
$lang['user_signin_email_password_conf_fail'] = "Unable to confirm your email address. Please try the link again.";
$lang["user_signin_email_invalid"] = "Email invalid";
$lang["user_signin_account_email"] = "Email:";

/* Register (and also used in User Account) */
$lang["user_signin_register_username"] = "Username:";
$lang["user_signin_register_email"] = "Email:";
$lang["user_signin_register_password"] = "Password:";
$lang["user_signin_register_password_verify"] = "Password (again):";
$lang["user_signin_register_instructions"] = "Enter your username, email and password to register:";
$lang["user_signin_register_username_error"] = "Your username must be at least 4 characters and can contain letters, dashes and underscores only";
$lang["user_signin_register_username_error_short"] = "At least 4 characters, using only letters, dashes and underscores";
$lang["user_signin_register_password_error"] = "The password must be at least 8 characters and can only contain letters, numbers and these symbols: ! @ * # - _";
$lang["user_signin_register_password_error_short"] = "At least 8 characters, using only letters, numbers and these symbols: ! @ * # - _";
$lang['user_signin_register_password_match_error'] = "The password fields don't match";
$lang["user_signin_register_email_error"] = "That doesn't parse as a valid email address";
$lang["user_signin_register_id_exists"] = "There's been a problem with id numbers";
$lang["user_signin_register_username_exists"] = "That username has been taken";
$lang["user_signin_register_email_exists"] = "That email address is already in use";
$lang['user_signin_register_user_blocked'] = "Sorry, you are on the blocked list. Please contact the site administrator.";
$lang['user_signin_register_recaptcha_error'] = "Please confirm your password and try the captcha again";
$lang['user_signin_register_recaptcha_empty'] = "Please confirm your password and complete the captcha";
$lang["user_signin_register_unexpected_error"] = "Sorry, there's been an unexpected error";
$lang["user_signin_register_make_note"] = "Make a note of your new username, email and password before clicking \"Next\"...";
$lang["user_signin_register_form_submit"] = "Register";
$lang['user_signin_register_emailconf'] = "Email Confirmation";
$lang['user_signin_register_emailconf_sent'] = "An email has been sent to the address provided. <br />Please confirm it to complete registration. <br />Thank you!";
$lang['user_signin_register_emailconf_subject'] = SITE_NAME . " Registration"; 
$lang['user_signin_register_emailconf_body_hello'] = "Hi";
$lang['user_signin_register_emailconf_body_welcome'] = "Thank you for registering at " . SITE_NAME . ".";
$lang['user_signin_register_emailconf_body_click'] = "Your email address must be validated before you can log in. Please click this url or copy it into your browser:";
$lang['user_signin_register_emailconf_body_regards'] = "Regards,";
$lang['user_signin_register_emailconf_body_sign'] = SITE_NAME . " Admin";
$lang['user_signin_register_emailconf_success'] = "Your email address has been successfully confirmed.";
$lang['user_signin_register_emailconf_success_login'] = "Click here to log in.";
$lang['user_signin_register_emailconf_fail'] = "Unable to confirm your email address. Please try the link again.";

?>