<?php
/**
 * USER MANAGER LANGUAGE
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

/* Main Page */
$lang["user_man"] = "User Manager";
$lang["user_man_default_perms"] = "Edit Default Permissions";
$lang["user_man_default_settings"] = "Edit Default User Settings";
$lang["user_man_user"] = "User";
$lang["user_man_user_submissions_1"] = "You can see all";
$lang["user_man_user_submissions_2"] = "'s submissions";
$lang["user_man_user_last_logged_in"] = "last logged in at";
$lang["user_man_user_registered_on"] = "registered at";
$lang["user_man_user_email_icon"] = "Email address not validated yet.";
$lang["user_man_user_email_not_validated"] = ", but has <b>not</b> validated his or her email address yet.";
$lang["user_man_user_email_validated"] = " and has validated his or her email address.";
$lang['user_man_here'] = "here";
$lang['user_man_email'] = "Email:";
$lang["user_man_desc"] = "The User Manager enables you to quickly find and edit users, ordered newest first.";
$lang["user_man_search"] = "Search for a user: <span style='font-weight: normal;'><small>(username or email)</small></span>";
$lang["user_man_search_too_short"] = "You search term must be at least 3 characters";
$lang["user_man_filter"] = "Filter users:";
$lang["user_man_id"] = "ID";
$lang["user_man_role"] = "Role";
$lang["user_man_joined"] = "Joined";
$lang["user_man_username"] = "Username <span style='font-weight: normal;'><small>(click for details)</small></span>";
$lang["user_man_account"] = "Account";
$lang["user_man_perms"] = "Permissions";
$lang['user_man_search_button'] = "Search";
$lang['user_man_filter_button'] = "Filter";
$lang['user_man_filter_all'] = "All";
$lang['user_man_filter_not_killspammed'] = "Not Killspammed";
$lang['user_man_filter_newest'] = "Newest";
$lang['user_man_filter_oldest'] = "Oldest";
$lang['user_man_filter_last_visited'] = "Last Visited";
$lang["user_man_show_content"] = "Show content";
$lang["user_man_need_search"] = "Please install and activate the Search plugin";
$lang["user_man_usered"] = "Usered:";
$lang["user_man_author"] = "Author:";
$lang["user_man_content"] = "Content:";
$lang["user_man_category"] = "Category:";
$lang["user_man_tags"] = "Tags:";
$lang["user_man_urls"] = "Urls:";
$lang["user_man_check"] = "";
$lang['user_man_checkbox_action'] = "Go";
$lang["user_man_checkboxes_empty"] = "No users selected";
$lang["user_man_checkboxes_role_changed"] = "Role changed for selected users";
$lang["user_man_checkboxes_user_deleted"] = "Selected user and associated posts, comments and tags permanently deleted";
$lang["user_man_checkboxes_no_action"] = "No action taken";
$lang["user_man_set_admin"] = "Set role to 'admin'";
$lang["user_man_set_supermod"] = "Set role to 'supermod'";
$lang["user_man_set_moderator"] = "Set role to 'moderator'";
$lang["user_man_set_member"] = "Set role to 'member'";
$lang["user_man_set_pending"] = "Set role to 'pending'";
$lang["user_man_set_undermod"] = "Set role to 'undermod'";
$lang["user_man_set_suspended"] = "Set role to 'suspended'";
$lang["user_man_set_banned"] = "Set role to 'banned'";
$lang["user_man_set_killspammed"] = "Killspam selected";
$lang["user_man_set_delete"] = "Physically Delete";
$lang['user_man_link'] = "User Manager";
$lang['user_man_no_pending_users'] = "There are no users pending approval";
$lang["user_man_num_pending"] = "<span style='color: red;'>Pending users: </span>";
$lang['user_man_when_killspam_delete'] = "When killspamming or deleting a user:";
$lang['user_man_add_blocked_list'] = "Add to Blocked List";

/* Sidebar */
$lang["user_man_admin_sidebar"] = "User Manager";

/* Submit Post Edit */
$lang['user_man_find_user'] = "Find this post's author in User Manager";

/* Edit Default Permissions */
$lang["user_man_perms_desc"] = "Use this page to edit the <b>default</b> permissions for each usergroup.";
$lang["user_man_admin_access_denied"] = "Sorry, only admins can access this page.";
$lang["user_man_default_perms_for"] = "Default permissions for ";
$lang["user_man_apply_perms_desc"] = "Changing default permissions <b>only affects <i>new</i> users</b>. Check the box below if you want existing users to have these permissions, too. <b>Be careful</b> because you will overwrite any changes you may have made for individual users."; 
$lang["user_man_apply_perms"] = "Apply changes to existing users";
$lang["user_man_revert_perms"] = "Revert this usergroup to its original default permissions";
$lang["user_man_revert_all_perms"] = "Revert <span class='bold_red'>all usergroups</span> to their original default permissions";
$lang["user_man_revert_perms_note"] = "<small>(not forced on existing users)</small>";
$lang["user_man_perms_updated"] = "Permissions updated";
$lang["user_man_perms_reverted"] = "Permissions reverted to defaults";
$lang["user_man_all_perms_reverted"] = "All permissions reverted to defaults";
$lang["user_man_all_perms_deleted"] = "All default permissions deleted";
$lang["user_man_perms_trouble"] = "<span class='bold_red'>DELETE ALL DEFAULTS</span>";
$lang["user_man_perms_trouble_note"] = "<small>(You will need to uninstall and reinstall plugins to restore them)</small>";
$lang['user_man_no_perms'] = "That's odd, there are no default permissions... Try clicking \"DELETE ALL DEFAULTS\" to reinstall the required \"Can Access Admin\" permission.";

/* Edit Default Settings */
$lang["user_man_user_settings_desc"] = "Use this page to edit the <b>default</b> user settings for each usergroup.";
$lang["user_man_admin_access_denied"] = "Sorry, only admins can access this page.";
$lang["user_man_default_user_settings_for"] = "Default user settings for ";
$lang["user_man_force_user_settings_desc"] = "Changing default user settings <b>only affects <i>new</i> users</b>. Check the box below if you want to force these settings on existing users, too. <b>Be careful</b> because you will overwrite their own custom settings!"; 
$lang["user_man_force_user_settings"] = "Force changes on existing users";
$lang["user_man_revert_user_settings"] = "Revert this usergroup to its original default user settings";
$lang["user_man_revert_all_user_settings"] = "Revert <span class='bold_red'>all users</span> to the original default settings";
$lang["user_man_revert_user_settings_note"] = "<small>(not forced on existing users)</small>";
$lang["user_man_user_settings_updated"] = "User settings updated";
$lang["user_man_user_settings_reverted"] = "User settings reverted to defaults";
$lang["user_man_all_user_settings_reverted"] = "All user settings reverted to defaults";
$lang["user_man_all_user_settings_deleted"] = "All default user settings deleted";
$lang["user_man_user_settings_trouble"] = "<span class='bold_red'>DELETE ALL DEFAULTS</span>";
$lang["user_man_user_settings_trouble_note"] = "<small>(You will need to uninstall and reinstall plugins to restore them)</small>";
$lang['user_man_no_settings'] = "There haven't been any settings installed by plugins yet";

?>