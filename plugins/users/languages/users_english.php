<?php

// USERS

/* **************************************************************************************************** 
 *  File: plugins/users/languages/users_english.php
 *  Purpose: A language file for Users. It's used whenever a Users file needs to output language.
 *  Notes: ---
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

/* Login */
$lang["users_login"] = "Login";
$lang["users_login_instructions"] = "Enter your username and password to login:";
$lang["users_login_failed"] = "Login failed";
$lang["users_login_form_submit"] = "Login";

/* Register (and also used in User Settings) */
$lang["users_register"] = "Register";
$lang["users_register_instructions"] = "Enter your username, email and password to register:";
$lang["users_register_username_error"] = "Your username must be at least 4 characters and can contain letters, dashes and underscores only.";
$lang["users_register_password_error"] = "The password must be at least 8 characters and can only contain letters, numbers and these symbols: @ * # - _";
$lang["users_register_email_error"] = "That doesn't parse as a valid email address.";
$lang["users_register_id_exists"] = "There's been a problem with id numbers.";
$lang["users_register_username_exists"] = "That username has been taken.";
$lang["users_register_email_exists"] = "That email address is already in use.";
$lang["users_register_unexpected_error"] = "Sorry, there's been an unexpected error.";
$lang["users_register_make_note"] = "Make a note of your new username, email and password before clicking \"Next\"...";
$lang["users_register_form_submit"] = "Register";

/* User Settings */
$lang["users_update_success"] = "Updated successfully.";
$lang["users_update_instructions"] = "Update your account information:";
$lang["users_update_form_submit"] = "Update";

?>