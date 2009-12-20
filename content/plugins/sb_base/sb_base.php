<?php
/**
 * name: SB Base
 * description: Social Bookmarking base - provides "list" and "post" templates. 
 * version: 0.1
 * folder: sb_base
 * class: SbBase
 * type: base
 * hooks: install_plugin, theme_index_top, header_meta, navigation, breadcrumbs, theme_index_main
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
    public $hotaru = '';   // access Hotaru functions using $this->hotaru
    
    /**
     * Install Submit settings if they don't already exist
     */
    public function install_plugin()
    {
        // Default settings 
        $sb_base_settings = $this->hotaru->getSerializedSettings();
        if (!isset($sb_base_settings['posts_per_page'])) { $sb_base_settings['posts_per_page'] = 10; }
        if (!isset($sb_base_settings['archive'])) { $sb_base_settings['archive'] = "no_archive"; }
        $this->hotaru->updateSetting('sb_base_settings', serialize($sb_base_settings));
        
        // Add "open in new tab" option to the default user settings
        $base_settings = $this->hotaru->getDefaultSettings('base'); // originals from plugins
        $site_settings = $this->hotaru->getDefaultSettings('site'); // site defaults updated by admin
        if (!isset($base_settings['new_tab'])) { 
            $base_settings['new_tab'] = ""; $site_settings['new_tab'] = "";
            $this->hotaru->updateDefaultSettings($base_settings, 'base'); 
            $this->hotaru->updateDefaultSettings($site_settings, 'site');
        }
        if (!isset($base_settings['link_action'])) { 
            $base_settings['link_action'] = ""; $site_settings['link_action'] = "";
            $this->hotaru->updateDefaultSettings($base_settings, 'base'); 
            $this->hotaru->updateDefaultSettings($site_settings, 'site');
        }
    }
    
    
    /**
     * Determine the pageType
     */
    public function theme_index_top()
    {
        switch ($this->hotaru->pageName)
        {
            case 'index':
                $this->hotaru->pageType = 'list';
                $this->hotaru->pageTitle = $this->hotaru->lang["sb_base_site_name"];
                break;
            case 'latest':
                $this->hotaru->pageType = 'list';
                $this->hotaru->pageTitle = $this->hotaru->lang["sb_base_latest"];
                break;
            case 'upcoming':
                $this->hotaru->pageType = 'list';
                $this->hotaru->pageTitle = $this->hotaru->lang["sb_base_latest"];
                break;
            case 'sort':
                $this->hotaru->pageType = 'list';
                $sort = $hotaru->cage->get->testPage('sort');
                $sort_lang = 'sb_base_' . str_replace('-', '_', $sort);
                $this->hotaru->pageTitle = $this->hotaru->lang[$sort_lang];
                break;
        }
        
        // stop here if not a list of post page:
        if (($this->hotaru->pageType != 'list') && ($this->hotaru->pageType != 'post')) {
            return false; 
        }
        
        // include sb_base_functions class:
        include_once(PLUGINS . 'sb_base/libs/SbBaseFunctions.php');
        $funcs = new SbBaseFunctions();
        
        // if a list, get the posts:
        if ($this->hotaru->pageType == 'list') {
            $this->hotaru->vars['posts'] = $funcs->prepareList($this->hotaru);
        }
        
        // if a post, find it:
        if ($this->hotaru->pageType == 'post')
        {
            $pagename = $this->hotaru->pageName;
            
            if (is_numeric($pagename)) {
                // Page name is a number so it must be a post with non-friendly urls
                $this->hotaru->readPost($pagename);    // read current post
                $this->hotaru->pageTitle = $this->hotaru->post->title;
            
            } elseif ($post_id = $this->hotaru->isPostUrl($pagename)) {
                // Page name belongs to a story
                $this->hotaru->readPost($post_id);    // read current post
                $this->hotaru->pageTitle = $this->hotaru->post->title;
            
            } else {
                // don't know what kind of post this is. Maybe return a page not found?
            }
        }
        
        // user defined settings:
        
        // open links in a new tab?
        if (isset($hotaru->currentUser->settings['new_tab'])) { 
            $this->hotaru->vars['target'] = 'target="_blank"'; 
        } else { 
            $this->hotaru->vars['target'] = ''; 
        }
        
        // open link to the source or the site post?
        if (isset($hotaru->currentUser->settings['link_action'])) { 
            $this->hotaru->vars['link_action'] = 'source'; 
        } else { 
            $this->hotaru->vars['link_action'] = ''; 
        }
        
        // editorial (story with an internal link)
        if (strstr($this->hotaru->post->origUrl, BASEURL)) { 
            $this->hotaru->vars['editorial'] = true;
        } else { $this->hotaru->vars['editorial'] = false; } 
        
        // get settings from SB_Submit 
        if (!isset($this->hotaru->vars['submit_settings'])) {
            $this->hotaru->vars['submit_settings'] = $this->hotaru->getSerializedSettings('sb_submit');
        }
    }
    
    
    /**
     * Match meta tag to a post's description (keywords is done in the Tags plugin)
     */
    public function header_meta()
    {    
        if ($this->hotaru->pageType != 'post') { return false; }
        $meta_content = sanitize($this->hotaru->post->content, 1);
        $meta_content = truncate($meta_content, 200);
        echo '<meta name="description" content="' . $meta_content . '">' . "\n";
        return true;
    }
    
    
    /**
     * Add "Latest" to the navigation bar
     */
    public function navigation()
    {
        // highlight "Latest" as active tab
        if ($this->hotaru->pageName == 'latest') { $status = "id='navigation_active'"; } else { $status = ""; }
        
        // display the link in the navigation bar
        echo "<li><a  " . $status . " href='" . $this->hotaru->url(array('page'=>'latest')) . "'>" . $this->hotaru->lang["sb_base_latest"] . "</a></li>\n";
    }
    
    
    /**
     * Replace the default breadcrumbs in specific circumstances
     */
    public function breadcrumbs()
    {
        if ($this->hotaru->pageName == 'index') { 
            $this->hotaru->pageTitle = $this->hotaru->lang["sb_base_top"];
        }
        
        switch ($this->hotaru->pageName) {
            case 'index':
                $this->hotaru->pageTitle .= $this->hotaru->rssBreadcrumbsLink('top');
                break;
            case 'latest':
                $this->hotaru->pageTitle .= $this->hotaru->rssBreadcrumbsLink('new');
                break;
        }
    }
    
    /**
     * Determine which template to show and do preparation of variables, etc.
     */
    public function theme_index_main()
    {
        // stop here if not a list of a post
        if (($this->hotaru->pageType != 'list') && ($this->hotaru->pageType != 'post')) { return false; }
        
        // necessary settings:
        $this->hotaru->vars['use_content'] = $this->hotaru->vars['submit_settings']['content'];
        $this->hotaru->vars['use_summary'] = $this->hotaru->vars['submit_settings']['summary'];
        $this->hotaru->vars['summary_length'] = $this->hotaru->vars['submit_settings']['summary_length'];
        
        switch ($this->hotaru->pageType)
        {
            case 'post':
                // This post is visible if it's not buried/pending OR if the viewer has edit post permissions...
                
                // defaults:
                $buried = false; $pending = false; $can_edit = false;
                
                // check if buried:
                if ($this->hotaru->post->status == 'buried') { 
                    $buried = true;
                    $hotaru->message = $this->hotaru->lang["sb_base_post_buried"];
                } 
                
                // check if pending:
                if ($this->hotaru->post->status == 'pending') { 
                    $pending = true;
                    $hotaru->message = $this->hotaru->lang["sb_base_post_pending"];
                }
                
                // check if global edit permissions
                if ($this->hotaru->currentUser->getPermission('can_edit_posts') == 'yes') { $can_edit = true; }
                
                // display post or show error message
                if ((!$buried && !$pending) || $can_edit){
                    $this->hotaru->displayTemplate('sb_post');
                } else {
                    $this->hotaru->messageType = "red";
                    $this->hotaru->showMessage();
                }
                
                return true;
                break;
                
            case 'list':
                $this->hotaru->displayTemplate('sb_list');
                return true;
        }
    }


}
?>