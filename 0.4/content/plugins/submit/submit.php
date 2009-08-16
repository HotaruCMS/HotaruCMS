<?php
/**
 * name: Submit
 * description: Submit and manage stories.
 * version: 0.2
 * folder: submit
 * prefix: sub
 * hooks: hotaru_header, header_include, install_plugin, upgrade_plugin, navigation, theme_index_replace, theme_index_main, admin_plugin_settings, admin_sidebar_plugin_settings
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


/* ******************************************************************** 
 *  Function: sub_upgrade_plugin
 *  Parameters: None
 *  Purpose: If it doesn't already exist, add a post_domain field to posts table.
 *  Notes: ---
 ********************************************************************** */
 
function sub_upgrade_plugin() {
    global $db, $plugin, $post;
    
    // Create a new table column called "post_tags" if it doesn't already exist
    $exists = $db->column_exists('posts', 'post_domain');
    if(!$exists) {
        $db->query("ALTER TABLE " . table_posts . " ADD post_domain varchar(255) NULL AFTER post_orig_url");
        $db->query("ALTER TABLE " . table_posts . " ADD FULLTEXT (post_domain)"); // Make it fulltext searchable
    } 
}
    
    
/* ******************************************************************** 
 *  Function: sub_install_plugin
 *  Parameters: None
 *  Purpose: If they don't already exist, posts and postmeta tables are created
 *  Notes: Happens when the plugin is installed. The tables are never deleted.
 ********************************************************************** */
 
