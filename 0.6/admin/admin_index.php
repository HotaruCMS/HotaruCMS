<?php
/**
 * Determines which page of Admin should be shown
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
 

// includes
require_once('../hotaru_header.php');
require_once('admin_news.php');
require_once('admin_plugins.php');
require_once(LIBS . 'Admin.php');
require_once(LIBS . 'PluginSettings.php');

$admin = New Admin();

$hotaru->setPageType('admin');

// Include combined css and js files
if ($cage->get->keyExists('combine')) {
    $type = $cage->get->testAlpha('type');
    $version = $cage->get->testInt('version');
    $hotaru->combineIncludes($type, $version);
    return true;
}

// Include admin language file
if (file_exists(LANGUAGES . LANGUAGE_PACK . 'admin_language.php')) 
{
    // language file for admin 
    require_once(LANGUAGES . LANGUAGE_PACK . 'admin_language.php');
} 
else 
{
    // try the default language pack
    require_once(LANGUAGES . 'language_default/admin_language.php');
}

$page = $cage->get->testPage('page');    // check with "get";
if (!$page) { 
    // check with "post" - used in admin_login_form().
    $page = $cage->post->testPage('page'); 
}

// Authenticate the admin if the Users plugin is INACTIVE:
if (!$plugins->pluginActive('users'))
{
    if (($page != 'admin_login') && !$result = $admin->isAdminCookie())
    {
        header('Location: ' . BASEURL . 'admin/admin_index.php?page=admin_login');
    }
}

// Authenticate the admin if the Users plugin is ACTIVE:
if (isset($current_user) && $plugins->pluginActive('users'))
{
    // This first condition happens when the Users plugin is activated 
    // and there's no cookie for the Admin yet.
    if (($current_user->userName == "") && $plugins->pluginActive('users')) 
    {
        header('Location: ' . BASEURL . 'index.php?page=login');
        die; exit;
    } 
    elseif (!$current_user->isAdmin($current_user->userName)) 
    {
        echo "You do not have permission to access this page.<br />";
        die(); exit;
    }
}

// If we get this far, we know that the user is an administrator.

$plugins->checkActions('admin_index');

switch ($page) {
    case "admin_login":
        $admin->adminLogin();
        break;
    case "admin_logout":
        $admin->adminLogout();
        break;
    case "settings":
        // Nothing special to do...
        break;
    case "maintenance":
        // Nothing special to do...
        break;
    case "plugins":
        plugins();
        break;
    case "plugin_settings":
        $plugins->setFolder($cage->get->testAlnumLines('plugin'));
        $plugins->setName($plugins->getName($plugins->getFolder()));
        break;
    default:
        break;
}

// Display the main theme's index.php template
$admin->displayAdminTemplate('index');

?>
