<?php
/**
 * name: SB Users
 * description: Provides profile, settings and permission pages
 * version: 0.1
 * folder: sb_users
 * type: users
 * class: SbUsers
 * hooks: pagehandling_getpagename, theme_index_top, breadcrumbs, theme_index_main
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
        }
        
        // read this user into the global hotaru object for later use on this page
        if ($h->pageType == 'user') {
            $h->vars['user'] = new UserBase();
            $h->vars['user']->getUserBasic($h, 0, $user);
            $h->vars['profile'] = $h->vars['user']->getProfileSettingsData($h, 'user_profile');
        }
    }
    
    
    /**
     * Replace the default breadcrumbs in specific circumstances
     */
    public function breadcrumbs($h)
    {
        if ($h->pageName == 'profile') { 
            $h->pageTitle = $h->vars['user']->name;
        }
    }
    
    
    /**
     * Display the right page
     */
    public function theme_index_main($h)
    {
        switch($h->pageName) {
            case 'profile':
                $h->displayTemplate('sb_users_profile');
                return true;
                break;
        }
    }
}

?>
