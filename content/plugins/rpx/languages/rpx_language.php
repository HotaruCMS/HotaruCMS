<?php
/**
 * RPX LANGUAGE
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

/* Users navigation */
$lang['rpx_navigation_signin'] = "Sign in";

/* Users login */
$lang['rpx_login_sign_in_1'] = "If you joined with Twitter, Facebook, Google, etc.";
$lang['rpx_login_sign_in_2'] = "click here to sign in!";

/* Users registration */
$lang['rpx_register_sign_up'] = "Click here to sign up with Twitter, Facebook, Google and more!";

/* Users account */
$lang['rpx_account_providers'] = "Providers";
$lang['rpx_account_providers_list'] = "Your " . SITE_NAME . " account is associated with: ";
$lang['rpx_account_no_providers'] = "<i>Your account is not associated with any providers (e.g. Twitter or Facebook)</i>";
$lang['rpx_account_add_provider'] = "Add a provider...";
$lang['rpx_account_add_provider_instruct'] = "<small><i>Note:</i> You may need to click \"This isn't me\" to show the choices.</small>";

/* RPX Registration */
$lang["rpx_registration_nearly_complete"] = "Nearly done, just a couple more things...";
$lang["rpx_register_instructions"] = "You can change these later...";
$lang["rpx_register_captcha"] = "Please complete this captcha:";
$lang['rpx_users_register_exists_error_1_rpx'] = "It looks like you may have registered with";
$lang['rpx_users_register_exists_error_2_rpx'] = "Try logging in with that!";
$lang['rpx_users_register_exists_error_3_rpx'] = "You can add more providers from your \"Account\" page.";
$lang['rpx_users_register_exists_error_1_password'] = "It looks like you may have already registered.";
$lang['rpx_users_register_exists_error_2_password'] = "Click here to login with your username and password.";
$lang['rpx_users_register_exists_error_3_password'] = "You can change to a 3rd party provider from your \"Account\" page.";

/* RPX Admin Settings */
$lang["rpx_settings_header"] = "RPX Configuration";
$lang["rpx_settings_instructions"] = "Before you can use this plugin, you'll need to set up an account at <a href='http://rpxnow.com'>http://rpxnow.com</a>.";
$lang["rpx_settings_application"] = "What's the name of you application? If it's <i>My Website</i> then enter <code>my-website</code>.";
$lang["rpx_settings_api_key"] = "What's your RPX API key?";
$lang["rpx_settings_language"] = "What language do you want to use? <small>(See <a href='https://rpxnow.com/docs#sign-in_localization'>Sign-In Interface Localization</a>)</small>";
$lang["rpx_settings_language_default"] = "<small>(default: en)</small>";
$lang["rpx_settings_account"] = "RPX account type:";
$lang["rpx_settings_account_desc"] = "<small>(Plus or Pro accounts include <a href='https://rpxnow.com/features#account_mapping'>Account Mapping</a>)</small>";
$lang["rpx_settings_display"] = "RPX display type:";
$lang["rpx_settings_display_desc"] = "You can (1) embed the RPX widget in the login and registration pages, (2) overlay the screen with the widget from links in the login and registration pages, or (3) replace the login and registration pages completely, and use a \"Sign in\" link in the navigation bar to overlay the screen with the widget. See the forums for more details.</small>";
$lang["rpx_settings_error_application"] = "There's a problem with the application name";
$lang["rpx_settings_error_api_key"] = "There's a problem with the API key";
$lang["rpx_settings_error_language"] = "There's a problem with the language";

?>