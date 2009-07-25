<?php

/* **************************************************************************************************** 
 *  File: /admin/languages/admin_language.php
 *  Purpose: A language file for Admin core and theme files. It's used whenever an admin file needs to output language.
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


/* ***************************************************************** 
******************************************************************** 
**********						  **********
**********		ADMIN CORE LANGUAGE 		  **********
**********						  **********
******************************************************************** 
***************************************************************** */

/* Breadcrumbs */
$lang["admin_breadcrumbs_home"] = "Home";
$lang["admin_breadcrumbs_login"] = "Login";

/* Login */
$lang["admin_login_failed"] = "Login failed";
$lang["admin_login_error_cookie"] = "Error setting cookie. Username not provided";

/* News */
$lang["admin_news_posted_by"] = "Posted by";
$lang["admin_news_on"] = "on";
$lang["admin_news_read_more"] = "Read more";

/* Announcements /class.hotaru.php */
$lang['admin_announcement_delete_install'] = "<span style='color: #ff0000;'>Please delete the install folder before someone deletes your database!</span>";
$lang['admin_announcement_plugins_disabled'] = "<span style='color: #ff0000;'>Go to Plugin Management to enable some plugins.</span>";
$lang['admin_announcement_users_disabled'] = "<span style='color: #ff0000;'>Please enable the Users plugin in Plugin Management.</span>";

/* Plugins /class.plugins.php */
$lang["admin_plugins_install_done"] = "Plugin successfully installed and activated";
$lang["admin_plugins_install_sorry"] = "Sorry,";
$lang["admin_plugins_install_requires"] = "requires";
$lang["admin_plugins_uninstall_done"] = "Plugin uninstalled";
$lang["admin_plugins_upgrade_done"] = "Plugin upgraded";
$lang["admin_plugins_page_refresh"] = "Refresh this page";
$lang["admin_plugins_activated"] = "Plugin activated";
$lang["admin_plugins_deactivated"] = "Plugin deactivated";
$lang['admin_plugins_on'] = "On";
$lang['admin_plugins_off'] = "Off";
$lang['admin_plugins_install'] = "Install";
$lang['admin_plugins_uninstall'] = "Uninstall";
$lang['admin_plugins_upgrade'] = "Upgrade";
$lang['admin_plugins_installed'] = "Installed";
$lang['admin_plugins_order_updated'] = "Order updated";
$lang['admin_plugins_order_last'] = "is already last.";
$lang['admin_plugins_order_zero'] = "Error: The order value is zero.";
$lang['admin_plugins_order_first'] = "is already first.";
$lang['admin_plugins_order_last'] = "is already last.";
$lang['admin_plugins_order_above'] = "Error: The plugin to move above has the same order value.";
$lang['admin_plugins_order_below'] = "Error: The plugin to move below has the same order value.";

/* Settings */
$lang['admin_settings_update_success'] = "Updated successfully.";

/* Maintenance */
$lang['admin_maintenance_clear_cache_success'] = "Cache successfully deleted.";
$lang['admin_maintenance_clear_cache_failure'] = "No cache files were found.";


/* ***************************************************************** 
******************************************************************** 
**********						  **********
**********		ADMIN THEME LANGUAGE 		  **********
**********						  **********
******************************************************************** 
***************************************************************** */

/* login */
$lang["admin_theme_login"] = "Login";
$lang["admin_theme_login_username"] = "Username";
$lang["admin_theme_login_password"] = "Password";
$lang["admin_theme_login_instructions"] = "Enter your username and password:";
$lang["admin_theme_login_form_submit"] = "Login";

/* navigation */
$lang["admin_theme_navigation_home"] = "Home";
$lang["admin_theme_navigation_admin"] = "Admin";
$lang["admin_theme_navigation_login"] = "Login";
$lang["admin_theme_navigation_logout"] = "Logout";

/* main */
$lang["admin_theme_main_admin_cp"] = "Admin Control Panel";
$lang["admin_theme_main_admin_home"] = "Admin Home";
$lang["admin_theme_main_latest"] = "Latest from Hotaru CMS";

/* maintenance */
$lang["admin_theme_maintenance"] = "Maintenance";
$lang["admin_theme_maintenance_title"] = "Hotaru Maintenance";
$lang["admin_theme_maintenance_db_cache"] = "Clear database cache";
$lang["admin_theme_maintenance_db_cache_desc"] = "deletes cached database queries.";
$lang["admin_theme_maintenance_rss_cache"] = "Clear RSS cache";
$lang["admin_theme_maintenance_rss_cache_desc"] = "deletes cached RSS feeds.";

/* plugins */
$lang["admin_theme_plugins"] = "Plugin Management";
$lang["admin_theme_plugins_installed"] = "Installed";
$lang["admin_theme_plugins_not_installed"] = "Not installed";
$lang["admin_theme_plugins_active"] = "Active";
$lang["admin_theme_plugins_switch"] = "Switch";
$lang["admin_theme_plugins_plugin"] = "Plugin";
$lang["admin_theme_plugins_install"] = "Install";
$lang["admin_theme_plugins_order"] = "Order";
$lang["admin_theme_plugins_details"] = "Details";
$lang["admin_theme_plugins_requires"] = "Requires";
$lang["admin_theme_plugins_description"] = "Description";
$lang["admin_theme_plugins_close"] = "Close";
$lang["admin_theme_plugins_no_plugins"] = "No additional plugins needed.";
$lang["admin_theme_plugins_guide"] = "Plugin Management Guide";
$lang["admin_theme_plugins_guide1"] = "The order column is used to determine which plugins are checked for hooks first.";
$lang["admin_theme_plugins_guide2"] = "Uninstalling a plugin will delete it from the <i>plugins</i>, <i>pluginhooks</i> and <i>pluginsettings</i> tables.";
$lang["admin_theme_plugins_guide3"] = "Any other database entries created by the plugin will not be removed.";

/* settings */
$lang["admin_theme_settings"] = "Settings";
$lang["admin_theme_settings_title"] = "Hotaru Settings";
$lang["admin_theme_settings_setting"] = "Setting";
$lang["admin_theme_settings_value"] = "Value";
$lang["admin_theme_settings_default"] = "Default";
$lang["admin_theme_settings_notes"] = "Notes";

/* plugin settings */
$lang["admin_theme_plugin_settings"] = "Plugin Settings";

/* footer */
$lang["admin_theme_footer_having_trouble_vist_forums"] = "Having trouble? Visit the forums at";
$lang["admin_theme_footer_for_help"] = "for help.";

/* 404 */
$lang["admin_theme_404_page_not_found"] = "Page not found.";

?>