<?php

/* Admin */

/* Login */
$lang["admin_login"] = "Login";
$lang["admin_login_reason"] = "Because the Users plugin is inactive, you'll need to log in again. ";
$lang["admin_login_instructions"] = "Enter your username and password to login:";
$lang["admin_login_failed"] = "Login failed";
$lang["admin_login_form_submit"] = "Login";

/* Announcements libraries/class.hotaru.php */
$lang['admin_announcement_delete_install'] = "<span style='color: #ff0000;'>Please delete the install folder before someone deletes your database!</span>";
$lang['admin_announcement_users_disabled'] = "<span style='color: #ff0000;'>Please enable the Users plugin in Plugin Management.</span>";

/* Uninstall a plugin  - admin/plugins.php */
$lang["admin_plugins_uninstall_done"] = "Done!";
$lang["admin_plugins_uninstall_deleted"] = "This plugin has been deleted from the <i>plugins</i>, <i>pluginhooks</i> and <i>pluginsettings</i> database tables (if it was there in the first place).";
$lang["admin_plugins_uninstall_note"] = "<i>Note: </i>Any other database entries or tables created by the plugin have not been deleted.";
$lang["admin_plugins_uninstall_refresh"] = "Please <a href='javascript:location.reload(true);' target='_self'>refresh this page</a> to update these lists.";

/* Plugin info - libraries/class.plugins.php */
$lang["admin_plugins_class_new_version"] = "<span style='color: red'>- Newer version available</span>. <b>Please uninstall</b>.";
?>