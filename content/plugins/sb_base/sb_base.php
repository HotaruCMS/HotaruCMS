<?php
/**
 * name: SB Base
 * description: Social Bookmarking base - provides "list" and "post" templates. 
 * version: 0.1
 * folder: sb_base
 * class: SbBase
 * type: base
 * hooks: install_plugin, theme_index_top, header_meta, header_include, navigation, breadcrumbs, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings
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

class SbBase
{
    /**
     * Install Submit settings if they don't already exist
     */
    public function install_plugin($hotaru)
    {
        // Default settings 
        $sb_base_settings = $hotaru->getSerializedSettings();
        if (!isset($sb_base_settings['posts_per_page'])) { $sb_base_settings['posts_per_page'] = 10; }
        if (!isset($sb_base_settings['archive'])) { $sb_base_settings['archive'] = "no_archive"; }
        $hotaru->updateSetting('sb_base_settings', serialize($sb_base_settings));
        
        // Add "open in new tab" option to the default user settings
        $base_settings = $hotaru->getDefaultSettings('base'); // originals from plugins
        $site_settings = $hotaru->getDefaultSettings('site'); // site defaults updated by admin
        if (!isset($base_settings['new_tab'])) { 
            $base_settings['new_tab'] = ""; $site_settings['new_tab'] = "";
            $hotaru->updateDefaultSettings($base_settings, 'base'); 
            $hotaru->updateDefaultSettings($site_settings, 'site');
        }
        if (!isset($base_settings['link_action'])) { 
            $base_settings['link_action'] = ""; $site_settings['link_action'] = "";
            $hotaru->updateDefaultSettings($base_settings, 'base'); 
            $hotaru->updateDefaultSettings($site_settings, 'site');
        }
    }
    
    
    /**
     * Determine the pageType
     */
    public function theme_index_top($hotaru)
    {
        switch ($hotaru->pageName)
        {
            case 'index':
                $hotaru->pageType = 'list';
                $hotaru->pageTitle = $hotaru->lang["sb_base_site_name"];
                break;
            case 'latest':
                $hotaru->pageType = 'list';
                $hotaru->pageTitle = $hotaru->lang["sb_base_latest"];
                break;
            case 'upcoming':
                $hotaru->pageType = 'list';
                $hotaru->pageTitle = $hotaru->lang["sb_base_latest"];
                break;
            case 'sort':
                $hotaru->pageType = 'list';
                $sort = $hotaru->cage->get->testPage('sort');
                $sort_lang = 'sb_base_' . str_replace('-', '_', $sort);
                $hotaru->pageTitle = $hotaru->lang[$sort_lang];
                break;
            default:
                // no default or we'd mess up anything set by other plugins
        }
        
        // stop here if not a list or the pageType has been set elsewhere:
        if ($hotaru->pageType && ($hotaru->pageType != 'list')) {
            return false; 
        }
        
        // get settings
        $hotaru->vars['sb_base_settings'] = $hotaru->getSerializedSettings('sb_base');
        $hotaru->vars['posts_per_page'] = $hotaru->vars['sb_base_settings']['posts_per_page'];
        
        // include sb_base_functions class:
        include_once(PLUGINS . 'sb_base/libs/SbBaseFunctions.php');
        $funcs = new SbBaseFunctions();
        
        // if a list, get the posts:
        if ($hotaru->pageType == 'list') {
            $hotaru->vars['posts'] = $funcs->prepareList($hotaru);
        }
        
        // Probably a post, let's check:

        $pagename = $hotaru->pageName;
        
        if (is_numeric($pagename)) {
            // Page name is a number so it must be a post with non-friendly urls
            $hotaru->readPost($pagename);    // read current post
            $hotaru->pageTitle = $hotaru->post->title;
            $hotaru->pageType = 'post';
        
        } elseif ($post_id = $hotaru->isPostUrl($pagename)) {
            // Page name belongs to a story
            $hotaru->readPost($post_id);    // read current post
            $hotaru->pageTitle = $hotaru->post->title;
            $hotaru->pageType = 'post';
        
        } else {
            // don't know what kind of post this is. Maybe return a page not found?
        }
        
        // user defined settings:
        
        // open links in a new tab?
        if (isset($hotaru->currentUser->settings['new_tab'])) { 
            $hotaru->vars['target'] = 'target="_blank"'; 
        } else { 
            $hotaru->vars['target'] = ''; 
        }
        
        // open link to the source or the site post?
        if (isset($hotaru->currentUser->settings['link_action'])) { 
            $hotaru->vars['link_action'] = 'source'; 
        } else { 
            $hotaru->vars['link_action'] = ''; 
        }
        
        // editorial (story with an internal link)
        if (strstr($hotaru->post->origUrl, BASEURL)) { 
            $hotaru->vars['editorial'] = true;
        } else { 
            $hotaru->vars['editorial'] = false; 
        } 
        
        // get settings from SB_Submit 
        if (!isset($hotaru->vars['submit_settings'])) {
            $hotaru->vars['submit_settings'] = $hotaru->getSerializedSettings('sb_submit');
        }
    }
    
    
    /**
     * Match meta tag to a post's description (keywords is done in the Tags plugin)
     */
    public function header_meta($hotaru)
    {    
        if ($hotaru->pageType != 'post') { return false; }
        $meta_content = sanitize($hotaru->post->content, 1);
        $meta_content = truncate($meta_content, 200);
        echo '<meta name="description" content="' . $meta_content . '">' . "\n";
        return true;
    }
    
    
    /**
     * Add "Latest" to the navigation bar
     */
    public function navigation($hotaru)
    {
        // highlight "Latest" as active tab
        if ($hotaru->pageName == 'latest') { $status = "id='navigation_active'"; } else { $status = ""; }
        
        // display the link in the navigation bar
        echo "<li><a  " . $status . " href='" . $hotaru->url(array('page'=>'latest')) . "'>" . $hotaru->lang["sb_base_latest"] . "</a></li>\n";
    }
    
    
    /**
     * Replace the default breadcrumbs in specific circumstances
     */
    public function breadcrumbs($hotaru)
    {
        if ($hotaru->pageName == 'index') { 
            $hotaru->pageTitle = $hotaru->lang["sb_base_top"];
        }
        
        switch ($hotaru->pageName) {
            case 'index':
                $hotaru->pageTitle .= $hotaru->rssBreadcrumbsLink('top');
                break;
            case 'latest':
                $hotaru->pageTitle .= $hotaru->rssBreadcrumbsLink('new');
                break;
        }
    }
    
    /**
     * Determine which template to show and do preparation of variables, etc.
     */
    public function theme_index_main($hotaru)
    {
        // stop here if not a list of a post
        if (($hotaru->pageType != 'list') && ($hotaru->pageType != 'post')) { return false; }
        
        // necessary settings:
        $hotaru->vars['use_content'] = $hotaru->vars['submit_settings']['content'];
        $hotaru->vars['use_summary'] = $hotaru->vars['submit_settings']['summary'];
        $hotaru->vars['summary_length'] = $hotaru->vars['submit_settings']['summary_length'];
        
        switch ($hotaru->pageType)
        {
            case 'post':
                // This post is visible if it's not buried/pending OR if the viewer has edit post permissions...
                
                // defaults:
                $buried = false; $pending = false; $can_edit = false;
                
                // check if buried:
                if ($hotaru->post->status == 'buried') { 
                    $buried = true;
                    $hotaru->message = $hotaru->lang["sb_base_post_buried"];
                } 
                
                // check if pending:
                if ($hotaru->post->status == 'pending') { 
                    $pending = true;
                    $hotaru->message = $hotaru->lang["sb_base_post_pending"];
                }
                
                // check if global edit permissions
                if ($hotaru->currentUser->getPermission('can_edit_posts') == 'yes') { $can_edit = true; }
                
                // display post or show error message
                if ((!$buried && !$pending) || $can_edit){
                    $hotaru->displayTemplate('sb_post');
                } else {
                    $hotaru->messageType = "red";
                    $hotaru->showMessage();
                }
                
                return true;
                break;
                
            case 'list':
                $hotaru->displayTemplate('sb_list');
                return true;
        }
    }


}
?>