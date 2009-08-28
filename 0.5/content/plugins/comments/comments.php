<?php
/**
 * name: Comments
 * description: Enables logged-in users to comment on posts
 * version: 0.1
 * folder: comments
 * prefix: cmmts
 * requires: submit 0.3, users 0.3
 * hooks: header_include, install_plugin, hotaru_header, theme_index_replace, submit_show_post_extra_fields, submit_post_show_post, admin_plugin_settings, admin_sidebar_plugin_settings, submit_form_2_assign, submit_form_2_fields, submit_form_2_process_submission
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
    $comment_settings['comment_levels'] = 5;
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
    $comment->comment_levels = $comment_settings['comment_levels'];
        
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
                     
            if (($cage->post->getAlpha('comment_process') == 'newcomment') || 
                ($cage->post->getAlpha('comment_process') == 'editcomment'))
            {
            
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
                    $comment->unsubscribe($comment->comment_post_id);
                }
                
                if ($cage->post->getAlpha('comment_process') == 'newcomment')
                {
                    // A user can unsubscribe by submitting an empty comment, so...
                    if(!empty($comment->comment_content)) {
                        $comment->add_comment();
                        $comment->email_comment_subscribers($comment->comment_post_id);
                    } else {
                        //comment empty so just check subscribe box:
                        $comment->update_subscribe($comment->comment_post_id);
                    }
                }
                elseif($cage->post->getAlpha('comment_process') == 'editcomment')
                {
                        $comment->edit_comment();
                }
                
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
    global $post, $lang, $comment;
    
    echo '<li><a class="comments_comments_link" href="' . url(array('page'=>$post->post_id)) . '">' . $comment->count_comments() . '</a></li>' . "\n";
}


/**
 * Prepare and display comments wrapper and form
 */
function cmmts_submit_post_show_post()
{
    global $db, $hotaru, $comment, $post, $current_user;
    
    // set default
    $current_user->userbase_vars['post_subscribed'] = false; 
    
    // Check if the current_user is the post author
    if ($post->post_author == $current_user->id) {
        // Check if the user subscribed to comments as a submitter
        if ($post->post_subscribe == 1) { 
            $current_user->userbase_vars['post_subscribed'] = true; 
        } 
    } 
    
    // Check if the user subscribed to comments as a commenter
    $sql = "SELECT COUNT(comment_subscribe) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_user_id = %d AND comment_subscribe = %d";
    $subscribe_result = $db->get_var($db->prepare($sql, $post->post_id, $current_user->id, 1));
    
    if ($subscribe_result > 0) { 
        $current_user->userbase_vars['post_subscribed'] = true; 
    } 
    
    $parents = $comment->read_all_parents($post->post_id);
    if ($parents) { 
        
        echo "<!--  START COMMENTS_WRAPPER -->\n";
        echo "<div id='comments_wrapper'>\n";
        echo "<h2>" . $comment->count_comments() . "</h2>\n";
        
        foreach ($parents as $parent) {
            
            display_comment($parent);
            comment_tree($parent->comment_id, 0);
        }
        
        echo "</div><!-- close comments_wrapper -->\n";
        echo "<!--  END COMMENTS -->\n";
        
    }
    
    if ($comment->comment_form == 'checked' && !$hotaru->is_page('submit2')) {
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
            if ($depth == $comment->comment_levels) { 
                // Prevent depth exceeding nesting levels
                // levels start at 0 so we're using -1.
                $depth = $comment->comment_levels - 1;
            }
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
    global $hotaru, $comment, $current_user;
       
    if (!$hotaru->is_page('submit2')) {
        $comment->read_comment($item);
    
        $hotaru->display_template('show_comments', 'comments', false);
        $hotaru->display_template('comment_form', 'comments', false);
    }
}


/**
 * Check and update post_submit in Submit step 2 and Post Edit pages
 */
function cmmts_submit_form_2_assign()
{
    global $hotaru, $cage, $post, $subscribe_check;
    
    if ($cage->post->getAlpha('submit2') == 'true') 
    {
        if ($cage->post->keyExists('post_subscribe')) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
    } 
    elseif ($cage->post->getAlpha('submit3') == 'edit')
    {
        if ($post->post_subscribe == 1) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
    }
    elseif ($hotaru->is_page('edit_post')) 
    {
        if ($cage->post->getAlpha('edit_post') == 'true') {
            if ($cage->post->keyExists('post_subscribe')) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
        } else {
            if ($post->post_subscribe == 1) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
        }
    }
    else 
    {
        $subscribe_check = "";
    }
}


/**
 * Show post_subscribe option in Submit step 2 and Post Edit
 */
function cmmts_submit_form_2_fields()
{
    global $lang, $subscribe_check;
    
    echo "<tr><td colspan='3'>\n";
    echo "<input id='post_subscribe' name='post_subscribe' type='checkbox' " . $subscribe_check . "> " . $lang['submit_form_subscribe']; 
    echo "</tr>";
}


/**
 * Save post_subscribe to the database
 */
function cmmts_submit_form_2_process_submission() 
{
    global $post, $cage;
    
    if ($cage->post->keyExists('post_subscribe')) { $post->post_subscribe = 1; } else { $post->post_subscribe = 0; } 
}

?>