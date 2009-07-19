<?php

// ADMIN

/* **************************************************************************************************** 
 *  File: /languages/admin/admin_english.php
 *  Purpose: A language file for English. It's used whenever an admin file needs to output language.
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
$lang["admin_login"] = "Login";
$lang["admin_login_instructions"] = "Enter your username and password:";
$lang["admin_login_failed"] = "Login failed";
$lang["admin_login_form_submit"] = "Login";

/* Announcements /class.hotaru.php */
$lang['admin_announcement_delete_install'] = "<span style='color: #ff0000;'>Please delete the install folder before someone deletes your database!</span>";
$lang['admin_announcement_plugins_disabled'] = "<span style='color: #ff0000;'>Go to Plugin Management to enable some plugins.</span>";
$lang['admin_announcement_users_disabled'] = "<span style='color: #ff0000;'>Please enable the Users plugin in Plugin Management.</span>";

/* Un/Install, Upgrade De/Activate a plugin  - admin/plugins.php */
$lang["admin_plugins_install_done"] = "Plugin successfully installed and activated";
$lang["admin_plugins_install_error"] = "Sorry, this plugin requires ";
$lang["admin_plugins_uninstall_done"] = "Plugin uninstalled";
$lang["admin_plugins_upgrade_done"] = "Plugin upgraded";
$lang["admin_plugins_page_refresh"] = "Refresh this page";

$lang["admin_plugins_activated"] = "Plugin activated";
$lang["admin_plugins_deactivated"] = "Plugin deactivated";

/* Plugin info - /class.plugins.php */
$lang["admin_plugins_class_new_version"] = "<span style='color: red'>- Newer version available</span>.";
$lang["admin_plugins_class_upgrade_now"] = "Upgrade now!";
$lang["admin_plugins_class_reinstall"] = "This plugin doesn't have an upgrade script, so please uninstall and reactivate.";

/* Settings */
$lang['admin_settings_update_success'] = "Updated successfully.";
$lang['admin_settings_update_failure'] = "Update failed.";

/* Maintenance */
$lang['admin_maintenance_clear_cache_success'] = "Cache successfully deleted.";
$lang['admin_maintenance_clear_cache_failure'] = "No cache files were found.";
?>