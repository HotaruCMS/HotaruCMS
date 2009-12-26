<?php
/**
 * name: SB Users
 * description: Provides profile, settings and permission pages
 * version: 0.1
 * folder: sb_users
 * type: users
 * class: SbUsers
 * hooks: pagehandling_getpagename, theme_index_top, header_include, breadcrumbs, theme_index_post_breadcrumbs, theme_index_main
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

class SbUsers
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
        if ($h->pageType == 'user') {
            $h->vars['user'] = new UserAuth();
            $result = $h->vars['user']->getUserBasic($h, 0, $user);
            if ($result) {
                $h->vars['profile'] = $h->vars['user']->getProfileSettingsData($h, 'user_profile');
            } else {
                $h->pageTitle = $h->lang["main_theme_page_not_found"];
                $h->pageType = '';
                $h->vars['user'] = false;
            }
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
        
        $h->displayTemplate('sb_users_tabs');
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
                $h->displayTemplate('sb_users_profile');
                return true;
                break;
            case 'account':
                if (!$admin && !$own) { $denied = true; break; }
                $h->vars['checks'] = $h->vars['user']->updateAccount($h);
                $h->displayTemplate('sb_users_account');
                return true;
                break;
            case 'edit-profile':
                if (!$admin && !$own) { $denied = true; break; }
                $h->displayTemplate('sb_users_edit_profile');
                return true;
                break;
            case 'user-settings':
                if (!$admin && !$own) { $denied = true; break; }
                $h->displayTemplate('sb_users_settings');
                return true;
                break;
            case 'permissions':
                if (!$admin) { $denied = true; break; }
                $h->displayTemplate('sb_users_permissions');
                return true;
                break;
        }
        
        if ($denied) {
            $h->messages[$this->lang["access_denied"]] = 'red';
            $h->showMessages();
        }
    }
}

?>
