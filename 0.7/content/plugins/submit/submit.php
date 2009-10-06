<?php
/**
 * name: Submit
 * description: Submit and manage stories.
 * version: 0.7
 * folder: submit
 * class: Submit
 * hooks: hotaru_header, header_include, header_include_raw, upgrade_plugin, install_plugin, upgrade_plugin, navigation, theme_index_replace, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings, userbase_default_permissions
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
     * Upgrade plugin
     */
    public function upgrade_plugin()
    {
        /* Having this here makes hotaru ignore the install function. For this version, 
           we don't need to make any database changes and don't want to reset our settings, 
           so we'll just do nothing and let the "upgrade" simply be the updated files. */
    }
    
    
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
              `postmeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
              `postmeta_postid` int(20) NOT NULL DEFAULT 0,
              `postmeta_key` varchar(255) NULL,
              `postmeta_value` text NULL,
               `postmeta_updateby` int(20) NOT NULL DEFAULT 0, 
              INDEX  (`postmeta_postid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Meta';";
            $this->db->query($sql); 
        }
        
        // Default settings 
        $submit_settings['post_enabled'] = "checked";
        $submit_settings['post_author'] = "checked";
        $submit_settings['post_date'] = "checked";
        $submit_settings['post_content'] = "checked";
        $submit_settings['post_content_length'] = 50;
        $submit_settings['post_summary'] = "checked";
        $submit_settings['post_summary_length'] = 200;
        $submit_settings['post_posts_per_page'] = 10;
        $submit_settings['post_allowable_tags'] = "<b><i><u><a><blockquote><strike>";
        
        $this->updateSetting('submit_settings', serialize($submit_settings));
        
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
        
        if (is_numeric($this->hotaru->getPageName())) {
            // Page name is a number so it must be a post with non-friendly urls
            $this->hotaru->post->readPost($this->hotaru->getPageName());    // read current post
            $this->hotaru->pageType = 'post';
            $this->hotaru->title = $this->hotaru->post->title;
            
        } elseif ($post_id = $this->hotaru->post->isPostUrl($this->hotaru->getPageName())) {
            // Page name belongs to a story
            $this->hotaru->post->readPost($post_id);    // read current post
            $this->hotaru->pageType = 'post';
            $this->hotaru->title = $this->hotaru->post->title;
            
        } else {
            $this->hotaru->post->readPost();    // read current post settings only
            $this->hotaru->pageType = '';
        }

        $this->pluginHook('submit_hotaru_header_2');
    }
    
    
    /**
     * Add a "submit a story" link to the navigation bar
     */
    public function navigation()
    {    
        if ($this->current_user->loggedIn) {
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
                             
                 if ($this->cage->post->getAlpha('submit3') == 'confirm') {             
        
                    $post_id = $this->cage->post->getInt('post_id');
                    $this->hotaru->post->readPost($post_id);
                    $this->hotaru->post->changeStatus('new');
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
                        header("Location: " . $this->hotaru->url(array('page'=>$this->hotaru->post->id)));    // Go to the post
                        die();
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
                    echo $this->lang['submit_disabled'];    
                    return true;
                }
                  
                  if ($this->cage->post->getAlpha('submit1') == 'true') {
                    if (!$this->check_for_errors_1()) { 
                        // No errors found, proceed to step 2
                        $this->hotaru->vars['post_orig_url'] = $this->cage->post->testUri('post_orig_url'); 
                        $this->hotaru->vars['post_orig_title'] = $this->hotaru->post->fetch_title($this->hotaru->vars['post_orig_url']);
                        $this->hotaru->displayTemplate('submit_step2', $this->hotaru, 'submit');
                        return true;
                        
                    } else {
                        // Errors found, go back to step 1 - use getMixedString because testUri returns false
                        $this->hotaru->vars['post_orig_url'] = $this->cage->post->getMixedString2('post_orig_url');
                        $this->hotaru->displayTemplate('submit_step1', $this->hotaru, 'submit');
                        return true;
                    }
                } else {
                    // First time to step 1...
                    $this->hotaru->displayTemplate('submit_step1', $this->hotaru, 'submit');
                    return true;
                }
            } else {
                return false;
            }
            
        } elseif ($this->hotaru->isPage('submit2')) {
             
            if ($this->current_user->loggedIn) {
            
                if (!$this->hotaru->post->useSubmission) {
                    echo $this->lang['submit_disabled'];    
                    return true;
                }
                 
                 if ($this->cage->post->getAlpha('submit2') == 'true') {
                 
                    $this->hotaru->vars['post_orig_url'] = $this->cage->post->testUri('post_orig_url'); 
                    if ($this->hotaru->post->status == 'processing') {     
                        // No errors, go to step 3...    
                        $this->hotaru->post->readPost($this->hotaru->post->id);
                        $this->hotaru->displayTemplate('submit_step3', $this->hotaru, 'submit');
                        return true;
                    } else {
                        // Errors found, show step 2 again...
                        $this->hotaru->displayTemplate('submit_step2', $this->hotaru, 'submit');
                        return true;
                    }
                }
            }
        
        } elseif ($this->hotaru->isPage('submit3')) {
             
            if ($this->current_user->loggedIn) {
            
                if (!$this->hotaru->post->useSubmission) {
                    echo $this->lang['submit_disabled'];    
                    return true;
                }
                 
                 if ($this->cage->post->getAlpha('submit3') == 'edit') {             
                     $this->hotaru->displayTemplate('submit_step2', $this->hotaru, 'submit');
                     return true;
                }
            }
            
        } elseif ($this->hotaru->isPage('edit_post')) {
            if ($this->current_user->loggedIn) {
                if ($this->cage->get->keyExists('sourceurl') || $this->cage->get->keyExists('post_id')) {
                    $this->hotaru->displayTemplate('submit_edit_post', $this->hotaru, 'submit');
                    return true;
                }
            }
    
        } elseif ($this->hotaru->isPage('main')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_main');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $this->hotaru->displayTemplate('list', $this->hotaru, 'submit');
            return true;
            
        } elseif ($this->hotaru->isPage('latest')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_latest');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $this->hotaru->displayTemplate('list', $this->hotaru, 'submit');
            return true;
            
        } elseif ($this->hotaru->isPage('all')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_all');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $this->hotaru->displayTemplate('list', $this->hotaru, 'submit');
            return true;
            
        } elseif ($this->hotaru->pageType == 'post') {
            // We found out this is a post from the hotaru_header function above.
            $this->hotaru->displayTemplate('post', $this->hotaru, 'submit');
            return true;
            
        } else {        
            return false;
        }
        
        return false;
    }

    
    /**
     * Default permissions 
     *
     * @param array $params - conatins "role"
     */
    public function userbase_default_permissions($params)
    {
        $perms = $this->hotaru->vars['perms'];

        $role = $params['role'];
        
        // Permission Options
        $perms['options']['can_submit'] = array('yes', 'no');
        
        // Permissions for $role
        switch ($role) {
            case 'admin':
                $perms['can_submit'] = 'yes';
                break;
            case 'member':
                $perms['can_submit'] = 'yes';
                break;
            default:
                $perms['can_submit'] = 'no';
        }
        
        $this->hotaru->vars['perms'] = $perms;
    }
    
    
    /**
     * Checks submit_step1 for errors
     */
    public function check_for_errors_1()
    {
        // ******** CHECK URL ********
        
        $post_orig_url_check = $this->cage->post->testUri('post_orig_url');
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
            // URL already exists...
            $this->hotaru->message = $this->lang['submit_form_no_permission'];
            $this->hotaru->messageType = 'red';
            $error = 1;
        } elseif ($this->checkBlocked($post_orig_url_check)) {
            // URL already exists...
            $this->hotaru->message = $this->lang['submit_form_url_blocked'];
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
    
        // ******** CHECK URL ********
        $error_url = 0;
        // Only for Admin user
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
                    
            if (!$content_check) {
                // No content present...
                $this->hotaru->messages[$this->lang['submit_form_content_not_present_error']] = "red";
                $error_content = 1;
            } elseif (strlen($content_check) < $this->hotaru->post->post_content_length) {
                // content is too short
                $this->hotaru->messages[$this->lang['submit_form_content_too_short_error']] = "red";
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
            $this->hotaru->post->origUrl = $this->cage->post->testUri('post_orig_url');
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
            }
            
            $this->pluginHook('submit_form_2_process_submission');
            $this->hotaru->post->updatePost();
        }
    }

    /**
     * Check if user is on the blocked list
     *
     * @param string $username
     * @param string $email
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
                        
        return false;   // not blocked
    }
    
}

?>