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
$lang['admin_announcement_users_disabled'] = "<span style='color: #ff0000;'>Please enable the Users plugin in Plugin Management.</span>";

/* Uninstall a plugin  - admin/plugins.php */
$lang["admin_plugins_uninstall_done"] = "Done!";
$lang["admin_plugins_uninstall_deleted"] = "This plugin has been deleted from the <i>plugins</i>, <i>pluginhooks</i> and <i>pluginsettings</i> database tables (if it was there in the first place).";
$lang["admin_plugins_uninstall_note"] = "<i>Note: </i>Any other database entries or tables created by the plugin have not been deleted.";
$lang["admin_plugins_uninstall_refresh"] = "Please <a href='javascript:location.reload(true);' target='_self'>refresh this page</a> to update these lists.";

/* Plugin info - /class.plugins.php */
$lang["admin_plugins_class_new_version"] = "<span style='color: red'>- Newer version available</span>. <b>Please uninstall</b>.";

/* Settings */
$lang['admin_settings_update_success'] = "Updated successfully.";
$lang['admin_settings_update_failure'] = "Update failed.";
?>