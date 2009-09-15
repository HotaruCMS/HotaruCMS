<?php
/**
 * name: Submit
 * description: Submit and manage stories.
 * version: 0.4
 * folder: submit
 * class: Submit
 * prefix: sub
 * hooks: hotaru_header, header_include, header_include_raw, upgrade_plugin, install_plugin, upgrade_plugin, navigation, theme_index_replace, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings
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
        global $db, $post;
        
        // Create a new empty table called "posts"
        $exists = $db->table_exists('posts');
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
            $db->query($sql); 
        }
        
        // Create a new empty table called "postmeta"
        $exists = $db->table_exists('postmeta');
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
            $db->query($sql); 
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
        global $hotaru, $lang, $cage, $post;

        if (!defined('TABLE_POSTS')) { define("TABLE_POSTS", DB_PREFIX . 'posts'); }
        if (!defined('TABLE_POSTMETA')) { define("TABLE_POSTMETA", DB_PREFIX . 'postmeta'); }
        
        //Include HTMLPurifier which we'll use on post_content
        $cage->post->loadHTMLPurifier(EXTENSIONS . 'HTMLPurifier/HTMLPurifier.standalone.php');
    
        // include language file
        $this->includeLanguage();
                
        require_once(PLUGINS . 'submit/libs/Post.php');
        require_once(EXTENSIONS . 'Paginated/Paginated.php');
        require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
            
        require_once(PLUGINS . 'submit/libs/Post.php');
        $post = new Post();
        
        $this->pluginHook('submit_hotaru_header_1');
        
        if (is_numeric($hotaru->getPageName())) {
            // Page name is a number so it must be a post with non-friendly urls
            $post->readPost($hotaru->getPageName());    // read current post
            $hotaru->setPageType('post');
            $hotaru->setTitle($post->getTitle());
            
        } elseif ($post_id = $post->isPostUrl($hotaru->getPageName())) {
            // Page name belongs to a story
            $post->readPost($post_id);    // read current post
            $hotaru->setPageType('post');
            $hotaru->setTitle($post->getTitle());
            
        } else {
            $post->readPost();    // read current post settings only
            $hotaru->setPageType('');
        }
                
        $this->pluginHook('submit_hotaru_header_2');
               
        $vars['post'] = $post; 
        return $vars; 
    }
    
    
    /**
     * Add a "submit a story" link to the navigation bar
     */
    public function navigation()
    {    
        global $current_user, $lang, $hotaru;
        
        if ($current_user->getLoggedIn()) {
            if ($hotaru->getTitle() == 'submit') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a  " . $status . " href='" . url(array('page'=>'submit')) . "'>" . $lang['submit_submit_a_story'] . "</a></li>\n";
        }
    }
    
    
    /**
     * Output raw javascript directly to the header (instead of caching a .js file)
     */
    public function header_include_raw()
    {
        global $lang, $hotaru;
        
        /* This code (courtesy of Pligg.com and SocialWebCMS.com) pops up a 
           box asking the user of they are sure they want to leave the page
           without submitting their post. */
           
        if ($hotaru->isPage('submit2')) {
            echo '
                <script type="text/javascript">
        
                var safeExit = false;
            
                window.onbeforeunload = function (event) 
                {
                    if (safeExit)
                        return;
        
                    if (!event && window.event) 
                              event = window.event;
                              
                       event.returnValue = "' . $lang['submit_form_submit_accidental_click'] . '";
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
        global $hotaru, $cage, $post, $current_user;
        
        if ($hotaru->isPage('submit2') && $post->getUseSubmission()) {
             
            if ($current_user->getLoggedIn()) {
                         
                if ($cage->post->getAlpha('submit2') == 'true') {             
        
                    $post_orig_url = $cage->post->testUri('post_orig_url'); 
                    if (!$this->check_for_errors_2()) { 
                        $this->process_submission($post_orig_url);
                    }
                }
            }
            
        } elseif ($hotaru->isPage('submit3') && $post->getUseSubmission()) {
             
            if ($current_user->getLoggedIn()) {
    
                 if ($cage->post->getAlpha('submit3') == 'edit') {             
        
                    $post_id = $cage->post->getInt('post_id'); 
                    $post->readPost($post_id);
                }
                             
                 if ($cage->post->getAlpha('submit3') == 'confirm') {             
        
                    $post_id = $cage->post->getInt('post_id');
                    $post->readPost($post_id);
                    $post->changeStatus('new');
                    $post->sendTrackback();
                    header("Location: " . BASEURL);    // Go home  
                    die();
                }
            }
            
        } elseif ($hotaru->isPage('edit_post')) {
            if ($current_user->getLoggedIn()) {
                       
                if ($cage->post->getAlpha('edit_post') == 'true') {
                    $post_orig_url = $cage->post->testUri('post_orig_url'); 
                    if (!$this->check_for_errors_2()) { 
                        $this->process_submission($post_orig_url);
                        header("Location: " . url(array('page'=>$post->getId())));    // Go to the post
                        die();
                    }
                }
            }
        
        } elseif ($hotaru->isPage('rss')) {
        
            // Display RSS Feed - index.php?page=rss&status=new&limit=10
            $post->rssFeed();
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
        global $hotaru, $cage, $post, $current_user, $lang, $user;
        global $post_orig_url, $post_orig_title, $filter, $filter_heading;
        
        if ($hotaru->isPage('submit')) {
              
              if ($current_user->getLoggedIn()) {
                           
                  if (!$post->getUseSubmission()) {
                    echo $lang['submit_disabled'];    
                    return true;
                }
                  
                  if ($cage->post->getAlpha('submit1') == 'true') {
                    if (!$this->check_for_errors_1()) { 
                        // No errors found, proceed to step 2
                        $post_orig_url = $cage->post->testUri('post_orig_url'); 
                        $post_orig_title = $post->fetch_title($post_orig_url);
                        $hotaru->displayTemplate('submit_step2' , 'submit');
                        return true;
                        
                    } else {
                        // Errors found, go back to step 1
                        $post_orig_url = $cage->post->testUri('post_orig_url');
                        $hotaru->displayTemplate('submit_step1', 'submit');
                        return true;
                    }
                } else {
                    // First time to step 1...
                    $hotaru->displayTemplate('submit_step1', 'submit');
                    return true;
                }
            } else {
                return false;
            }
            
        } elseif ($hotaru->isPage('submit2')) {
             
            if ($current_user->getLoggedIn()) {
            
                if (!$post->getUseSubmission()) {
                    echo $lang['submit_disabled'];    
                    return true;
                }
                 
                 if ($cage->post->getAlpha('submit2') == 'true') {
                 
                    $post_orig_url = $cage->post->testUri('post_orig_url'); 
                    if ($post->getStatus() == 'processing') {     
                        // No errors, go to step 3...    
                        $post->readPost($post->getId());
                        $hotaru->displayTemplate('submit_step3', 'submit');
                        return true;
                    } else {
                        // Errors found, show step 2 again...
                        $hotaru->displayTemplate('submit_step2', 'submit');
                        return true;
                    }
                }
            }
        
        } elseif ($hotaru->isPage('submit3')) {
             
            if ($current_user->getLoggedIn()) {
            
                if (!$post->getUseSubmission()) {
                    echo $lang['submit_disabled'];    
                    return true;
                }
                 
                 if ($cage->post->getAlpha('submit3') == 'edit') {             
                     $hotaru->displayTemplate('submit_step2', 'submit');
                     return true;
                }
            }
            
        } elseif ($hotaru->isPage('edit_post')) {
            if ($current_user->getLoggedIn()) {
                if ($cage->get->keyExists('sourceurl') || $cage->get->keyExists('post_id')) {
                    $hotaru->displayTemplate('submit_edit_post', 'submit');
                    return true;
                }
            }
    
        } elseif ($hotaru->isPage('main')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_main');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $hotaru->displayTemplate('list', 'submit');
            return true;
            
        } elseif ($hotaru->isPage('latest')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_latest');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $hotaru->displayTemplate('list', 'submit');
            return true;
            
        } elseif ($hotaru->isPage('all')) {
        
            // Plugin hook
            $result = $this->pluginHook('submit_is_page_all');
            if ($result && is_array($result)) { return true; }
        
            // Show the list of posts
            $hotaru->displayTemplate('list', 'submit');
            return true;
            
        } elseif ($hotaru->getPageType() == 'post') {
            // We found out this is a post from the hotaru_header function above.
            $hotaru->displayTemplate('post', 'submit');
            return true;
            
        } else {        
            return false;
        }
        
        return false;
    }
    
     
    /**
     * Add a link to the Admin sidebar under Plugin Settings
     */
    public function admin_sidebar_plugin_settings()
    {
        echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'submit'), 'admin') . "'>Submit</a></li>";
    }
    
    
    /**
     * Calls the function for displaying Admin settings
     */
    public function admin_plugin_settings()
    {
        require_once(PLUGINS . 'submit/submit_settings.php');
        $submitSettings = new SubmitSettings();
        $submitSettings->settings($this->folder);
        return true;
    }
    
    
    /**
     * Checks submit_step1 for errors
     */
    public function check_for_errors_1()
    {
        global $hotaru, $post, $cage, $lang;
    
        // ******** CHECK URL ********
        
        $post_orig_url_check = $cage->post->testUri('post_orig_url');
        if (!$post_orig_url_check) {
            // No url present...
            $hotaru->message = $lang['submit_form_url_not_present_error'];
            $hotaru->message_type = 'red';
            $error = 1;
        } elseif ($post->urlExists($post_orig_url_check)) {
            // URL already exists...
            $hotaru->message = $lang['submit_form_url_already_exists_error'];
            $hotaru->message_type = 'red';
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
        global $hotaru, $post, $cage, $lang;
        
        $post_id = $cage->post->getInt('post_id'); // 0 unless come back from step 3.
        
        if ($cage->post->keyExists('edit_post')) { $edit = true; } else {$edit = false; }
    
        // ******** CHECK URL ********
        $error_url = 0;
        // Only for Admin user
        if ($edit) {
            $orig_url_check = $cage->post->testUri('post_orig_url');    
            
            if (!$orig_url_check) {
                // No url present...
                $hotaru->messages[$lang['submit_form_url_not_complete_error']] = "red";
                $error_url = 1;
            }
        }
        
        // ******** CHECK TITLE ********
        
        $title_check = $cage->post->noTags('post_title');    
            
        if (!$title_check) {
            // No title present...
            $hotaru->messages[$lang['submit_form_title_not_present_error']] = "red";
            $error_title= 1;
        } elseif (!$edit && $post->titleExists($title_check)) {
            // title already exists...
            if ($post_id != $post->titleExists($title_check)) {
                $hotaru->messages[$lang['submit_form_title_already_exists_error']] = "red";
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
        if ($post->getUseContent()) {
            $content_check = sanitize($cage->post->getPurifiedHTML('post_content'), 2, $post->allowableTags);
                    
            if (!$content_check) {
                // No content present...
                $hotaru->messages[$lang['submit_form_content_not_present_error']] = "red";
                $error_content = 1;
            } elseif (strlen($content_check) < $post->post_content_length) {
                // content is too short
                $hotaru->messages[$lang['submit_form_content_too_short_error']] = "red";
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
        global $hotaru, $cage, $current_user, $post;
            
        if ($cage->post->getAlpha('submit2') == 'true') {    
        
            $post->setId($cage->post->getInt('post_id'));
            $post->setOrigUrl($cage->post->testUri('post_orig_url'));
            $post->setTitle($cage->post->noTags('post_title'));
            $post->setUrl($cage->post->getFriendlyUrl('post_title'));
            $post->setContent(sanitize($cage->post->getPurifiedHTML('post_content'), 2, $post->allowableTags));
            $post->setStatus('processing');
            $post->setAuthor($current_user->getId());
            
            $this->pluginHook('submit_form_2_process_submission');
            
            if ($post->getId() != 0) {
                $post->updatePost();    // Updates an existing post (e.g. returning to step 2 from step 3 to modify it)
            } else {
                $post->addPost();    // Adds a new post
            }
            
        } elseif ($cage->post->keyExists('edit_post')) { 
            
            // Editing an existing post.
            $post->setId($cage->post->getInt('post_id'));
            $post->readPost($post->getId());
            $post->setOrigUrl($cage->post->testUri('post_orig_url'));
            $post->setTitle($cage->post->noTags('post_title'));
            $post->setUrl($cage->post->getFriendlyUrl('post_title'));
            $post->setContent(sanitize($cage->post->getPurifiedHTML('post_content'), 2, $post->allowableTags));
            $post->setStatus($cage->post->testAlnumLines('post_status'));
            $this->pluginHook('submit_form_2_process_submission');
            $post->updatePost();
        }
    }

}

?>
