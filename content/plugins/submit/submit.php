<?php
/**
 * name: Submit
 * description: Submit and manage stories.
 * version: 1.8
 * folder: submit
 * class: Submit
 * hooks: hotaru_header, header_meta, header_include, header_include_raw, admin_header_include_raw, install_plugin, navigation, theme_index_replace, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings, admin_maintenance_database, admin_maintenance_top, admin_theme_main_stats, user_settings_pre_save, user_settings_fill_form, user_settings_extra_settings
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
 
return false; die(); // die on direct access.

class Submit extends PluginFunctions
{
    /**
     * If they don't already exist, create "posts" and "postmeta" tables
     */
    public function install_plugin()
    {
        // Create a new empty table called "posts"
        $exists = $this->db->table_exists('posts');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "posts` (
              `post_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `post_archived` enum('Y','N') NOT NULL DEFAULT 'N',
              `post_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `post_author` int(20) NOT NULL DEFAULT 0,
              `post_category` int(20) NOT NULL DEFAULT 1,
              `post_status` varchar(32) NOT NULL DEFAULT 'processing',
              `post_date` timestamp NOT NULL,
              `post_title` varchar(255) NULL, 
              `post_orig_url` varchar(255) NULL, 
              `post_domain` varchar(255) NULL, 
              `post_url` varchar(255) NULL, 
              `post_content` text NULL,
              `post_tags` text NULL,
              `post_subscribe` tinyint(1) NOT NULL DEFAULT '0',
              `post_updateby` int(20) NOT NULL DEFAULT 0, 
              FULLTEXT (`post_title`, `post_domain`, `post_url`, `post_content`, `post_tags`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Story Posts';";
            $this->db->query($sql); 
        }
        
        // Create a new empty table called "postmeta"
        $exists = $this->db->table_exists('postmeta');
        if (!$exists) {
            //echo "table doesn't exist. Stopping before creation."; exit;
            $sql = "CREATE TABLE `" . DB_PREFIX . "postmeta` (
              `postmeta_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `postmeta_archived` enum('Y','N') NOT NULL DEFAULT 'N',
              `postmeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `postmeta_postid` int(20) NOT NULL DEFAULT 0,
              `postmeta_key` varchar(255) NULL,
              `postmeta_value` text NULL,
               `postmeta_updateby` int(20) NOT NULL DEFAULT 0, 
              INDEX  (`postmeta_postid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Meta';";
            $this->db->query($sql); 
        }
        
        if (!$this->db->column_exists('posts', 'post_archived')) {
            // add new post_archived field
            $sql = "ALTER TABLE " . DB_PREFIX . "posts ADD post_archived ENUM(%s, %s) NOT NULL DEFAULT %s AFTER post_id";
            $this->db->query($this->db->prepare($sql, 'Y', 'N', 'N'));
        }
        
        if (!$this->db->column_exists('postmeta', 'postmeta_archived')) {
            // add new post_archived field
            $sql = "ALTER TABLE " . DB_PREFIX . "postmeta ADD postmeta_archived ENUM(%s, %s) NOT NULL DEFAULT %s AFTER postmeta_id";
            $this->db->query($this->db->prepare($sql, 'Y', 'N', 'N'));
        }
        
        
        // Permissions
        $site_perms = $this->current_user->getDefaultPermissions('all');
        if (!isset($site_perms['can_submit'])) { 
            $perms['options']['can_submit'] = array('yes', 'no', 'mod');
            $perms['options']['can_edit_posts'] = array('yes', 'no', 'own');
            $perms['options']['can_delete_posts'] = array('yes', 'no');
            $perms['options']['can_post_without_link'] = array('yes', 'no');
            
            $perms['can_submit']['admin'] = 'yes';
            $perms['can_submit']['supermod'] = 'yes';
            $perms['can_submit']['moderator'] = 'yes';
            $perms['can_submit']['member'] = 'yes';
            $perms['can_submit']['undermod'] = 'mod';
            $perms['can_submit']['default'] = 'no';
            
            $perms['can_edit_posts']['admin'] = 'yes';
            $perms['can_edit_posts']['supermod'] = 'yes';
            $perms['can_edit_posts']['moderator'] = 'yes';
            $perms['can_edit_posts']['member'] = 'own';
            $perms['can_edit_posts']['undermod'] = 'own';
            $perms['can_edit_posts']['default'] = 'no';
            
            $perms['can_delete_posts']['admin'] = 'yes';
            $perms['can_delete_posts']['supermod'] = 'yes';
            $perms['can_delete_posts']['default'] = 'no';
            
            $perms['can_post_without_link']['admin'] = 'yes';
            $perms['can_post_without_link']['supermod'] = 'yes';
            $perms['can_post_without_link']['default'] = 'no';
            
            $this->current_user->updateDefaultPermissions($perms);
        }
        

        // Default settings 
        $submit_settings = $this->getSerializedSettings();
        
        if (!isset($submit_settings['post_enabled'])) { $submit_settings['post_enabled'] = "checked"; }
        if (!isset($submit_settings['post_author'])) { $submit_settings['post_author'] = "checked"; }
        if (!isset($submit_settings['post_date'])) { $submit_settings['post_date'] = "checked"; }
        if (!isset($submit_settings['post_content'])) { $submit_settings['post_content'] = "checked"; }
        if (!isset($submit_settings['post_content_length'])) { $submit_settings['post_content_length'] = 50; }
        if (!isset($submit_settings['post_summary'])) { $submit_settings['post_summary'] = "checked"; }
        if (!isset($submit_settings['post_summary_length'])) { $submit_settings['post_summary_length'] = 200; }
        if (!isset($submit_settings['post_posts_per_page'])) { $submit_settings['post_posts_per_page'] = 10; }
        if (!isset($submit_settings['post_allowable_tags'])) { $submit_settings['post_allowable_tags'] = "<b><i><u><a><blockquote><strike>"; }
        if (!isset($submit_settings['post_url_limit'])) { $submit_settings['post_url_limit'] = 0; }
        if (!isset($submit_settings['post_daily_limit'])) { $submit_settings['post_daily_limit'] = 0; }
        if (!isset($submit_settings['post_freq_limit'])) { $submit_settings['post_freq_limit'] = 0; }
        if (!isset($submit_settings['post_set_pending'])) { $submit_settings['post_set_pending'] = ""; } // sets all new posts to pending 
        if (!isset($submit_settings['post_x_posts'])) { $submit_settings['post_x_posts'] = 1; }
        if (!isset($submit_settings['post_email_notify'])) { $submit_settings['post_email_notify'] = ""; }
        if (!isset($submit_settings['post_email_notify_mods'])) { $submit_settings['post_email_notify_mods'] = array(); }
        if (!isset($submit_settings['post_archive'])) { $submit_settings['post_archive'] = "no_archive"; }
        
        $this->updateSetting('submit_settings', serialize($submit_settings));
        
        // Add "open in new tab" option to the default user settings
        require_once(PLUGINS . 'users/libs/UserFunctions.php');
        $uf = new UserFunctions($this->hotaru);
        $base_settings = $uf->getDefaultSettings('base'); // originals from plugins
        $site_settings = $uf->getDefaultSettings('site'); // site defaults updated by admin
        if (!isset($base_settings['new_tab'])) { 
            $base_settings['new_tab'] = ""; $site_settings['new_tab'] = "";
            $uf->updateDefaultSettings($base_settings, 'base'); $uf->updateDefaultSettings($site_settings, 'site');
        }
        if (!isset($base_settings['link_action'])) { 
            $base_settings['link_action'] = ""; $site_settings['link_action'] = "";
            $uf->updateDefaultSettings($base_settings, 'base'); $uf->updateDefaultSettings($site_settings, 'site');
        }
        
        // fixes PHP 2.3 errors on installation:
        require_once(PLUGINS . 'submit/libs/Post.php');
        $this->hotaru->post = new Post($this->hotaru);  // adds Post object to Hotaru class
    }
    
    
    /**
     * Define global "table_posts" and "table_postmeta" constants for referring to the db tables
     */
    public function hotaru_header()
    {    
        require_once(PLUGINS . 'submit/libs/Post.php');
        $this->hotaru->post = new Post($this->hotaru);  // adds Post object to Hotaru class
        
        if (!defined('TABLE_POSTS')) { define("TABLE_POSTS", DB_PREFIX . 'posts'); }
        if (!defined('TABLE_POSTMETA')) { define("TABLE_POSTMETA", DB_PREFIX . 'postmeta'); }
    
        // include language file
        $this->includeLanguage();
                
        require_once(PLUGINS . 'submit/libs/Post.php');
        require_once(EXTENSIONS . 'Paginated/Paginated.php');
        require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
        
        $this->pluginHook('submit_hotaru_header_1');
        
        $pagename = $this->hotaru->getPageName();
        
        if (is_numeric($pagename)) {
            // Page name is a number so it must be a post with non-friendly urls
            $this->hotaru->post->readPost($pagename);    // read current post
            $this->hotaru->pageType = 'post';
            $this->hotaru->title = $this->hotaru->post->title;
            
        } elseif ($post_id = $this->hotaru->post->isPostUrl($pagename)) {
            // Page name belongs to a story
            $this->hotaru->post->readPost($post_id);    // read current post
            $this->hotaru->pageType = 'post';
            $this->hotaru->title = $this->hotaru->post->title;
            
        } else {
            $this->hotaru->post->readPost();    // read current post settings only
            $this->hotaru->pageType = 'list';
            
            // NOTE: The links for sorting are only shown when using the Vote Simple plugin or equivalent
            // Only show these "sort" titles if there's no category or tag in the url.
            if ($sort = $this->cage->get->testPage('sort')
                && !$this->cage->get->keyExists('category')
                && !$this->cage->get->keyExists('tag')
                && !$this->cage->get->keyExists('type')
                && !$this->cage->get->keyExists('user')) {
                // Determine TITLE tags for a page of sorted posts:
                switch ($sort) {
                    case 'top-24-hours':
                        $this->hotaru->title = $this->lang["post_breadcrumbs_top_24_hours"];
                        break;
                    case 'top-48-hours':
                        $this->hotaru->title = $this->lang["post_breadcrumbs_top_48_hours"];
                        break;
                    case 'top-7-days':
                        $this->hotaru->title = $this->lang["post_breadcrumbs_top_7_days"];
                        break;
                    case 'top-30-days':
                        $this->hotaru->title = $this->lang["post_breadcrumbs_top_30_days"];
                        break;
                    case 'top-365-days':
                        $this->hotaru->title = $this->lang["post_breadcrumbs_top_365_days"];
                        break;
                    case 'top-all-time':
                        $this->hotaru->title = $this->lang["post_breadcrumbs_top_all_time"];
                        break;
                }
            }
        }
        $this->pluginHook('submit_hotaru_header_2');
        $this->hotaru->title = stripslashes($this->hotaru->title);
    }
    
    
    /**
     * Match meta tag to a post's description (keywords is done in the Tags plugin)
     */
    public function header_meta()
    {    
        if ($this->hotaru->pageType == 'post') {
            $meta_content = truncate(stripslashes(htmlentities($this->hotaru->post->content,ENT_QUOTES,'UTF-8')), 200);
            echo '<meta name="description" content="' . $meta_content . '">' . "\n";
            return true;
        }
    }
    
    
    /**
     * Include jQuery for hiding and showing email options in plugin settings
     */
    public function admin_header_include_raw()
    {
        $admin = new Admin();
        
        if ($admin->isSettingsPage('submit')) {
            echo "<script type='text/javascript'>\n";
            echo "$(document).ready(function(){\n";
                echo "$('#email_notify').click(function () {\n";
                echo "$('#email_notify_options').slideToggle();\n";
                echo "});\n";
            echo "});\n";
            echo "</script>\n";
        }
    }
    
    
    /**
     * Add a "submit a story" link to the navigation bar
     */
    public function navigation()
    {   
        if ($this->current_user->loggedIn && isset($this->hotaru->post->useSubmission)) {
            if ($this->hotaru->title == 'submit') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . $this->hotaru->url(array('page'=>'submit')) . "'>" . $this->lang['submit_submit_a_story'] . "</a></li>\n";
        }
    }
    
    
    /**
     * Output raw javascript directly to the header (instead of caching a .js file)
     */
    public function header_include_raw()
    {
        /* This code (courtesy of Pligg.com and SocialWebCMS.com) pops up a 
           box asking the user of they are sure they want to leave the page
           without submitting their post. */
           
        if ($this->hotaru->isPage('submit2')) {
            echo '
                <script type="text/javascript">
        
                var safeExit = false;
            
                window.onbeforeunload = function (event) 
                {
                    if (safeExit)
                        return;
        
                    if (!event && window.event) 
                              event = window.event;
                              
                       event.returnValue = "' . $this->lang['submit_form_submit_accidental_click'] . '";
                }
                
                </script>
            ';
        }
    }
    
    
    /**
     * Checks results from submit form 2.
     *
     * @return bool
     */
    public function theme_index_replace()
    {
        if ($this->hotaru->isPage('submit2') && $this->hotaru->post->useSubmission) {
            if ($this->current_user->loggedIn) {
                         
                if ($this->cage->post->getAlpha('submit2') == 'true') {             
                    $post_orig_url = $this->cage->post->testUri('post_orig_url'); 
                    if ((!$post_orig_url) && ($this->current_user->getPermission('can_post_without_link') == 'yes')) { 
                        $this->hotaru->post->useLink = false;
                    }
                    if (!$this->check_for_errors_2()) { 
                        $this->process_submission($post_orig_url);
                    }
                }
            }
            
        } elseif ($this->hotaru->isPage('submit3') && $this->hotaru->post->useSubmission) {
             
            if ($this->current_user->loggedIn) {
    
                if ($this->cage->post->getAlpha('submit3') == 'edit') {             
                
                    $post_id = $this->cage->post->getInt('post_id'); 
                    $this->hotaru->post->readPost($post_id);
                }
                             
                 
                // SUCCESS ! Submit the post...
                if ($this->cage->post->getAlpha('submit3') == 'confirm') {             
        
                    $post_id = $this->cage->post->getInt('post_id');
                    $this->hotaru->post->readPost($post_id);
                    $this->hotaru->post->changeStatus('new');
                    
                    $return = 0; // will return false later if set to 1.
                    
                    $this->pluginHook('submit_step_3_pre_trackback'); // Akismet uses this to change the status
                    
                    // Get settings to determine the status of this post
                    $submit_settings = $this->getSerializedSettings();
                    $set_pending = $submit_settings['post_set_pending'];

                    if ($set_pending == 'some_pending') {
                        $posts_approved = $this->hotaru->post->postsApproved($this->current_user->id);
                        $x_posts_needed = $submit_settings['post_x_posts'];
                    }

                    
                    // Set to pending is the user's permissions for "can_submit" are "mod" OR
                    // if "Put all new posts in moderation" has been checked in Admin->Submit
                    if (   ($this->current_user->getPermission('can_submit') == 'mod')
                        || ($set_pending == 'all_pending')
                        || (($set_pending == 'some_pending') && ($posts_approved <= $x_posts_needed)))
                    {
                    // Submitted posts given 'pending' for this user
                        $this->hotaru->post->changeStatus('pending');
                        $this->hotaru->messages[$this->lang['submit_form_moderation']] = 'green';
                        $return = 1; // will return false just after we notify admins of the post (see about 10 lines down)
                    }
                    
                    // get settings
                    $submit_settings = $this->getSerializedSettings();

                    // notify chosen mods of new post by email if enabled and UserFunctions file exists
                    if (($submit_settings['post_email_notify']) && (file_exists(PLUGINS . 'users/libs/UserFunctions.php')))
                    {
                        require_once(PLUGINS . 'users/libs/UserFunctions.php');
                        $uf = new UserFunctions($this->hotaru);
                        $uf->notifyMods('post', $this->hotaru->post->status, $this->hotaru->post->id);
                    }
                    
                    if ($return == 1) { return false; } // post is pending so we don't want to send a trackback. Return now.
                    
                    $this->hotaru->post->sendTrackback();
                    if ($this->hotaru->post->useLatest) {
                        header("Location: " . $this->hotaru->url(array('page'=>'latest')));    // Go to the Latest page
                    } else {
                        header("Location: " . BASEURL);    // Go home  
                    }
                    die();
                }
            }
            
        } elseif ($this->hotaru->isPage('edit_post')) {
            if ($this->current_user->loggedIn) {
                       
                if ($this->cage->post->getAlpha('edit_post') == 'true') {
                    $post_orig_url = $this->cage->post->testUri('post_orig_url'); 
                    if (!$this->check_for_errors_2()) { 
                        $this->process_submission($post_orig_url);
                        
                        if ($this->cage->post->testAlnumLines('from') == 'post_man')
                        {
                            // Build the redirect link to send us back to Post Manager
                            
                            $redirect_link = BASEURL . "admin_index.php?page=plugin_settings&plugin=post_manager";
                            if ($this->cage->post->testAlnumLines('post_status_filter')) {
                                $redirect_link .= "&type=filter";
                                $redirect_link .= "&post_status_filter=" . $this->cage->post->testAlnumLines('post_status_filter');
                            }
                            if ($this->cage->post->getMixedString2('search_value')) {
                                $redirect_link .= "&type=search";
                                $redirect_link .= "&search_value=" . $this->cage->post->getMixedString2('search_value');
                            }
                            $redirect_link .= "&pg=" . $this->cage->post->testInt('pg');
                            header("Location: " . $redirect_link);    // Go back to where we were in Post Manager
                        }
                        else 
                        {
                            // Send us back to the post page itself
                            header("Location: " . $this->hotaru->url(array('page'=>$this->hotaru->post->id)));    // Go to the post
                        }
                        die();
                    }
                }
                
                if ($this->cage->get->getAlpha('action') == 'delete') {
                    if ($this->current_user->getPermission('can_delete_posts') == 'yes') { // double-checking
                        $post_id = $this->cage->get->testInt('post_id');
                        $this->hotaru->post->readPost($post_id); 
                        $this->pluginHook('submit_edit_post_delete'); // Akismet uses this to report the post as spam
                        $this->hotaru->post->deletePost(); 
                        $this->hotaru->messages[$this->lang["submit_edit_post_deleted"]] = 'red';
                    }
                }
            }
        
        } elseif ($this->hotaru->isPage('rss')) {
        
            // Display RSS Feed - index.php?page=rss&status=new&limit=10
            $this->hotaru->post->rssFeed();
            return true;
        }
    
        return false;
    }
    
    
    /**
     * Determines which submit page to display
     *
     * @return bool
     */
    public function theme_index_main()
    {
        if ($this->hotaru->isPage('submit')) {
              
            if ($this->current_user->loggedIn) {

                if (!$this->hotaru->post->useSubmission) {
                    $this->hotaru->messages[$this->lang['submit_disabled']] = 'red';
                    echo $this->hotaru->showMessages();
                    return true;
                }
                    // getAlpha for Submit page, keyExists for EVB & Bookmarklet
                  if (($this->cage->post->getAlpha('submit1') == 'true')
                        || $this->cage->get->keyExists('url')) {
                    if (!$this->check_for_errors_1()) { 
                        // No errors found, proceed to step 2
                        if (!$this->hotaru->post->useLink) { 
                            $this->hotaru->vars['post_orig_url'] = "";
                            $this->hotaru->vars['post_orig_title'] = "";
                        } else {
                            $this->hotaru->vars['post_orig_url'] = $this->cage->post->testUri('post_orig_url'); 
                            if (!$this->hotaru->vars['post_orig_url']) {
                                $this->hotaru->vars['post_orig_url'] = $this->cage->get->testUri('url'); // if EVB & Bookmarklet
                            }
                            $this->hotaru->vars['post_orig_title'] = $this->hotaru->post->fetchTitle($this->hotaru->vars['post_orig_url']);
                        }
                        $this->hotaru->displayTemplate('submit_step2', 'submit');
                        return true;
                        
                    } else {
                        // Errors found, go back to step 1 
                        $this->hotaru->vars['post_orig_url'] = $this->cage->post->testUri('post_orig_url');
                        $this->hotaru->displayTemplate('submit_step1', 'submit');
                        return true;
                    }
                } else {
                    // First time to step 1...
                    if ($this->current_user->getPermission('can_post_without_link') == 'yes') { 
                        $this->hotaru->post->useLink = false;
                    }
                    $this->hotaru->displayTemplate('submit_step1', 'submit');
                    return true;
                }
            } else {
                return false;
            }
            
        } elseif ($this->hotaru->isPage('submit2')) {
             
            if ($this->current_user->loggedIn) {
            
                if (!$this->hotaru->post->useSubmission) {
                    $this->hotaru->messages[$this->lang['submit_disabled']] = 'red';
                    echo $this->hotaru->showMessages();
                    return true;
                }
                 if ($this->cage->post->getAlpha('submit2') == 'true') {
                    $this->hotaru->vars['post_orig_url'] = $this->cage->post->testUri('post_orig_url'); 
                    if ($this->hotaru->post->status == 'processing') {     
                        // No errors, go to step 3...    
                        $this->hotaru->post->readPost($this->hotaru->post->id);
                        $this->hotaru->displayTemplate('submit_step3', 'submit');
                        return true;
                    } else {
                        // Errors found, show step 2 again...
                        $this->hotaru->displayTemplate('submit_step2', 'submit');
                        return true;
                    }
                }
            }
        
        } elseif ($this->hotaru->isPage('submit3')) {
             
            if ($this->current_user->loggedIn) {
            
                if (!$this->hotaru->post->useSubmission) {
                    $this->hotaru->messages[$this->lang['submit_disabled']] = 'red';
                    echo $this->hotaru->showMessages();
                    return true;
                }
                 
                 if ($this->cage->post->getAlpha('submit3') == 'edit') {             
                     $this->hotaru->displayTemplate('submit_step2', 'submit');
                     return true;
                }
                
                echo $this->hotaru->showMessages();
                return true;
            }
            
        } elseif ($this->hotaru->isPage('edit_post')) {
            if ($this->current_user->loggedIn) {
                if ($this->cage->get->keyExists('sourceurl') || $this->cage->get->keyExists('post_id')) {
                    $this->hotaru->displayTemplate('submit_edit_post', 'submit');
                    return true;
                }
            }
    
        } elseif ($this->hotaru->isPage('main')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_main');
            if ($result && is_array($result)) { return true; }

            // Show the list of posts
            $this->hotaru->displayTemplate('list', 'submit');
            return true;
            
        } elseif ($this->hotaru->pageType == 'post') {
            // We found out this is a post from the hotaru_header function above.
           
            $this->hotaru->displayTemplate('post', 'submit');
            return true;
            
        } elseif ($this->hotaru->isPage('latest')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_latest');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $this->hotaru->displayTemplate('list', 'submit');
            return true;
            
        } elseif ($this->hotaru->isPage('upcoming')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_upcoming');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $this->hotaru->displayTemplate('list', 'submit');
            return true;
            
        } elseif ($this->hotaru->isPage('top')) {   // used only for filtering users
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_top');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $this->hotaru->displayTemplate('list', 'submit');
            return true;
            
        } elseif ($this->hotaru->isPage('all')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_all');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $this->hotaru->displayTemplate('list', 'submit');
            return true;
            
        } else {        
            return false;
        }
        
        return false;
    }
    
    
    /**
     * Checks submit_step1 for errors
     */
    public function check_for_errors_1()
    {
        // ******** CHECK URL ********
        
        // get the settings we need:
        $submit_settings = $this->getSerializedSettings();
        $daily_limit = $submit_settings['post_daily_limit'];
        $freq_limit = $submit_settings['post_freq_limit'];
        
        // allow submission to continue without a link
        if ($this->cage->post->keyExists('use_link') 
            && ($this->current_user->getPermission('can_post_without_link') == 'yes')) { 
            $this->hotaru->post->useLink = false; 
            return false; //no error
        }
        
        $post_orig_url_check = $this->cage->post->testUri('post_orig_url');
        
        if (!$post_orig_url_check) {
            $post_orig_url_check = $this->cage->get->testUri('url'); // try to get a url from GET (in case of EVB)
        }
        
        if (!$post_orig_url_check) {
            // No url present...
            $this->hotaru->message = $this->lang['submit_form_url_not_present_error'];
            $this->hotaru->messageType = 'red';
            $error = 1;
        } elseif ($this->hotaru->post->urlExists($post_orig_url_check)) {
            // URL already exists...
            $this->hotaru->message = $this->lang['submit_form_url_already_exists_error'];
            $this->hotaru->messageType = 'red';
            $error = 1;
        } elseif ($this->current_user->getPermission('can_submit') == 'no') {
            // No permission to submit posts
            $this->hotaru->message = $this->lang['submit_form_no_permission'];
            $this->hotaru->messageType = 'red';
            $error = 1;
        } elseif ($this->checkBlocked($post_orig_url_check)) {
            // URL is blocked
            $this->hotaru->message = $this->lang['submit_form_url_blocked'];
            $this->hotaru->messageType = 'red';
            $error = 1;
        } elseif (($this->hotaru->current_user->role == 'member' || $this->hotaru->current_user->role == 'undermod')
                   && $daily_limit && ($daily_limit < $this->hotaru->post->countPosts(24))) { 
            // exceeded daily limit
            $this->hotaru->message = $this->lang['submit_form_daily_limit_exceeded'];
            $this->hotaru->messageType = 'red';
            $error = 1;
        } elseif (($this->hotaru->current_user->role == 'member' || $this->hotaru->current_user->role == 'undermod')
                   && $freq_limit && ($this->hotaru->post->countPosts(0, $freq_limit) > 0)) { 
            // already submitted a post in the last X minutes. Needs to wait before submitting again
            $this->hotaru->message = $this->lang['submit_form_freq_limit_error'];
            $this->hotaru->messageType = 'red';
            $error = 1;
        } else {
            // URL is okay.
            $error = 0;
        }
        
        // Return true if error is found
        if ($error == 1) { return true; } else { return false; }
    }
    
    
    /**
     * Check for errors in submit 2 or when editing a post
     *
     * @return bool
     */
    public function check_for_errors_2() 
    {
        $post_id = $this->cage->post->getInt('post_id'); // 0 unless come back from step 3.
        
        if ($this->cage->post->keyExists('edit_post')) { $edit = true; } else {$edit = false; }
        
        // get the settings we need:
        $submit_settings = $this->getSerializedSettings();
        $url_limit = $submit_settings['post_url_limit'];
        
    
        // ******** CHECK URL ********
        $error_url = 0;
        // Only for user with Edit Post permissions
        if ($edit) {
            $orig_url_check = $this->cage->post->testUri('post_orig_url'); 
            
            if (!$orig_url_check) {
                // No url present...
                $this->hotaru->messages[$this->lang['submit_form_url_not_complete_error']] = "red";
                $error_url = 1;
            }
        }
        
        // ******** CHECK TITLE ********
        
        $title_check = $this->cage->post->noTags('post_title');    
            
        if (!$title_check) {
            // No title present...
            $this->hotaru->messages[$this->lang['submit_form_title_not_present_error']] = "red";
            $error_title= 1;
        } elseif (!$edit && $this->hotaru->post->titleExists($title_check)) {
            // title already exists...
            if ($post_id != $this->hotaru->post->titleExists($title_check)) {
                $this->hotaru->messages[$this->lang['submit_form_title_already_exists_error']] = "red";
                $error_title = 1;
            } else {
                // the matching title is for the post we're currently modifying so no error...
                $error_title = 0;
            }
        } else {
            // title is okay.
            $error_title = 0;
        }
        
        // ******** CHECK DESCRIPTION ********
        if ($this->hotaru->post->useContent) {
            $content_check = sanitize($this->cage->post->getHtmLawed('post_content'), 2, $this->hotaru->post->allowableTags);
            $this->hotaru->post->content = $content_check;
                    
            if (!$content_check) {
                // No content present...
                $this->hotaru->messages[$this->lang['submit_form_content_not_present_error']] = "red";
                $error_content = 1;
            } elseif (strlen($content_check) < $this->hotaru->post->post_content_length) {
                // content is too short
                $this->hotaru->messages[$this->lang['submit_form_content_too_short_error']] = "red";
                $error_content = 1;
            } elseif (($this->hotaru->current_user->role == 'member' || $this->hotaru->current_user->role == 'undermod')
                        && $url_limit && ($url_limit < $this->hotaru->post->countUrls())) { 
                // content contains too many links
                $this->hotaru->messages[$this->lang['submit_form_content_too_many_links']] = "red";
                $error_content = 1;
            } else {
                // content is okay.
                $error_content = 0;
            }
        }
        
        
        // Check for errors from plugin fields, e.g. Tags
        $error_hooks = 0;
        $error_array = $this->pluginHook('submit_form_2_check_for_errors');
        if (is_array($error_array)) {
            foreach ($error_array as $err) { if ($err == 1) { $error_hooks = 1; } }
        }
        
        // Return true if error is found
        if ($error_url == 1 || $error_title == 1 || $error_content == 1 || $error_hooks == 1) { return true; } else { return false; }
    }
    
    
    /**
     * Saves the submitted story to the database
     */
    public function process_submission($post_orig_url)
    {
        if ($this->cage->post->getAlpha('submit2') == 'true') {    
        
            $this->hotaru->post->id = $this->cage->post->getInt('post_id');
            
            if ($this->hotaru->post->useLink || ($this->hotaru->post->id != 0)) {
                $this->hotaru->post->origUrl = $this->cage->post->testUri('post_orig_url'); 
            } else {
                $this->hotaru->post->origUrl = "self";
            }
            
            $this->hotaru->post->title = $this->cage->post->noTags('post_title');
            $this->hotaru->post->url = $this->cage->post->getFriendlyUrl('post_title');
            $this->hotaru->post->content = sanitize($this->cage->post->getHtmLawed('post_content'), 2, $this->hotaru->post->allowableTags);
            $this->hotaru->post->status = 'processing';
            $this->hotaru->post->author = $this->current_user->id;
            
            $this->pluginHook('submit_form_2_process_submission');
            
            if ($this->hotaru->post->id != 0) {
                $this->hotaru->post->updatePost();    // Updates an existing post (e.g. returning to step 2 from step 3 to modify it)
            } else {
                $this->hotaru->post->addPost();    // Adds a new post
            }
            
        } elseif ($this->cage->post->keyExists('edit_post')) { 
            
            // Editing an existing post.
            $this->hotaru->post->id = $this->cage->post->getInt('post_id');
            $this->hotaru->post->readPost($this->hotaru->post->id);
            $this->hotaru->post->origUrl = $this->cage->post->testUri('post_orig_url'); 
            $this->hotaru->post->title = $this->cage->post->noTags('post_title');
            $this->hotaru->post->url = $this->cage->post->getFriendlyUrl('post_title');
            $this->hotaru->post->content = sanitize($this->cage->post->getHtmLawed('post_content'), 2, $this->hotaru->post->allowableTags);
            
            // only update if option available (i.e. when an admin is editing the post):
            if ($this->cage->post->keyExists('post_status')) {
                $this->hotaru->post->status = $this->cage->post->testAlnumLines('post_status');
                $this->pluginHook('submit_edit_post_change_status');
            }
            
            $this->pluginHook('submit_form_2_process_submission');
            
            $this->hotaru->post->updatePost();
        }
    }

    /**
     * Check if url or domain is on the blocked list
     *
     * @param string $url
     * @return bool - true if blocked
     */
    public function checkBlocked($url)
    {
        // Is url blocked?
        if ($this->isBlocked('url', $url)) {
            return true;
        }
        
        // Is domain blocked?
        $domain = get_domain($url); // returns the domain including http 
        if ($this->isBlocked('url', $domain)) {
            return true;
        }
        
        // Is domain extension blocked?
        $host = parse_url($url, PHP_URL_HOST); // returns www.google.com
        $ext = substr(strrchr($host, '.'), 1); 
        if ($this->isBlocked('url', '.' . $ext)) { // dot added here
            return true;
        } 
                        
        return false;   // not blocked
    }
    
    
    /**
     * Archive option on Maintenance page
     */
    public function admin_maintenance_database()
    {
        $submit_settings = $this->getSerializedSettings();
        $archive = $submit_settings['post_archive'];
        echo "<li><a href='" . BASEURL . "admin_index.php?page=maintenance&amp;action=update_archive'>";
        echo $this->hotaru->lang["submit_maintenance_update_archive"] . "</a> - ";
        if ($archive == 'no_archive') {
            echo $this->hotaru->lang["submit_maintenance_update_archive_remove"];
        } else {
            echo $this->hotaru->lang["submit_maintenance_update_archive_desc_1"];
            echo $this->lang["submit_settings_post_archive_$archive"];
            echo $this->hotaru->lang["submit_maintenance_update_archive_desc_2"];
        }
        echo "</li>";
    }
    
    
    /**
     * Perform archiving tasks
     */
    public function admin_maintenance_top()
    {
        if ($this->cage->get->testAlnumLines('action') != 'update_archive') { return false; }
        
        $submit_settings = $this->getSerializedSettings();
        $archive = $submit_settings['post_archive'];
        
        // FIRST, WE NEED TO RESET THE ARCHIVE, setting all archive fields to "N":
        
        // posts
        if ($this->db->table_exists('posts')) {
            $sql = "UPDATE " . DB_PREFIX . "posts SET post_archived = %s";
            $this->db->query($this->db->prepare($sql, 'N'));
        }
        
        // postmeta
        if ($this->db->table_exists('postmeta')) {
            $sql = "UPDATE " . DB_PREFIX . "postmeta SET postmeta_archived = %s";
            $this->db->query($this->db->prepare($sql, 'N'));
        }
        
        // postvotes
        if ($this->db->table_exists('postvotes')) {
            $sql = "UPDATE " . DB_PREFIX . "postvotes SET vote_archived = %s";
            $this->db->query($this->db->prepare($sql, 'N'));
        }
        
        // comments
        if ($this->db->table_exists('comments')) {
            $sql = "UPDATE " . DB_PREFIX . "comments SET comment_archived = %s";
            $this->db->query($this->db->prepare($sql, 'N'));
        }
        
        // commentvotes
        if ($this->db->table_exists('commentvotes')) {
            $sql = "UPDATE " . DB_PREFIX . "commentvotes SET cvote_archived = %s";
            $this->db->query($this->db->prepare($sql, 'N'));
        }
        
        // tags
        if ($this->db->table_exists('tags')) {
            $sql = "UPDATE " . DB_PREFIX . "tags SET tags_archived = %s";
            $this->db->query($this->db->prepare($sql, 'N'));
        }
        
        // useractivity
        if ($this->db->table_exists('useractivity')) {
            $sql = "UPDATE " . DB_PREFIX . "useractivity SET useract_archived = %s";
            $this->db->query($this->db->prepare($sql, 'N'));
        }
        
        // RETURN NOW IF NO_ARCHIVE IS SET ***************************** 
        if ($archive == 'no_archive') { 
            $this->hotaru->message = $this->lang['submit_maintenance_archive_removed'];
            $this->hotaru->messageType = 'green';
            $this->hotaru->showMessage();
            return true;
        }
        
        // NEXT, START ARCHIVING! ***************************** 
        $archive_text = "-" . $archive . " days"; // e.g. "-365 days"
        $archive_date = date('YmdHis', strtotime($archive_text));
        
        // posts
        if ($this->db->table_exists('posts')) {
            $sql = "UPDATE " . DB_PREFIX . "posts SET post_archived = %s WHERE post_date <= %s";
            $this->db->query($this->db->prepare($sql, 'Y', $archive_date));
        }
        
        // postmeta
        if ($this->db->table_exists('postmeta')) {
            // No date field in postmeta table so join with posts table...
            $sql = "UPDATE " . DB_PREFIX . "postmeta, " . DB_PREFIX . "posts  SET " . DB_PREFIX . "postmeta.postmeta_archived = %s WHERE (" . DB_PREFIX . "posts.post_date <= %s) AND (" . DB_PREFIX . "posts.post_id = " . DB_PREFIX . "postmeta.postmeta_postid)";
            $this->db->query($this->db->prepare($sql, 'Y', $archive_date));
        }
        
        // postvotes
        if ($this->db->table_exists('postvotes')) {
            $sql = "UPDATE " . DB_PREFIX . "postvotes SET vote_archived = %s WHERE vote_date <= %s";
            $this->db->query($this->db->prepare($sql, 'Y', $archive_date));
        }
        
        // comments
        if ($this->db->table_exists('comments')) {
            $sql = "UPDATE " . DB_PREFIX . "comments SET comment_archived = %s WHERE comment_date <= %s";
            $this->db->query($this->db->prepare($sql, 'Y', $archive_date));
        }
        
        // commentvotes
        if ($this->db->table_exists('commentvotes')) {
            $sql = "UPDATE " . DB_PREFIX . "commentvotes SET cvote_archived = %s WHERE cvote_date <= %s";
            $this->db->query($this->db->prepare($sql, 'Y', $archive_date));
        }
        
        // tags
        if ($this->db->table_exists('tags')) {
            $sql = "UPDATE " . DB_PREFIX . "tags SET tags_archived = %s WHERE tags_date <= %s";
            $this->db->query($this->db->prepare($sql, 'Y', $archive_date));
        }
        
        // useractivity
        if ($this->db->table_exists('useractivity')) {
            $sql = "UPDATE " . DB_PREFIX . "useractivity SET useract_archived = %s WHERE useract_date <= %s";
            $this->db->query($this->db->prepare($sql, 'Y'));
        }
        
        $this->hotaru->message = $this->lang['submit_maintenance_archive_updated'];
        $this->hotaru->messageType = 'green';
        $this->hotaru->showMessage();
        return true;

    }
    
    
    /**
     * Show stats on Admin home page
     */
    public function admin_theme_main_stats($vars)
    {
        echo "<li>&nbsp;</li>";
    
        foreach ($vars as $stat_type) {
            $posts = $this->hotaru->post->stats($stat_type);
            if (!$posts) { $posts = 0; }
            $lang_name = 'submit_admin_stats_' . $stat_type;
            echo "<li>" . $this->lang[$lang_name] . ": " . $posts . "</li>";
        }
    }
    
    
    /**
     * User Settings - before saving
     */
    public function user_settings_pre_save()
    {
        // Open posts in a new tab?
        if ($this->cage->post->getAlpha('new_tab') == 'yes') { 
            $this->hotaru->vars['settings']['new_tab'] = "checked"; 
        } else { 
            $this->hotaru->vars['settings']['new_tab'] = "";
        }
        
        // List links open source url or post page?
        if ($this->cage->post->getAlpha('link_action') == 'source') { 
            $this->hotaru->vars['settings']['link_action'] = "checked"; 
        } else { 
            $this->hotaru->vars['settings']['link_action'] = "";
        }
    }
    
    
    /**
     * User Settings - fill the form
     */
    public function user_settings_fill_form()
    {
        if ($this->hotaru->vars['settings']['new_tab']) { 
            $this->hotaru->vars['new_tab_yes'] = "checked"; 
            $this->hotaru->vars['new_tab_no'] = ""; 
        } else { 
            $this->hotaru->vars['new_tab_yes'] = ""; 
            $this->hotaru->vars['new_tab_no'] = "checked"; 
        }
        
        if ($this->hotaru->vars['settings']['link_action']) { 
            $this->hotaru->vars['link_action_source'] = "checked"; 
            $this->hotaru->vars['link_action_post'] = ""; 
        } else { 
            $this->hotaru->vars['link_action_source'] = ""; 
            $this->hotaru->vars['link_action_post'] = "checked"; 
        }
    }
    
    
    /**
     * User Settings - html for form
     */
    public function user_settings_extra_settings()
    {
        $this->includeLanguage();

        echo "<tr>\n";
            // OPEN POSTS IN A NEW TAB?
        echo "<td>" . $this->lang['users_settings_open_new_tab'] . "</td>\n";
        echo "<td><input type='radio' name='new_tab' value='yes' " . $this->hotaru->vars['new_tab_yes'] . "> " . $this->lang['users_settings_yes'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='new_tab' value='no' " . $this->hotaru->vars['new_tab_no'] . "> " . $this->lang['users_settings_no'] . "</td>\n";
        echo "</tr>\n";
        
        echo "<tr>\n";
            // OPEN POSTS IN A NEW TAB?
        echo "<td>" . $this->lang['users_settings_link_action'] . "</td>\n";
        echo "<td><input type='radio' name='link_action' value='source' " . $this->hotaru->vars['link_action_source'] . "> " . $this->lang['users_settings_source'] . " &nbsp;&nbsp;\n";
        echo "<input type='radio' name='link_action' value='post' " . $this->hotaru->vars['link_action_post'] . "> " . $this->lang['users_settings_post'] . "</td>\n";
        echo "</tr>\n";
    }
}

?>