function sub_install_plugin() {
    global $db, $plugin, $post;
    
    // Create a new empty table called "posts"
    $exists = $db->table_exists('posts');
    if(!$exists) {
        //echo "table doesn't exist. Stopping before creation."; exit;
        $sql = "CREATE TABLE `" . db_prefix . "posts` (
          `post_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `post_author` int(20) NOT NULL DEFAULT 0,
          `post_category` int(20) NOT NULL DEFAULT 1,
          `post_status` varchar(32) NOT NULL DEFAULT 'processing',
          `post_date` timestamp NULL,
          `post_title` varchar(255) NULL, 
          `post_orig_url` varchar(255) NULL, 
          `post_domain` varchar(255) NULL, 
          `post_url` varchar(255) NULL, 
          `post_content` text NULL,
          `post_tags` text NULL,
          `post_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
           `post_updateby` int(20) NOT NULL DEFAULT 0, 
          FULLTEXT (`post_title`, `post_domain`, `post_url`, `post_content`, `post_tags`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Story Posts';";
        $db->query($sql); 
    }
    
    // Create a new empty table called "postmeta"
    $exists = $db->table_exists('postmeta');
    if(!$exists) {
        //echo "table doesn't exist. Stopping before creation."; exit;
        $sql = "CREATE TABLE `" . db_prefix . "postmeta` (
          `postmeta_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `postmeta_postid` int(20) NOT NULL DEFAULT 0,
          `postmeta_key` varchar(255) NULL,
          `postmeta_value` text NULL,
          `postmeta_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
           `postmeta_updateby` int(20) NOT NULL DEFAULT 0, 
          INDEX  (`postmeta_postid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Meta';";
        $db->query($sql); 
    }
    
    // Default settings (Note: we can't use $post because it hasn't been filled yet.)
    $plugin->plugin_settings_update('submit', 'submit_enabled', 'checked');    
    $plugin->plugin_settings_update('submit', 'submit_author', 'checked');    
    $plugin->plugin_settings_update('submit', 'submit_date', 'checked');
    $plugin->plugin_settings_update('submit', 'submit_content', 'checked');    
    $plugin->plugin_settings_update('submit', 'submit_content_length', 50);    
    $plugin->plugin_settings_update('submit', 'submit_summary', 'checked');    
    $plugin->plugin_settings_update('submit', 'submit_summary_length', 200);    
    
    // Include language file. Also included in hotaru_header, but needed here so 
    // that the link in the Admin sidebar shows immediately after installation.
    $plugin->include_language_file('submit');    
    
}


/* ******************************************************************** 
 *  Function: sub_hotaru_header
 *  Parameters: None
 *  Purpose: Defines global "table_posts" and "table_postmeta" constants for referring to the db tables
 *  Notes: ---
 ********************************************************************** */
 
function sub_hotaru_header() {
    global $hotaru, $lang, $cage, $plugin, $post;
    
    if(!defined('table_posts')) { define("table_posts", db_prefix . 'posts'); }
    if(!defined('table_postmeta')) { define("table_postmeta", db_prefix . 'postmeta'); }

    // include language file
    $plugin->include_language_file('submit');
    
    require_once(plugins . 'submit/class.post.php');
    require_once(includes . 'Paginated/Paginated.php');
    require_once(includes . 'Paginated/DoubleBarLayout.php');
        
    $post = new Post();
    
    $plugin->check_actions('submit_hotaru_header_1');
    
    if(is_numeric($hotaru->get_page_name())) {
        // Page name is a number so it must be a post with non-friendly urls
        $post->read_post($hotaru->get_page_name());    // read current post
        $hotaru->page_type = 'post';
        $hotaru->title = $post->post_title;
        
    } elseif($post_id = $post->is_post_url($hotaru->get_page_name())) {
        // Page name belongs to a story
        $post->read_post($post_id);    // read current post
        $hotaru->page_type = 'post';
        $hotaru->title = $post->post_title;
        
    } else {
        $post->read_post();    // read current post settings only
        $hotaru->page_type = '';
    }
    
    $plugin->check_actions('submit_hotaru_header_2');
        
    $vars['post'] = $post; 
    return $vars; 
}


/* ******************************************************************** 
 *  Function: sub_navigation
 *  Parameters: None
 *  Purpose: Adds a "submit a story" link to the navigation bar
 *  Notes: 
 ********************************************************************** */

function sub_navigation() {    
    global $current_user, $lang, $hotaru;
    
    if($current_user->logged_in) {
        if($hotaru->title == 'submit') { $status = "id='navigation_active'"; } else { $status = ""; }
        echo "<li><a  " . $status . " href='" . url(array('page'=>'submit')) . "'>" . $lang['submit_submit_a_story'] . "</a></li>\n";
    }
}


/* ******************************************************************** 
 *  Function: sub_header_include
 *  Parameters: None
 *  Purpose: Includes css and javascript for fetching remote url content.
 *  Notes: Also adds javascript to preview page to prevent a user clicking away
 ********************************************************************** */
 
function sub_header_include() {
    global $plugin, $lang, $hotaru;
    $plugin->include_css_file('submit');
    $plugin->include_js_file('submit');
    
    /* This code (courtesy of Pligg.com and SocialWebCMS.com) pops up a 
       box asking the user of they are sure they want to leave the page
       without submitting their post. */
       
    if($hotaru->is_page('submit2')) {
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


/* ******************************************************************** 
 *  Function: sub_theme_index_replace
 *  Parameters: None
 *  Purpose: Checks results from submit form 2.
 *  Notes: ---
 ********************************************************************** */
 
function sub_theme_index_replace() {
    global $hotaru, $cage, $post, $plugin, $current_user;
    
    if($hotaru->is_page('submit2') && $post->use_submission) {
         
        if($current_user->logged_in) {
             
             if($cage->post->getAlpha('submit2') == 'true') {             
    
                 $post_orig_url = $cage->post->testUri('post_orig_url'); 
                 if(!sub_check_for_errors_2()) { 
                    sub_process_submission($post_orig_url);
                }
            }
        }
        
    } elseif($hotaru->is_page('submit3') && $post->use_submission) {
         
        if($current_user->logged_in) {

             if($cage->post->getAlpha('submit3') == 'edit') {             
    
                 $post_id = $cage->post->getInt('post_id'); 
                $post->read_post($post_id);
            }
                         
             if($cage->post->getAlpha('submit3') == 'confirm') {             
    
                 $post_id = $cage->post->getInt('post_id');
                 $post->read_post($post_id);
                $post->change_status('new');
                $post->send_trackback();
                header("Location: " . baseurl);    // Go home  
                die();
            }
        }
    
    } elseif($hotaru->is_page('rss')) {
    
        // Display RSS Feed - index.php?page=rss&status=new&limit=10
        $post->rss_feed();
        return true;
    }

    return false;
}


/* ******************************************************************** 
 *  Function: sub_theme_index_main
 *  Parameters: None
 *  Purpose: Determines which submit page to display
 *  Notes: ---
 ********************************************************************** */
 
function sub_theme_index_main() {
    global $hotaru, $cage, $post, $plugin, $current_user, $lang;
    global $post_orig_url, $post_orig_title, $filter, $filter_heading;
    
    if($hotaru->is_page('submit')) {
          
          if($current_user->logged_in) {
                       
              if(!$post->use_submission) {
                echo $lang['submit_disabled'];    
                return true;
            }
              
              if($cage->post->getAlpha('submit1') == 'true') {
                if(!sub_check_for_errors_1()) { 
                    // No errors found, proceed to step 2
                    $post_orig_url = $cage->post->testUri('post_orig_url'); 
                    $post_orig_title = sub_fetch_title($post_orig_url);
                    $hotaru->display_template('submit_step2' , 'submit');
                    return true;
                    
                } else {
                    // Errors found, go back to step 1
                    $post_orig_url = $cage->post->testUri('post_orig_url');
                    $hotaru->display_template('submit_step1', 'submit');
                    return true;
                }
            } else {
                // First time to step 1...
                $hotaru->display_template('submit_step1', 'submit');
                return true;
            }
        } else {
            return false;
        }
        
    } elseif($hotaru->is_page('submit2')) {
         
        if($current_user->logged_in) {
        
            if(!$post->use_submission) {
                echo $lang['submit_disabled'];    
                return true;
            }
             
             if($cage->post->getAlpha('submit2') == 'true') {
                 $post_orig_url = $cage->post->testUri('post_orig_url'); 
                 if($post->post_status == "processing") {     
                     // No errors, go to step 3...    
                     $post->read_post($post->post_id);
                     $hotaru->display_template('submit_step3', 'submit');
                     return true;
                 } else {
                     // Errors found, show step 2 again...
                     $hotaru->display_template('submit_step2', 'submit');
                     return true;
                 }
            }
        }
    
    } elseif($hotaru->is_page('submit3')) {
         
        if($current_user->logged_in) {
        
            if(!$post->use_submission) {
                echo $lang['submit_disabled'];    
                return true;
            }
             
             if($cage->post->getAlpha('submit3') == 'edit') {             
                 $hotaru->display_template('submit_step2', 'submit');
                 return true;
            }
        }

    } elseif($hotaru->is_page('main')) {
    
        // Plugin hook
        $result = $plugin->check_actions('submit_is_page_main');
        if($result && is_array($result)) { return true; }
    
        // Show the list of posts
        $hotaru->display_template('posts_list', 'submit');
        return true;
        
    } elseif($hotaru->is_page('latest')) {
    
        // Plugin hook
        $result = $plugin->check_actions('submit_is_page_latest');
        if($result && is_array($result)) { return true; }
    
        // Show the list of posts
        $hotaru->display_template('posts_list', 'submit');
        return true;
        
    } elseif($hotaru->is_page('all')) {
    
        // Plugin hook
        $result = $plugin->check_actions('submit_is_page_all');
        if($result && is_array($result)) { return true; }
    
        // Show the list of posts
        $hotaru->display_template('posts_list', 'submit');
        return true;
        
    } elseif($hotaru->page_type == 'post') {
        // We found out this is a post from the hotaru_header function above.
        
        $hotaru->display_template('post_page', 'submit');
        return true;
        
    } else {        
        return false;
    }
    
    return false;
}

/**
 * Prepare filter and breadcrumbs for Posts List
 *
 * @return array
 */ 
function sub_prepare_posts_list()
{
    global $hotaru, $plugin, $post, $cage, $filter, $lang, $page_title;

    $userbase = new UserBase();
    $post->template_name = "posts_list";
            
    if (!$filter) { $filter = array(); }
    
    if ($cage->get->testPage('page') == 'latest') 
    {
        $filter['post_status = %s'] = 'new'; 
        $rss = "<a href='" . url(array('page'=>'rss', 'status'=>'new')) . "'>";
        $rss .= " <img src='" . baseurl . "content/themes/" . theme . "images/rss_10.png'></a>";
        $page_title = $lang["submit_page_breadcrumbs_latest"] . $rss;
    } 
    else 
    {
        $filter['post_status = %s'] = 'top';
        $rss = "<a href='" . url(array('page'=>'rss')) . "'>";
        $rss .= " <img src='" . baseurl . "content/themes/" . theme . "images/rss_10.png'></a>";
        $page_title = $lang["submit_page_breadcrumbs_top"];
    }
    
    $plugin->check_actions('submit_posts_list_filter');
    
    $prepared_filter = $post->filter($filter, 0, true);
    $stories = $post->get_posts($prepared_filter);
    
    return $stories;
}

 
/* ******************************************************************** 
 *  Function: sub_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Puts a link to the settings page in the Admin sidebar under Plugin Settings
 *  Notes: ---
 ********************************************************************** */
 
function sub_admin_sidebar_plugin_settings() {
    echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'submit'), 'admin') . "'>Submit</a></li>";
}


 /* ******************************************************************** 
 *  Function: sub_admin_plugin_settings
 *  Parameters: None
 *  Purpose: Calls the function for displaying Admin settings
 *  Notes: ---
 ********************************************************************** */
 
function sub_admin_plugin_settings() {
    require_once(plugins . 'submit/submit_settings.php');
    sub_settings();
    return true;
}


 /* ******************************************************************** 
 *  Function: sub_fetch_title
 *  Parameters: None
 *  Purpose: Scrapes the title from the page being submitted
 *  Notes: ---
 ********************************************************************** */
 
function sub_fetch_title($url) {
    global $cage, $lang;
    
    require_once(includes . 'SWCMS/class.httprequest.php');
    
    if($url != 'http://' && $url != ''){
        $r = new HTTPRequest($url);
        $string = $r->DownloadToString();
    } else {
        $string = '';
    }
    
    if(preg_match('/charset=([a-zA-Z0-9-_]+)/i', $string , $matches)) {
        $encoding=trim($matches[1]);
        //you need iconv to encode to utf-8
        if(function_exists("iconv"))
        {
            if(strcasecmp($encoding, 'utf-8') != 0) {
                //convert the html code into utf-8 whatever encoding it is using
                $string=iconv($encoding, 'UTF-8//IGNORE', $string);
            }
        }
    }
        
    
    if(preg_match("'<title>([^<]*?)</title>'", $string, $matches)) {
        $title = trim($matches[1]);
    } else {
        $title = $lang["submit_form_not_found"];
    }
    
    return $title;
}


/* ******************************************************************** 
 *  Function: sub_check_for_errors_1
 *  Parameters: None
 *  Purpose: Checks submit_step1 for errors
 *  Notes: ---
 ********************************************************************** */

function sub_check_for_errors_1() {
    global $hotaru, $post, $cage, $lang;

    // ******** CHECK URL ********
    
    $post_orig_url_check = $cage->post->testUri('post_orig_url');
    if(!$post_orig_url_check) {
        // No url present...
        $hotaru->message = $lang['submit_form_url_not_present_error'];
        $hotaru->message_type = 'red';
        $error = 1;
    } elseif($post->url_exists($post_orig_url_check)) {
        // URL already exists...
        $hotaru->message = $lang['submit_form_url_already_exists_error'];
        $hotaru->message_type = 'red';
        $error = 1;
    } else {
        // URL is okay.
        $error = 0;
    }
    
    // Return true if error is found
    if($error == 1) { return true; } else { return false; }
}


/* ******************************************************************** 
 *  Function: sub_check_for_errors_2
 *  Parameters: None
 *  Purpose: Checks submit_step2 for errors
 *  Notes: ---
 ********************************************************************** */

function sub_check_for_errors_2() {
    global $hotaru, $post, $cage, $plugin, $lang;

    $post_id = $cage->post->getInt('post_id'); // 0 unless come back from step 3.

    // ******** CHECK TITLE ********
    
    $title_check = $cage->post->noTags('post_title');    
        
    if(!$title_check) {
        // No title present...
        $hotaru->messages[$lang['submit_form_title_not_present_error']] = "red";
        $error_title= 1;
    } elseif($post->title_exists($title_check)) {
        // title already exists...
        if($post_id != $post->title_exists($title_check)) {
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
    if($post->use_content) {
        $content_check = $cage->post->noTags('post_content');    
                
        if(!$content_check) {
            // No content present...
            $hotaru->messages[$lang['submit_form_content_not_present_error']] = "red";
            $error_content = 1;
        } elseif(strlen($content_check) < $post->post_content_length) {
            // content is too short
            $hotaru->messages[$lang['submit_form_content_too_short_error']] = "red";
            $error_content = 1;
        } else {
            // content is okay.
            $error_content = 0;
        }
    }
    
    
    // Check for errors from plugin fields, e.g. Tags
    $error_check_actions = 0;
    $error_array = $plugin->check_actions('submit_form_2_check_for_errors');
    if(is_array($error_array)) {
        foreach($error_array as $err) { if($err == 1) { $error_check_actions = 1; } }
    }
    
    // Return true if error is found
    if($error_title == 1 || $error_content == 1 || $error_check_actions == 1) { return true; } else { return false; }
}


/* ******************************************************************** 
 *  Function: sub_process_submission
 *  Parameters: None
 *  Purpose: Saves the submitted story to the database
 *  Notes: ---
 ********************************************************************** */
 
function sub_process_submission($post_orig_url) {
    global $hotaru, $cage, $plugin, $current_user, $post;
        
    if($cage->post->getAlpha('submit2') == 'true') {    
    
        $post->post_id = $cage->post->getInt('post_id');
        $post->post_orig_url = $cage->post->testUri('post_orig_url');
        $post->post_url = $cage->post->getFriendlyUrl('post_title');
        $post->post_title = $cage->post->noTags('post_title');
        $post->post_content = $cage->post->noTags('post_content');
        $post->post_status = "processing";
        $post->post_author = $current_user->id;
        
        $plugin->check_actions('submit_form_2_process_submission');
        
        if($post->post_id != 0) {
            $post->update_post();    // Updates an existing post (e.g. returning to step 2 from step 3 to modify it)
        } else {
            $post->add_post();    // Adds a new post
        }
    
    } 
}

    
?>