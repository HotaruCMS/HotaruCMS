<?php
/**
 * name: Users
 * description: Provides profile, settings and permission pages
 * version: 1.1
 * folder: users
 * type: users
 * class: Users
 * hooks: pagehandling_getpagename, theme_index_top, header_include, sb_base_functions_preparelist, breadcrumbs, theme_index_post_breadcrumbs, theme_index_main, users_edit_profile_save, user_settings_save
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

class Users
{
    /**
     * Check if we're looking at a user page
     */
    public function pagehandling_getpagename($h, $query_vars)
    {
        // we already know that there's no "page" parameter, so...
        if ($h->cage->get->keyExists('user')) {
            return 'profile'; // sets $h->pageName to "profile"
        }
    }
    
    
    /**
     * Determine what page we're looking at
     */
    public function theme_index_top($h)
    {
        switch ($h->pageName)
        {
            case 'profile':
                $user = $h->cage->get->testUsername('user');
                $h->pageTitle = $h->lang["users_profile"] . ' &laquo; ' . $user;
                $h->pageType = 'user';
                break;
            case 'account':
                $user = $h->cage->get->testUsername('user');
                $h->pageTitle = $h->lang["users_account"] . ' &laquo; ' . $user;
                $h->pageType = 'user';
                break;
            case 'edit-profile':
                $user = $h->cage->get->testUsername('user');
                $h->pageTitle = $h->lang["users_profile_edit"] . ' &laquo; ' . $user;
                $h->pageType = 'user';
                break;
            case 'user-settings':
                $user = $h->cage->get->testUsername('user');
                $h->pageTitle = $h->lang["users_settings"] . ' &laquo; ' . $user;
                $h->pageType = 'user';
                break;
            case 'permissions':
                $user = $h->cage->get->testUsername('user');
                $h->pageTitle = $h->lang["users_permissions"] . ' &laquo; ' . $user;
                $h->pageType = 'user';
                break;
        }
        
        // read this user into the global hotaru object for later use on this page
        if ($h->pageType != 'user') { return false; }
        
        $h->vars['user'] = new UserAuth();
        if ($user) {
            $result = $h->vars['user']->getUserBasic($h, 0, $user);
        } else {
            // when the account page has been submitted (get id in case username has changed)
            $userid = $h->cage->post->testInt('userid');
            if ($userid) { $result = $h->vars['user']->getUserBasic($h, $userid); }
        }
        
        if (isset($result)) {
            $h->vars['profile'] = $h->vars['user']->getProfileSettingsData($h, 'user_profile');
            $h->vars['settings'] = $h->vars['user']->getProfileSettingsData($h, 'user_settings');
        } else {
            $h->pageTitle = $h->lang["main_theme_page_not_found"];
            $h->pageType = '';
            $h->vars['user'] = false;
        }
        
        /* tidy up the title and breadcrumbs with the latest account data
          (i.e. if the username has been changed) */
        if ($h->pageName == 'account') {
            $h->vars['checks'] = $h->vars['user']->updateAccount($h);
            $h->vars['user']->name = $h->vars['checks']['username_check'];
            $h->pageTitle = $h->lang["users_account"] . ' &laquo; ' . $h->vars['user']->name;
            $h->pageType = 'user';
        }
    }
    
    /**
     * Filter posts to this user
     */
    public function sb_base_functions_preparelist($h)
    {
        $username = $h->cage->get->testUsername('user');
        if ($username) {
            $h->vars['filter']['post_author = %d'] = $h->getUserIdFromName($username); 
        }
    }
    
    
    /**
     * Replace the default breadcrumbs in specific circumstances
     */
    public function breadcrumbs($h)
    {
        if ($h->pageType != 'user') { return false; }
        
        switch ($h->pageName)
        {
            case 'profile':
                $h->pageTitle = $h->lang["users_profile"] . ' &raquo; ' . $h->vars['user']->name;
                break;
            case 'account':
                $h->pageTitle = $h->lang["users_account"] . ' &raquo; ' . $h->vars['user']->name;
                break;
            case 'edit-profile':
                $h->pageTitle = $h->lang["users_profile_edit"] . ' &raquo; ' . $h->vars['user']->name;
                break;
            case 'user-settings':
                $h->pageTitle = $h->lang["users_settings"] . ' &raquo; ' . $h->vars['user']->name;
                break;
            case 'permissions':
                $h->pageTitle = $h->lang["users_permissions"] . ' &raquo; ' . $h->vars['user']->name;
                break;
        }
    }
    
    /**
     * Display the user tabs
     */
    public function theme_index_post_breadcrumbs($h)
    {
        if ($h->pageType != 'user') { return false; }
        
        $h->displayTemplate('users_tabs');
        return true;
    }
    
    
    /**
     * Display the right page
     */
    public function theme_index_main($h)
    {
        if ($h->pageType != 'user') { return false; }
        
        // determine permissions
        $admin = false; $own = false; $denied = false;
        if ($h->currentUser->getPermission('can_access_admin') == 'yes') { $admin = true; }
        if ($h->currentUser->id == $h->vars['user']->id) { $own = true; }
        
        switch($h->pageName) {
            case 'profile':
                $h->displayTemplate('users_profile');
                return true;
                break;
            case 'account':
                if (!$admin && !$own) { $denied = true; break; }
                $h->displayTemplate('users_account');
                return true;
                break;
            case 'edit-profile':
                if (!$admin && !$own) { $denied = true; break; }
                $h->displayTemplate('users_edit_profile');
                return true;
                break;
            case 'user-settings':
                if (!$admin && !$own) { $denied = true; break; }
                $h->displayTemplate('users_settings');
                return true;
                break;
            case 'permissions':
                if (!$admin) { $denied = true; break; }
                $this->editPermissions($h);
                $h->displayTemplate('users_permissions');
                return true;
                break;
        }
        
        if ($denied) {
            $h->messages[$h->lang["access_denied"]] = 'red';
            $h->showMessages();
        }
    }
    
    
    /**
     * Save profile data (from hook in edit_profile.php)
     */
    public function users_edit_profile_save($h, $vars)
    {
        $username = $vars[0];
        $profile = $vars[1];
        
        // check CSRF key
        if (!$h->csrf()) {
            $h->message = $h->lang['error_csrf'];
            $h->messageType = "red";
            return false;
        }
        
        $h->vars['user']->saveProfileSettingsData($h, $profile, 'user_profile', $h->vars['user']->id);
        
        $h->message = $h->lang["users_profile_edit_saved"] . "<br />\n";
        $h->message .= "<a href='" . $h->url(array('user'=>$h->vars['user']->name)) . "'>";
        $h->message .= $h->lang["users_profile_edit_view_profile"] . "</a>\n";
        $h->messageType = "green";
    }
    
    
    /**
     * Save settings data (from hook in user_settings.php)
     */
    public function user_settings_save($h, $vars)
    {
        $username = $vars[0];
        $settings = $vars[1];
        
        // check CSRF key
        if (!$h->csrf()) {
            $h->message = $h->lang['error_csrf'];
            $h->messageType = "red";
            return false;
        }
        
        $h->vars['user']->saveProfileSettingsData($h, $settings, 'user_settings', $h->vars['user']->id);
        
        $h->message = $h->lang["users_settings_saved"] . "<br />\n";
        $h->messageType = "green";
    }
    
    
    /** 
     * Enable admins to edit a user
     */
    public function editPermissions($h)
    {
        // prevent non-admin user viewing permissions of admin user
        if (($h->vars['user']->role) == 'admin' && ($h->currentUser->role != 'admin')) {
            $h->messages[$h->lang["users_account_admin_admin"]] = 'red';
            $h->showMessages();
            return true;
        }
        
        $perm_options = $h->getDefaultPermissions('', 'site', true);
        $perms = $h->vars['user']->getAllPermissions();
        
        // If the form has been submitted...
        if ($h->cage->post->keyExists('permissions')) {
        
            // check CSRF key
            if (!$h->csrf()) {
                $h->messages[$h->lang['error_csrf']] = 'red';
                return false;
            }
        
           foreach ($perm_options as $key => $options) {
                if ($value = $h->cage->post->testAlnumLines($key)) {
                    $h->vars['user']->setPermission($key, $value);
                }
            }

            $h->vars['user']->updatePermissions($h);   // physically store changes in the database
            
            // get the newly updated latest permissions:
            $perm_options = $h->getDefaultPermissions('', 'site', true);
            $perms = $h->vars['user']->getAllPermissions();
            $h->messages[$h->lang['users_permissions_updated']] = 'green';
        }
        
        $h->vars['perm_options'] = '';
        foreach ($perm_options as $key => $options) {
            $h->vars['perm_options'] .= "<tr><td>" . make_name($key) . ": </td>\n";
            foreach($options as $value) {
                if (isset($perms[$key]) && ($perms[$key] == $value)) { $checked = 'checked'; } else { $checked = ''; } 
                if ($key == 'can_access_admin' && $h->vars['user']->role == 'admin') { $disabled = 'disabled'; } else { $disabled = ''; }
                $h->vars['perm_options'] .= "<td><input type='radio' name='" . $key . "' value='" . $value . "' " . $checked . " " . $disabled . "> " . $value . " &nbsp;</td>\n";
            }
            $h->vars['perm_options'] .= "</tr>";
        }
    }
}

?>
