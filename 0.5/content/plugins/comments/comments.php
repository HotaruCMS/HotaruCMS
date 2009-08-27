<?php
/**
 * name: Comments
 * description: Enables logged-in users to comment on posts
 * version: 0.1
 * folder: comments
 * prefix: cmmts
 * requires: submit 0.2, users 0.2
 * hooks: header_include, install_plugin, hotaru_header, theme_index_replace, submit_show_post_extra_fields, submit_post_show_post, admin_plugin_settings, admin_sidebar_plugin_settings
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

/**
 * Default settings on install
 */
function cmmts_install_plugin()
{
    global $db, $plugin, $lang;
        
    // Create a new empty table called "comments"
    $exists = $db->table_exists('comments');
    if (!$exists) {
        //echo "table doesn't exist. Stopping before creation."; exit;
        $sql = "CREATE TABLE `" . DB_PREFIX . "comments` (
          `comment_id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `comment_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
          `comment_post_id` int(20) NOT NULL DEFAULT '0',
          `comment_user_id` int(20) NOT NULL DEFAULT '0',
          `comment_parent` int(20) DEFAULT '0',
          `comment_date` timestamp NOT NULL,
          `comment_content` text NOT NULL,
          `comment_votes` int(20) NOT NULL DEFAULT '0',
          `comment_subscribe` tinyint(1) NOT NULL DEFAULT '0',
          `comment_updateby` int(20) NOT NULL DEFAULT 0
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Comments';";
        $db->query($sql); 
    }
    
    // Create a new empty table called "commentvotes" if it doesn't already exist
    $exists = $db->table_exists('commentvotes');
    if (!$exists) {
        //echo "table doesn't exist. Stopping before creation."; exit;
        $sql = "CREATE TABLE `" . DB_PREFIX . "commentvotes` (
          `cvote_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
          `cvote_post_id` int(11) NOT NULL DEFAULT '0',
          `cvote_comment_id` int(11) NOT NULL DEFAULT '0',
          `cvote_user_id` int(11) NOT NULL DEFAULT '0',
          `cvote_user_ip` varchar(32) NOT NULL DEFAULT '0',
          `cvote_date` timestamp NOT NULL,
          `cvote_rating` enum('positive','negative','alert') NULL,
          `cvote_reason` tinyint(3) NOT NULL DEFAULT 0,
          `cvote_updateby` int(20) NOT NULL DEFAULT 0
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Comment Votes';";
        $db->query($sql); 
    }
    
    // Default settings 
    $comment_settings['comment_form'] = "checked";
    $comment_settings['comment_avatars'] = "";
    $comment_settings['comment_voting'] = "";
    $comment_settings['comment_allowable_tags'] = "<b><i><u><a><blockquote><strike>";
    $plugin->plugin_settings_update('comments', 'comment_settings', serialize($comment_settings));
    
    // Include language file. Also included in hotaru_header, but needed here so 
    // that the link in the Admin sidebar shows immediately after installation.
    $plugin->include_language('comments');
}


/**
 * Include css and JavaScript
 */
function cmmts_header_include()
{ 
    global $plugin; 
    
    $plugin->include_css('comments');
    $plugin->include_js('comments');
}


/**
 * Define table name, include language file and creat global Comments object
 */
function cmmts_hotaru_header()
{
    global $lang, $plugin, $comment;

    if (!defined('TABLE_COMMENTS')) { define("TABLE_COMMENTS", DB_PREFIX . 'comments'); }
    if (!defined('TABLE_COMMENTVOTES')) { define("TABLE_COMMENTVOTES", DB_PREFIX . 'commentvotes'); }
    
    $plugin->include_language('comments');
    
    require_once(PLUGINS . 'comments/class.comments.php');
    // Create a new global object called "comments".
    $comment = new Comment();
    
    // Get settings from database if they exist...
    $comment_settings = $comment->get_comment_settings();

    // Assign settings to class member
    $comment->comment_form = $comment_settings['comment_form'];
    $comment->comment_avatars = $comment_settings['comment_avatars'];
    $comment->comment_voting = $comment_settings['comment_voting'];
    $comment->comment_allowable_tags = $comment_settings['comment_allowable_tags'];
        
    $vars['comment'] = $comment; 
    return $vars; 
}


/**
 * Process a new comment
 *
 * @return bool
 */
function cmmts_theme_index_replace()
{
    global $hotaru, $cage, $post, $plugin, $current_user, $comment;
    
    if (($hotaru->is_page('comments')) && ($comment->comment_form == 'checked')) {
         
        if ($current_user->logged_in) {
                     
            if ($cage->post->getAlpha('comment_process') == 'newcomment') {
            
                //Include HTMLPurifier which we'll use on comment_content
                $cage->post->loadHTMLPurifier(INCLUDES . 'HTMLPurifier/HTMLPurifier.standalone.php');
    
                if ($cage->post->keyExists('comment_content')) {
                    $comment->comment_content = sanitize($cage->post->getPurifiedHTML('comment_content'), 2, $comment->allowabletags);
                }
                
                if ($cage->post->keyExists('comment_post_id')) {
                    $comment->comment_post_id = $cage->post->testInt('comment_post_id');
                }
                
                if ($cage->post->keyExists('comment_user_id')) {
                    $comment->comment_author = $cage->post->testInt('comment_user_id');
                }
                
                if ($cage->post->keyExists('comment_parent')) {
                    $comment->comment_parent = $cage->post->testInt('comment_parent');
                }
                
                if ($cage->post->keyExists('comment_subscribe')) {
                    $comment->comment_subscribe = 1;
                } else {
                    $comment->comment_subscribe = 0;
                }
                
                $comment->add_comment();
                
                header("Location: " . url(array('page'=>$comment->comment_post_id)));    // Go to the post
                die();
                
            }

        }
        
    }

    return false;
}


/**
 * Display Admin sidebar link
 */
function cmmts_admin_sidebar_plugin_settings()
{
    global $lang;
    
    echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'comments'), 'admin') . "'>" . $lang['comments_admin_sidebar'] . "</a></li>";
}


/**
 * Display Admin settings page
 *
 * @return true
 */
function cmmts_admin_plugin_settings()
{
    require_once(PLUGINS . 'comments/comments_settings.php');
    cmmts_settings();
    return true;
}


/**
 * Link to comments
 */
function cmmts_submit_show_post_extra_fields()
{
    global $post, $lang;
    
    echo '<li><a class="comments_comments_link" href="' . url(array('page'=>$post->post_id)) . '">' . $lang['comments_comments_singular_link'] . '</a></li>' . "\n";
}


/**
 * Prepare and display comments wrapper and form
 */
function cmmts_submit_post_show_post()
{
    global $hotaru, $comment, $post;
    
    $parents = $comment->read_all_parents($post->post_id);
    if ($parents) { 
        
        echo "<!--  START COMMENTS_WRAPPER -->\n";
        echo "<div id='comments_wrapper'>\n";

        foreach ($parents as $parent) {
            
            display_comment($parent);
            comment_tree($parent->comment_id, 0);
        }
        
        echo "</div><!-- close comments_wrapper -->\n";
        echo "<!--  END COMMENTS -->\n";
        
    }
    
    if ($comment->comment_form == 'checked') {
        // force non-reply form to have parent "0" and depth "0"
        $comment->comment_id = 0;
        $comment->comment_depth = 0;
        $hotaru->display_template('comment_form', 'comments', false);
    }
}


/**
 * Recurse through comment tree
 *
 * @param int $item_id - id of current comment
 * @param int $depth - for comment nesting
 * @return bool
 */
function comment_tree($item_id, $depth)
{
    global $comment, $post;
        
    while ($children = $comment->read_all_children($post->post_id, $item_id)) {
        foreach ($children as $child) {
            $depth++;
            $comment->comment_depth = $depth;
            display_comment($child);
            if (comment_tree($child->comment_id, $depth)) {
                return true;
            }
        }
        
        return false;
    }
}


/**
 * Display a comment
 *
 * @param array $item - current comment
 */
function display_comment($item)
{
    global $hotaru, $comment;
    
    $comment->read_comment($item);
    
    $hotaru->display_template('show_comments', 'comments', false);
    $hotaru->display_template('comment_form', 'comments', false);
}


?>