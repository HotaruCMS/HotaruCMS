<?php
/**
 * name: SB Base
 * description: Social Bookmarking base - provides "list" and "post" templates. 
 * version: 0.1
 * folder: sb_base
 * class: SbBase
 * type: base
 * hooks: install_plugin, theme_index_top, header_meta, header_include, navigation, breadcrumbs, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings, admin_maintenance_database, admin_maintenance_top, admin_theme_main_stats, user_settings_pre_save, user_settings_fill_form, user_settings_extra_settings
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
    public function install_plugin($h)
    {
        // Default settings 
        $sb_base_settings = $h->getSerializedSettings();
        if (!isset($sb_base_settings['posts_per_page'])) { $sb_base_settings['posts_per_page'] = 10; }
        if (!isset($sb_base_settings['archive'])) { $sb_base_settings['archive'] = "no_archive"; }
        $h->updateSetting('sb_base_settings', serialize($sb_base_settings));
        
        // Add "open in new tab" option to the default user settings
        $base_settings = $h->getDefaultSettings('base'); // originals from plugins
        $site_settings = $h->getDefaultSettings('site'); // site defaults updated by admin
        if (!isset($base_settings['new_tab'])) { 
            $base_settings['new_tab'] = ""; $site_settings['new_tab'] = "";
            $h->updateDefaultSettings($base_settings, 'base'); 
            $h->updateDefaultSettings($site_settings, 'site');
        }
        if (!isset($base_settings['link_action'])) { 
            $base_settings['link_action'] = ""; $site_settings['link_action'] = "";
            $h->updateDefaultSettings($base_settings, 'base'); 
            $h->updateDefaultSettings($site_settings, 'site');
        }
    }
    
    
    /**
     * Determine the pageType
     */
    public function theme_index_top($h)
    {
        switch ($h->pageName)
        {
            case 'index':
                $h->pageType = 'list';
                $h->pageTitle = $h->lang["sb_base_site_name"];
                break;
            case 'latest':
                $h->pageType = 'list';
                $h->pageTitle = $h->lang["sb_base_latest"];
                break;
            case 'upcoming':
                $h->pageType = 'list';
                $h->pageTitle = $h->lang["sb_base_latest"];
                break;
            case 'sort':
                $h->pageType = 'list';
                $sort = $h->cage->get->testPage('sort');
                $sort_lang = 'sb_base_' . str_replace('-', '_', $sort);
                $h->pageTitle = $h->lang[$sort_lang];
                break;
            default:
                // no default or we'd mess up anything set by other plugins
        }
        
        $h->pluginHook('sb_base_theme_index_top');
        
        // stop here if not a list or the pageType has been set elsewhere:
        if (!empty($h->pageType) && ($h->pageType != 'list') && ($h->pageType != 'post')) {
            return false; 
        }
        
        // get settings
        $h->vars['sb_base_settings'] = $h->getSerializedSettings('sb_base');
        $h->vars['posts_per_page'] = $h->vars['sb_base_settings']['posts_per_page'];
        
        // include sb_base_functions class:
        require_once(PLUGINS . 'sb_base/libs/SbBaseFunctions.php');
        $funcs = new SbBaseFunctions();
        
        // if a list, get the posts:
        switch ($h->pageType)
        {
            case 'list':
                $h->vars['posts'] = $funcs->prepareList($h);
                break;
            case 'post':
                // if a post is already set (e.g. from the sb_categories plugin), we don't want to
                // do the default stuff below. We do, however, need the "target", "editorial" stuff after it, though...
                break;
            default:
                // Probably a post, let's check:
                if (is_numeric($h->pageName)) {
                    // Page name is a number so it must be a post with non-friendly urls
                    $h->readPost($h->pageName);    // read current post
                    $h->pageTitle = $h->post->title;
                    $h->pageType = 'post';
                } elseif ($post_id = $h->isPostUrl($h->pageName)) {
                    // Page name belongs to a story
                    $h->readPost($post_id);    // read current post
                    $h->pageTitle = $h->post->title;
                    $h->pageType = 'post';
                }
        } // close switch
        
        // user defined settings:
        
        // open links in a new tab?
        if (isset($h->currentUser->settings['new_tab'])) { 
            $h->vars['target'] = 'target="_blank"'; 
        } else { 
            $h->vars['target'] = ''; 
        }
        
        // open link to the source or the site post?
        if (isset($h->currentUser->settings['link_action'])) { 
            $h->vars['link_action'] = 'source'; 
        } else { 
            $h->vars['link_action'] = ''; 
        }
        
        // editorial (story with an internal link)
        if (strstr($h->post->origUrl, BASEURL)) { 
            $h->vars['editorial'] = true;
        } else { 
            $h->vars['editorial'] = false; 
        } 
        
        // get settings from SB_Submit 
        if (!isset($h->vars['submit_settings'])) {
            $h->vars['submit_settings'] = $h->getSerializedSettings('sb_submit');
        }
    }
    
    
    /**
     * Match meta tag to a post's description (keywords is done in the Tags plugin)
     */
    public function header_meta($h)
    {    
        if ($h->pageType != 'post') { return false; }
        $meta_content = sanitize($h->post->content, 1);
        $meta_content = truncate($meta_content, 200);
        echo '<meta name="description" content="' . $meta_content . '">' . "\n";
        return true;
    }
    
    
    /**
     * Add "Latest" to the navigation bar
     */
    public function navigation($h)
    {
        // highlight "Latest" as active tab
        if ($h->pageName == 'latest') { $status = "id='navigation_active'"; } else { $status = ""; }
        
        // display the link in the navigation bar
        echo "<li><a  " . $status . " href='" . $h->url(array('page'=>'latest')) . "'>" . $h->lang["sb_base_latest"] . "</a></li>\n";
    }
    
    
    /**
     * Replace the default breadcrumbs in specific circumstances
     */
    public function breadcrumbs($h)
    {
        if ($h->pageName == 'index') { 
            $h->pageTitle = $h->lang["sb_base_top"];
        }
        
        switch ($h->pageName) {
            case 'index':
                return $h->pageTitle . ' ' . $h->rssBreadcrumbsLink('top');
                break;
            case 'latest':
                return $h->pageTitle . ' ' . $h->rssBreadcrumbsLink('new');
                break;
        }
    }
    
    
    /**
     * Determine which template to show and do preparation of variables, etc.
     */
    public function theme_index_main($h)
    {
        // stop here if not a list of a post
        if (($h->pageType != 'list') && ($h->pageType != 'post')) { return false; }
        
        // necessary settings:
        $h->vars['use_content'] = $h->vars['submit_settings']['content'];
        $h->vars['use_summary'] = $h->vars['submit_settings']['summary'];
        $h->vars['summary_length'] = $h->vars['submit_settings']['summary_length'];
        
        switch ($h->pageType)
        {
            case 'post':
                // This post is visible if it's not buried/pending OR if the viewer has edit post permissions...
                
                // defaults:
                $buried = false; $pending = false; $can_edit = false;
                
                // check if buried:
                if ($h->post->status == 'buried') { 
                    $buried = true;
                    $h->message = $h->lang["sb_base_post_buried"];
                } 
                
                // check if pending:
                if ($h->post->status == 'pending') { 
                    $pending = true;
                    $h->message = $h->lang["sb_base_post_pending"];
                }
                
                // check if global edit permissions
                if ($h->currentUser->getPermission('can_edit_posts') == 'yes') { $can_edit = true; }
                
                // display post or show error message
                if ((!$buried && !$pending) || $can_edit){
                    $h->displayTemplate('sb_post');
                } else {
                    $h->messageType = "red";
                    $h->showMessage();
                }
                
                return true;
                break;
                
            case 'list':
                $h->displayTemplate('sb_list');
                return true;
        }
    }
    
    
    /**
     * Archive option on Maintenance page
     */
    public function admin_maintenance_database($h)
    {
        $sb_base_settings = $h->getSerializedSettings();
        $archive = $sb_base_settings['archive'];
        echo "<li><a href='" . BASEURL . "admin_index.php?page=maintenance&amp;action=update_archive'>";
        echo $h->lang["sb_base_maintenance_update_archive"] . "</a> - ";
        if ($archive == 'no_archive') {
            echo $h->lang["sb_base_maintenance_update_archive_remove"];
        } else {
            echo $h->lang["sb_base_maintenance_update_archive_desc_1"];
            echo $h->lang["sb_base_settings_post_archive_$archive"];
            echo $h->lang["sb_base_maintenance_update_archive_desc_2"];
        }
        echo "</li>";
    }
    
    
    /**
     * Perform archiving tasks
     */
    public function admin_maintenance_top($h)
    {
        if ($h->cage->get->testAlnumLines('action') != 'update_archive') { return false; }
        
        $sb_base_settings = $h->getSerializedSettings();
        $archive = $sb_base_settings['archive'];
        
        // FIRST, WE NEED TO RESET THE ARCHIVE, setting all archive fields to "N":
        
        // posts
        if ($h->db->table_exists('posts')) {
            $sql = "UPDATE " . DB_PREFIX . "posts SET post_archived = %s";
            $h->db->query($h->db->prepare($sql, 'N'));
        }
        
        // postmeta
        if ($h->db->table_exists('postmeta')) {
            $sql = "UPDATE " . DB_PREFIX . "postmeta SET postmeta_archived = %s";
            $h->db->query($h->db->prepare($sql, 'N'));
        }
        
        // postvotes
        if ($h->db->table_exists('postvotes')) {
            $sql = "UPDATE " . DB_PREFIX . "postvotes SET vote_archived = %s";
            $h->db->query($h->db->prepare($sql, 'N'));
        }
        
        // comments
        if ($h->db->table_exists('comments')) {
            $sql = "UPDATE " . DB_PREFIX . "comments SET comment_archived = %s";
            $h->db->query($h->db->prepare($sql, 'N'));
        }
        
        // commentvotes
        if ($h->db->table_exists('commentvotes')) {
            $sql = "UPDATE " . DB_PREFIX . "commentvotes SET cvote_archived = %s";
            $h->db->query($h->db->prepare($sql, 'N'));
        }
        
        // tags
        if ($h->db->table_exists('tags')) {
            $sql = "UPDATE " . DB_PREFIX . "tags SET tags_archived = %s";
            $h->db->query($h->db->prepare($sql, 'N'));
        }
        
        // useractivity
        if ($h->db->table_exists('useractivity')) {
            $sql = "UPDATE " . DB_PREFIX . "useractivity SET useract_archived = %s";
            $h->db->query($h->db->prepare($sql, 'N'));
        }
        
        // RETURN NOW IF NO_ARCHIVE IS SET ***************************** 
        if ($archive == 'no_archive') { 
            $h->message = $h->lang['sb_base_maintenance_archive_removed'];
            $h->messageType = 'green';
            $h->showMessage();
            return true;
        }
        
        // NEXT, START ARCHIVING! ***************************** 
        $archive_text = "-" . $archive . " days"; // e.g. "-365 days"
        $archive_date = date('YmdHis', strtotime($archive_text));
        
        // posts
        if ($h->db->table_exists('posts')) {
            $sql = "UPDATE " . DB_PREFIX . "posts SET post_archived = %s WHERE post_date <= %s";
            $h->db->query($h->db->prepare($sql, 'Y', $archive_date));
        }
        
        // postmeta
        if ($h->db->table_exists('postmeta')) {
            // No date field in postmeta table so join with posts table...
            $sql = "UPDATE " . DB_PREFIX . "postmeta, " . DB_PREFIX . "posts  SET " . DB_PREFIX . "postmeta.postmeta_archived = %s WHERE (" . DB_PREFIX . "posts.post_date <= %s) AND (" . DB_PREFIX . "posts.post_id = " . DB_PREFIX . "postmeta.postmeta_postid)";
            $h->db->query($h->db->prepare($sql, 'Y', $archive_date));
        }
        
        // postvotes
        if ($h->db->table_exists('postvotes')) {
            $sql = "UPDATE " . DB_PREFIX . "postvotes SET vote_archived = %s WHERE vote_date <= %s";
            $h->db->query($h->db->prepare($sql, 'Y', $archive_date));
        }
        
        // comments
        if ($h->db->table_exists('comments')) {
            $sql = "UPDATE " . DB_PREFIX . "comments SET comment_archived = %s WHERE comment_date <= %s";
            $h->db->query($h->db->prepare($sql, 'Y', $archive_date));
        }
        
        // commentvotes
        if ($h->db->table_exists('commentvotes')) {
            $sql = "UPDATE " . DB_PREFIX . "commentvotes SET cvote_archived = %s WHERE cvote_date <= %s";
            $h->db->query($h->db->prepare($sql, 'Y', $archive_date));
        }
        
        // tags
        if ($h->db->table_exists('tags')) {
            $sql = "UPDATE " . DB_PREFIX . "tags SET tags_archived = %s WHERE tags_date <= %s";
            $h->db->query($h->db->prepare($sql, 'Y', $archive_date));
        }

        // useractivity
        if ($h->db->table_exists('useractivity')) {
            $sql = "UPDATE " . DB_PREFIX . "useractivity SET useract_archived = %s WHERE useract_date <= %s";
            $h->db->query($h->db->prepare($sql, 'Y', $archive_date));
        }

        $h->message = $h->lang['sb_base_maintenance_archive_updated'];
        $h->messageType = 'green';
        $h->showMessage();
        return true;

    }
    
    
    /**
     * Show stats on Admin home page
     */
    public function admin_theme_main_stats($h, $vars)
    {
        echo "<li>&nbsp;</li>";
    
        foreach ($vars as $stat_type) {
            $posts = $h->post->stats($h, $stat_type);
            if (!$posts) { $posts = 0; }
            $lang_name = 'sb_base_admin_stats_' . $stat_type;
            echo "<li>" . $h->lang[$lang_name] . ": " . $posts . "</li>";
        }
    }
    
    
    /**
     * User Settings - before saving
     */
    public function user_settings_pre_save($h)
    {
        // Open posts in a new tab?
        if ($h->cage->post->getAlpha('new_tab') == 'yes') { 
            $h->vars['settings']['new_tab'] = "checked"; 
        } else { 
            $h->vars['settings']['new_tab'] = "";
        }
        
        // List links open source url or post page?
        if ($h->cage->post->getAlpha('link_action') == 'source') { 
            $h->vars['settings']['link_action'] = "checked"; 
        } else { 
            $h->vars['settings']['link_action'] = "";
        }
    }
    
    
    /**
     * User Settings - fill the form
     */
    public function user_settings_fill_form($h)
    {
        if ($h->vars['settings']['new_tab']) { 
            $h->vars['new_tab_yes'] = "checked"; 
            $h->vars['new_tab_no'] = ""; 
        } else { 
            $h->vars['new_tab_yes'] = ""; 
            $h->vars['new_tab_no'] = "checked"; 
        }
        
        if ($h->vars['settings']['link_action']) { 
            $h->vars['link_action_source'] = "checked"; 
            $h->vars['link_action_post'] = ""; 
        } else { 
            $h->vars['link_action_source'] = ""; 
            $h->vars['link_action_post'] = "checked"; 
        }
    }
    
    
    /**
     * User Settings - html for form
     */
    public function user_settings_extra_settings($h)
    {
        echo "<tr>\n";
            // OPEN POSTS IN A NEW TAB?
        echo "<td>" . $h->lang['sb_base_users_settings_open_new_tab'] . "</td>\n";
        echo "<td><input type='radio' name='new_tab' value='yes' " . $h->vars['new_tab_yes'] . "> " . $h->lang['users_settings_yes'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='new_tab' value='no' " . $h->vars['new_tab_no'] . "> " . $h->lang['users_settings_no'] . "</td>\n";
        echo "</tr>\n";
        
        echo "<tr>\n";
            // OPEN POSTS IN A NEW TAB?
        echo "<td>" . $h->lang['sb_base_users_settings_link_action'] . "</td>\n";
        echo "<td><input type='radio' name='link_action' value='source' " . $h->vars['link_action_source'] . "> " . $h->lang['sb_base_users_settings_source'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='link_action' value='post' " . $h->vars['link_action_post'] . "> " . $h->lang['sb_base_users_settings_post'] . "</td>\n";
        echo "</tr>\n";
    }
}
?>
