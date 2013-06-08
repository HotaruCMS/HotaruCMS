<?php

/* ****************************************************************************************************
 *  File: /install/install_language.php
 *  Purpose: A language file for Install. It's used whenever the install file needs to output language.
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
 *   Copyright (c) 2010 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

/* Upgrade Step 1 */
$lang["upgrade_title"] = "Hotaru CMS Upgrade";

$lang["upgrade_step1"] = "Step 1/3: Checking your existing setup";
$lang["upgrade_step1_details"] = "To upgrade Hotaru to version " . $h->version . ", click 'Next'...";
$lang["upgrade_step1_old_version"] = "You are currently running Hotaru CMS version ";
$lang["upgrade_step1_old_no_version"] = "We could not find an existing version number of Hotaru CMS in your database.";
$lang["upgrade_step1_current_version"] = "You already have the latest version of Hotaru installed.<br/>If you want to run the upgrade script again click 'Next', otherwise you can close this browser window now.";

/* Upgrade Step 2 */
$lang["upgrade_step2"] = "Step 2/3: Upgrade Database";
$lang["upgrade_step2_details"] = "Congratulations! You have successfully upgraded Hotaru CMS.";

/* Upgrade Step 2 */
$lang["upgrade_step3"] = "Step 3/3: Check Plugins, Templates";
$lang["upgrade_step3_details"] = "You have successfully upgraded Hotaru CMS.";
$lang["upgrade_step3_instructions"] = "After clicking \"Finish\" you may find some of your plugins need upgrading. You can check the latest version numbers from the Plugin Management page in your admin dashboard. You may also need to modify any templates you have customised to make sure they work with the latest version of Hotaru CMS.";
$lang["upgrade_step3_go_play"] = "Click \"Finish\" to access your Hotaru site!";
$lang["upgrade_home"] = "Finish";

/* Install Common */
$lang["install_title"] = "Hotaru CMS Setup";
$lang["admin_theme_header_hotarucms"] = "Hotaru CMS ";
$lang["install_next"] = "Next";
$lang["install_back"] = "Back";
$lang["install_trouble"] = "Having trouble? Read the <a href='http://docs.hotarucms.org'>Documentation</a> or ask for help in the <a href='http://forums.hotarucms.org'>Forums</a>.";

/* Install Step 1 */
$lang["install_step0"] = "Welcome to Hotaru CMS";
$lang["install_step0_welcome"] = "Installing Hotaru can be completed in just 4 steps and normally requires just a few minutes.";
$lang["install_step0_select"] = "Select below whether you want to make a new installation or upgrade an existing Hotaru site...";
$lang["install_new"] = "New Installation";
$lang["install_new2"] = "Install";
$lang["install_upgrade"] = "Upgrade Existing Site";
$lang["install_upgrade2"] = "Upgrade";

/* Install Step 2 */
$lang["install_step1"] = "Step 1/4: Database Setup";
$lang["install_step1_instructions"] = "To set up a database for Hotaru CMS, you'll need to do the following";
$lang["install_step1_instructions1"] = "Create a database called <i>hotaru</i> in your web host's control panel. Make a note of your username and password!";
$lang["install_step1_instructions2"] = "Copy <code>settings_default.php</code> in the config folder and rename it <code>settings.php</code>.";
$lang["install_step1_instructions3"] = "Open <code>settings.php</code> and fill in the \"Database Details\" section.";
$lang["install_step1_instructions4"] = "Fill in the <code>baseurl</code>, e.g. <i>http://example.com/</i>. Don't forget the trailing slash (/)";
$lang["install_step1_instructions5"] = "Save and upload <code>settings.php</code> to your server in the config folder, then click \"Next\"...";
$lang["install_step1_instructions_create_db"] = "Create a new database on your server first then fill in the form below. These details will be unique to your server and database setup.";
$lang["install_step1_instructions_manual_setup"] = "If you prefer to edit the settings file manually";
$lang["install_step1_instructions_manual_setup_click"] = "click here";
$lang["install_step1_warning"] = "<b>Warning</b>";
$lang["install_step1_warning_note"] = "When you click \"Next\", new database tables will be created, deleting any old ones you may have!";

$lang["install_step1_baseurl"] = "<b>Baseurl</b>";
$lang["install_step1_baseurl_explain"] = "e.g. http://example.com/ (Needs trailing slash '/')";
$lang["install_step1_dbuser"] = "<b>Database User</b>";
$lang["install_step1_dbuser_explain"] = "Add your own database details ";
$lang["install_step1_dbpassword"] = "<b>Database Password</b>";
$lang["install_step1_dbpassword_explain"] = "";
$lang["install_step1_dbname"] = "<b>Database Name</b>";
$lang["install_step1_dbname_explain"] = "";
$lang["install_step1_dbprefix"] = "<b>Database Prefix</b>";
$lang["install_step1_dbprefix_explain"] = "Database prefix, e.g. 'hotaru_'";
$lang["install_step1_dbhost"] = "<b>Database Host</b>";
$lang["install_step1_dbhost_explain"] = "You probably won't need to change this";

