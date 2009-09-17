<?php
/**
 * USERS LANGUAGE
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
$lang["users_settings_header"] = "Users Configuration";
$lang["users_settings_instructions"] = "Configure the users plugin with these options:";
$lang["users_settings_registration"] = "Registration Options";
$lang["users_settings_save"] = "Save";
$lang["users_settings_recaptcha_enable"] = "Enable reCaptcha - Get keys from";
$lang["users_settings_recaptcha_public_key"] = "Public Key";
$lang["users_settings_recaptcha_private_key"] = "Private Key";
$lang["users_settings_email_conf"] = "Enable email confirmation";
$lang["users_settings_no_keys"] = "Settings saved, but reCaptcha won't work without both keys!";
$lang["users_settings_saved"] = "Settings Saved";

/* Navigation & General */
$lang["users_home"] = "Home";
$lang["users_account"] = "Account";
$lang["users_login"] = "Login";
$lang["users_logout"] = "Logout";
$lang["users_register"] = "Register";
$lang["users_admin"] = "Admin";

/* Login */
$lang["users_login_instructions"] = "Enter your username and password to login:";
$lang["users_login_failed"] = "Login failed";
$lang["users_login_failed_email_not_validated"] = "Sorry, you haven't validated your email yet."; 
$lang["users_login_failed_email_request_sent"] = "Email confirmation request sent!";
$lang["users_login_form_submit"] = "Login";
$lang["users_login_form_submit_username"] = "Username:";
$lang["users_login_form_submit_password"] = "Password:";
$lang["users_login_form_submit_remember"] = "Remember:";
$lang["users_login_forgot_password"] = "Forgot your password?";
$lang['users_login_forgot_password_submit'] = "Submit";
$lang['users_login_forgot_password_submit_instruct_1'] = "Enter the email address for your " . SITE_NAME . " account:";
$lang['users_login_forgot_password_submit_instruct_2'] = "You will be emailed a confirmation code. Click that confirmation code to be sent a new, random password. Use that to access your account and change it to something you can remember.";

/* Forgotten Password */
$lang['users_email_password_conf_sent'] = "An email has been sent to the address provided";
$lang['users_email_password_conf_success'] = "A new password has been sent to your email address.";
$lang['users_email_password_conf_fail'] = "Unable to confirm your email address. Please try the link again.";
$lang["users_email_invalid"] = "Email invalid";

/* Register (and also used in User Account) */
$lang["users_register_username"] = "Username:";
$lang["users_register_email"] = "Email:";
$lang["users_register_password"] = "Password:";
$lang["users_register_password_verify"] = "Password (again):";
$lang["users_register_instructions"] = "Enter your username, email and password to register:";
$lang["users_register_username_error"] = "Your username must be at least 4 characters and can contain letters, dashes and underscores only";
$lang["users_register_password_error"] = "The password must be at least 8 characters and can only contain letters, numbers and these symbols: @ * # - _";
$lang['users_register_password_match_error'] = "The password fields don't match";
$lang["users_register_email_error"] = "That doesn't parse as a valid email address";
$lang["users_register_id_exists"] = "There's been a problem with id numbers";
$lang["users_register_username_exists"] = "That username has been taken";
$lang["users_register_email_exists"] = "That email address is already in use";
$lang['users_register_recaptcha_error'] = "Please confirm your password and try the captcha again";
$lang['users_register_recaptcha_empty'] = "Please confirm your password and complete the captcha";
$lang["users_register_unexpected_error"] = "Sorry, there's been an unexpected error";
$lang["users_register_make_note"] = "Make a note of your new username, email and password before clicking \"Next\"...";
$lang["users_register_form_submit"] = "Register";
$lang['users_register_emailconf_sent'] = "An email has been sent to the address provided. <br />Please confirm it to complete registration. Thank you!";
$lang['users_register_emailconf_subject'] = SITE_NAME . " Registration"; 
$lang['users_register_emailconf_body_hello'] = "Hi";
$lang['users_register_emailconf_body_welcome'] = "Thank you for registering at " . SITE_NAME . ".";
$lang['users_register_emailconf_body_click'] = "Your email address must be validated before you can log in. Please click this url or copy it into your browser:";
$lang['users_register_emailconf_body_regards'] = "Regards,";
$lang['users_register_emailconf_body_sign'] = SITE_NAME . " Admin";
$lang['users_register_emailconf_success'] = "Your email address has been successfully confirmed.";
$lang['users_register_emailconf_success_login'] = "Click here to log in.";
$lang['users_register_emailconf_fail'] = "Unable to confirm your email address. Please try the link again.";

/* User Account Update */
$lang["users_account_user_settings"] = "User Account";
$lang["users_account_username"] = "Username:";
$lang["users_account_email"] = "Email:";
$lang["users_account_password_instruct"] = "Change your password?";
$lang["users_account_old_password"] = "Old password:";
$lang["users_account_new_password"] = "New password:";
$lang["users_account_new_password_verify"] = "New password (again):";
$lang["users_account_success"] = "Updated successfully.";
$lang["users_account_instructions"] = "Update your account information:";
$lang["users_account_form_submit"] = "Update";
// Other language is used in content/language_packs/language_default/admin_language
?>