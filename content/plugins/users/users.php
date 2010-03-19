<?php
/**
 * name: Users
 * description: Provides profile, settings and permission pages
 * version: 1.7
 * folder: users
 * type: users
 * class: Users
 * requires: sb_base 0.1
 * hooks: pagehandling_getpagename, sb_base_theme_index_top, header_include, sb_base_functions_preparelist, breadcrumbs, theme_index_main, users_edit_profile_save, user_settings_save, admin_theme_main_stats, header_meta
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
    public function sb_base_theme_index_top($h)
    {
        $user = $h->cage->get->testUsername('user');
        if ($user) {
            $h->subPage = 'user';
        }
        
        switch ($h->pageName)
        {
            case 'profile':
                $h->pageTitle = $h->lang["users_profile"] . '[delimiter]' . $user;
                $h->pageType = 'user';
                break;
            case 'account':
                $h->pageTitle = $h->lang["users_account"] . '[delimiter]' . $user;
                $h->pageType = 'user';
                break;
            case 'edit-profile':
                $h->pageTitle = $h->lang["users_profile_edit"] . '[delimiter]' . $user;
                $h->pageType = 'user';
                break;
            case 'user-settings':
                $h->pageTitle = $h->lang["users_settings"] . '[delimiter]' . $user;
                $h->pageType = 'user';
                break;
            case 'permissions':
                if (!$user) { // when the permissions form is submitted
                    $userid = $h->cage->post->testInt('userid');
                    $user = $h->getUserNameFromId($userid);
                }
                $h->pageTitle = $h->lang["users_permissions"] . '[delimiter]' . $user;
                $h->pageType = 'user';
                break;
            case 'index':
                if ($h->subPage == 'user') { $h->pageTitle = $h->lang["sb_base_top"] . '[delimiter]' . $user . '[delimiter]' . $h->pageTitle = $h->lang["sb_base_site_name"]; }
                break;
            case 'latest':
                if ($h->subPage == 'user') { $h->pageTitle = $h->lang["sb_base_latest"] . '[delimiter]' . $user; }
                break;
            case 'upcoming':
                if ($h->subPage == 'user') { $h->pageTitle = $h->lang["sb_base_upcoming"] . '[delimiter]' . $user; }
                break;
            case 'all':
                if ($h->subPage == 'user') { $h->pageTitle = $h->lang["sb_base_all"] . '[delimiter]' . $user; }
                break;
            case 'sort':
                if ($h->subPage == 'user') { 
                    $sort = $h->cage->get->testPage('sort');
                    $sort_lang = 'sb_base_' . str_replace('-', '_', $sort);
                    $h->pageTitle = $h->lang[$sort_lang] . '[delimiter]' . $user;
                }
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
            if ($userid) { 
                $result = $h->vars['user']->getUserBasic($h, $userid); 
            } else {
                $result = $h->vars['user']->getUserBasic($h, $h->currentUser->id); // default to self 
            }
        }
        
        if (isset($result)) {
            $h->vars['profile'] = $h->vars['user']->getProfileSettingsData($h, 'user_profile');
            $h->vars['settings'] = $h->vars['user']->getProfileSettingsData($h, 'user_settings');
        } else {
            $h->pageTitle = $h->lang["main_theme_page_not_found"];
            $h->pageType = '';
            $h->vars['user'] = false;
        }
        
        /* check for account updates */
        if ($h->pageName == 'account') {
            $h->vars['checks'] = $h->vars['user']->updateAccount($h);
            $h->vars['user']->name = $h->vars['checks']['username_check'];
            $h->pageTitle = $h->lang["users_account"] . '[delimiter]' . $h->vars['user']->name;
            $h->pageType = 'user';
        }
    }
    
    
    /**
     * Match meta tags when browsing results for individual users 
     */
    public function header_meta($h)
    {
        if ($h->pageName == 'profile') {
            if (isset($h->vars['profile']['bio']) && ($h->vars['profile']['bio'] != $h->lang['users_profile_default_bio'])) { 
                echo '<meta name="description" content="' . $h->vars['profile']['bio'] . '" />' . "\n";
            } else {
                echo '<meta name="description" content="' . $h->lang['users_default_meta_description_before'] . $h->vars['user']->name . $h->lang['users_default_meta_description_after'] . '" />' . "\n";  // default profile meta description (see language file)
            }
            
            echo '<meta name="keywords" content="' . $h->vars['user']->name . $h->lang['users_profile_meta_keywords_more'] . '" />' . "\n";  // default profile meta keywords (see language file)
            
            return true;
        }
        
        
        if ($h->subPage == 'user' && ($h->pageName != 'profile'))
        { 
            $user = $h->cage->get->testUsername('user');
            if ($user) {
                $first_word = $h->pageName;
                if ($first_word == 'sort') { $first_word = $h->cage->get->testPage('sort'); }
                if ($first_word == 'index') { $first_word = $h->lang['users_meta_description_popular']; }
                $first_word = ucfirst(strtolower(make_name($first_word, '-')));
                echo '<meta name="description" content="' . $h->lang['users_meta_description_results_before'] . $first_word . $h->lang['users_meta_description_results_middle'] . $user . $h->lang['users_meta_description_results_after'] . '" />' . "\n";
                echo '<meta name="keywords" content="' . $user . $h->lang['users_profile_meta_keywords_more'] . '" />' . "\n";  // default profile meta keywords (see language file)
                return true;
            }
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
        if (isset($h->vars['user'])) {
            $userlink = "<a href='" . $h->url(array('user'=>$h->vars['user']->name)) . "'>";
            $userlink .= $h->vars['user']->name . "</a>";
        }
        
        // This is for user pages, e.g. account, edit profile, etc:
        switch ($h->pageName)
        {
            case 'profile':
                $crumbs = $userlink . ' &raquo; ' . $h->lang["users_profile"];
                return $crumbs;
                break;
            case 'account':
                $crumbs = $userlink . ' &raquo; ' . $h->lang["users_account"];
                return $crumbs;
                break;
            case 'edit-profile':
                $crumbs = $userlink . ' &raquo; ' . $h->lang["users_profile_edit"];
                return $crumbs;
                break;
            case 'user-settings':
                $crumbs = $userlink . ' &raquo; ' . $h->lang["users_settings"];
                return $crumbs;
                break;
            case 'permissions':
                $crumbs = $userlink . ' &raquo; ' . $h->lang["users_permissions"];
                return $crumbs;
                break;
        }
        
        // This is used for filtered story pages, e.g. popular, latest, etc:
        if ($h->subPage == 'user' && $h->pageType == 'list') {
            switch ($h->pageName) {
                case 'index':
                    $title = $h->lang["sb_base_top"];
                    break;
                case 'latest':
                    $title = $h->lang["sb_base_latest"];
                    break;
                case 'upcoming':
                    $title = $h->lang["sb_base_upcoming"];
                    break;
                case 'all':
                    $title = $h->lang["sb_base_all"];
                    break;
                case 'sort':
                    $sort = $h->cage->get->testPage('sort');
                    $sort_lang = 'sb_base_' . str_replace('-', '_', $sort);
                    $title = $h->lang[$sort_lang];
                    break;
                default:
                    $title = $h->lang['users_posts'];
                    break;
            }

            $user = $h->cage->get->testUsername('user');
            $crumbs = "<a href='" . $h->url(array('user'=>$user)) . "'>\n";
            $crumbs .= $user . "</a>\n ";
            $crumbs .= " &raquo; " . $title;
            
            return $crumbs . $h->rssBreadcrumbsLink('', array('user'=>$user));
        }
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
        
        $h->displayTemplate('users_navigation');
        
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
            $h->messages[$h->lang["main_access_denied"]] = 'red';
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
        
        /*  Problem! The previous profile data is cached and we don't want to disable caching for profiles, 
            nor do we want to clear the entire db_cache, so instead, we'll delete the cache file that holds
            the previous profile for this user. */
        $sql = "SELECT usermeta_value FROM " . DB_PREFIX . "usermeta WHERE usermeta_userid = %d AND usermeta_key = %s";
        $query = $h->db->prepare($sql, $h->vars['user']->id, 'user_profile');
        $cache_file = CACHE . 'db_cache/' . md5($query);
        if (file_exists($cache_file)) {
            unlink($cache_file); // delete cache file.
        }
        
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
        
        /*  Problem! The previous settings data is cached and we don't want to disable caching for settings, 
            nor do we want to clear the entire db_cache, so instead, we'll delete the cache file that holds
            the previous settings for this user. */
        $sql = "SELECT usermeta_value FROM " . DB_PREFIX . "usermeta WHERE usermeta_userid = %d AND usermeta_key = %s";
        $query = $h->db->prepare($sql, $h->vars['user']->id, 'user_settings');
        $cache_file = CACHE . 'db_cache/' . md5($query);
        if (file_exists($cache_file)) {
            unlink($cache_file); // delete cache file.
        }
        
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
    

    /**
     * Show stats on Admin home page
     */
    public function admin_theme_main_stats($h, $vars)
    {
        require_once(LIBS . 'UserInfo.php');
        $ui = new UserInfo();
        
        echo "<li>&nbsp;</li>";

        foreach ($vars as $stat_type) {
            $users = $ui->stats($h, $stat_type);
            if (!$users) { $users = 0; }
            $lang_name = 'users_admin_stats_' . $stat_type;
            echo "<li>" . $h->lang[$lang_name] . ": " . $users . "</li>";
        }
    }
}

?>