$lang["install_step1_dbpassword_error"] = "There was a problem with the password you entered.";
$lang["install_step1_baseurl_error"] = "There was a problem with the baseurl";
$lang["install_step1_dbuser_error"] = "There was a problem with the database user";
$lang["install_step1_dbname_error"] = "There was a problem with the database name";
$lang["install_step1_dbprefix_error"] = "There was a problem with the database prefix";
$lang["install_step1_dbhost_error"] = "There was a problem with the database host";

$lang["install_step1_settings_file_already_exists"] = "There is already a Hotaru settings file on your server. If you press 'update' your existing file will be updated with the settings above.";
$lang["install_step1_settings_db_already_exists"] = "There is already a Hotaru database on your server with live tables. Proceed with caution.";
$lang["install_step1_update_file_writing_success"] = "The 'settings' file was created.";
$lang["install_step1_update_file_writing_failure"] = "There was a problem creating the 'settings' file.";
$lang["install_step1_no_db_exists_failure"] = "The database does not exist or the connection settings are incorrect.";
$lang["install_step1_no_table_exists_failure"] = "No tables exist or the database prefix may be incorrect in your settings file.";


/* Install Step 3 */
$lang["install_step2"] = "Step 2/4: Create Database Tables";
$lang["install_step2_checking_tables"] = "Checking for existing tables in database: ";
$lang["install_step2_no_tables"] = "No existing tables were found in the database";
$lang["install_step2_creating_table"] = "Creating table";
$lang["install_step2_adding_data"] = "Adding data to";
$lang["install_step2_deleting_table"] = "Deleting existing tables";
$lang["install_step2_already_exists"] = "It seems there are already tables for Hotaru CMS in the database.";
$lang["install_step2_continue"] = "Click \"Next\" to continue.";
$lang["install_step2_rebuild_note"] = "<i>Note</i>: If you'd like to start fresh, ";
$lang["install_step2_rebuild_link"] = "delete and rebuild the database tables";
$lang["install_step2_success"] = "Database tables created successfully. Click \"Next\" to configure Hotaru CMS.";
$lang["install_step2_fail"] = "There were some errors in creating database tables. Not all tables may have been created correctly.";
$lang["install_step2_existing_db"] = "You already have an existing installation of Hotaru CMS.<br/>If you continue, this installation will DELETE all your existing tables and settings, including posts, users and plugin data.";
$lang["install_step2_existing_confirm"] = "Confirm you wish to continue this install by typing 'DELETE' in the box and press the button";
$lang["install_step2_existing_go_upgrade1"] = "Alternatively, you may wish to ";
$lang["install_step2_existing_go_upgrade2"] = "run the upgrade script";
$lang["install_step2_form_delete_confirm"] = "confirm";
$lang["install_step2_form_delete"] = "Update";



/* Install Step 4 */
$lang["install_step3"] = "Step 3/4: Admin Registration";
$lang["install_step3_instructions"] = "Register yourself as a site administrator";
$lang["install_step3_username"] = "Username:";
$lang["install_step3_email"] = "Email:";
$lang["install_step3_password"] = "Password:";
$lang["install_step3_password_verify"] = "Password (again):";
$lang["install_step3_csrf_error"] = "Ah! You've triggered a CSRF error. That's only supposed to happen when someone tries hacking into the site...";
$lang["install_step3_username_error"] = "Your username must be at least 4 characters and can contain letters, dashes and underscores only";
$lang["install_step3_password_error"] = "The password must be at least 8 characters and can only contain letters, numbers and these symbols: @ * # - _";
$lang["install_step3_password_match_error"] = "The password fields don't match";
$lang["install_step3_email_error"] = "That doesn't parse as a valid email address";
$lang["install_step3_make_note"] = "Make a note of your new username, email and password before clicking \"Next\"...";
$lang["install_step3_update_success"] = "Updated successfully";
$lang["install_step3_form_update"] = "Update";

/* Install Step 5 */
$lang["install_step4"] = "Step 4/4: Completion";
$lang["install_step4_installation_complete"] = "The database has been successfully upgraded";
$lang["install_step4_installation_delete"] = "<span style='color: red;'><b>WARNING:</b> You <b>must</b> delete the install folder or someone else could run the install script and wipe everything!</span>";

$lang["install_step4_form_check_php"] = "Check PHP Setup";
$lang["install_step4_form_check_php_warning"] = "Note: Your server is missing the PHP module: ";
$lang["install_step4_form_check_php_success"] = "Your server has the required PHP modules";
$lang["install_step4_form_check_php_version"] = "Hotaru has not been tested on this version of PHP. You may need to upgrade";


$lang["install_step4_installation_go_play"] = "Done? Okay, go and play with your new Hotaru site!";
$lang["install_home"] = "Get Started!";

?>