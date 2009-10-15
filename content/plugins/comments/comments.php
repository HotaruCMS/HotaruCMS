<?php
/**
 * name: Comments
 * description: Enables logged-in users to comment on posts
 * version: 0.5
 * folder: comments
 * class: Comments
 * requires: submit 0.7, users 0.5
 * hooks: header_include, install_plugin, hotaru_header, theme_index_replace, submit_show_post_extra_fields, submit_post_show_post, admin_plugin_settings, admin_sidebar_plugin_settings, submit_form_2_assign, submit_form_2_fields, submit_form_2_process_submission, userbase_default_permissions
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

class Comments extends pluginFunctions
{
    /**
     * Default settings on install
     */
    public function install_plugin()
    {
        // Create a new empty table called "comments"
        $exists = $this->db->table_exists('comments');
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
            $this->db->query($sql); 
        }
        
        // Create a new empty table called "commentvotes" if it doesn't already exist
        $exists = $this->db->table_exists('commentvotes');
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
            $this->db->query($sql); 
        }
        
        // Default settings 
        $comments_settings['comment_form'] = "checked";
        $comments_settings['comment_avatars'] = "";
        $comments_settings['comment_voting'] = "";
        $comments_settings['comment_levels'] = 5;
        $comments_settings['comment_email'] = SITE_EMAIL;
        $comments_settings['comment_allowable_tags'] = "<b><i><u><a><blockquote><strike>";
        $this->updateSetting('comments_settings', serialize($comments_settings));
        
        // Include language file. Also included in hotaru_header, but needed here so 
        // that the link in the Admin sidebar shows immediately after installation.
        $this->includeLanguage();
    }
    
    
    /**
     * Include css and JavaScript
     */
    public function header_include()
    { 
        $this->hotaru->includeCss('comments', 'comments');
        $this->hotaru->includeJs('comments', 'comments');
        $this->hotaru->includeJs('urldecode.min', 'comments');
    }
    
    
    /**
     * Define table name, include language file and creat global Comments object
     */
    public function hotaru_header()
    {
        if (!defined('TABLE_COMMENTS')) { define("TABLE_COMMENTS", DB_PREFIX . 'comments'); }
        if (!defined('TABLE_COMMENTVOTES')) { define("TABLE_COMMENTVOTES", DB_PREFIX . 'commentvotes'); }
        
        $this->includeLanguage();
        
        // Create a new global object called "comments".
        require_once(PLUGINS . 'comments/libs/Comment.php');
        $this->hotaru->comment = new Comment($this->hotaru);
        
        // Get settings from database if they exist...
        $comments_settings = $this->getSerializedSettings();
    
        // Assign settings to class member
        $this->hotaru->comment->form = $comments_settings['comment_form'];
        $this->hotaru->comment->avatars = $comments_settings['comment_avatars'];
        $this->hotaru->comment->voting = $comments_settings['comment_voting'];
        $this->hotaru->comment->email = $comments_settings['comment_email'];
        $this->hotaru->comment->allowableTags = $comments_settings['comment_allowable_tags'];
        $this->hotaru->comment->levels = $comments_settings['comment_levels'];
    }
    
    
    /**
     * Process a new comment
     *
     * @return bool
     */
    public function theme_index_replace()
    {
        if (($this->hotaru->isPage('comments')) && ($this->hotaru->comment->form == 'checked')) {
        
            if ($this->current_user->loggedIn) {

                if (($this->cage->post->getAlpha('comment_process') == 'newcomment') || 
                    ($this->cage->post->getAlpha('comment_process') == 'editcomment'))
                {
        
                    if ($this->cage->post->keyExists('comment_content')) {
                        $this->hotaru->comment->content = sanitize($this->cage->post->getHtmLawed('comment_content'), 2, $this->hotaru->comment->allowableTags);
                    }
                    
                    if ($this->cage->post->keyExists('comment_post_id')) {
                        $this->hotaru->comment->postId = $this->cage->post->testInt('comment_post_id');
                    }
                    
                    if ($this->cage->post->keyExists('comment_user_id')) {
                        $this->hotaru->comment->author = $this->cage->post->testInt('comment_user_id');
                    }
                
                    if ($this->cage->post->keyExists('comment_parent')) {
                        $this->hotaru->comment->parent = $this->cage->post->testInt('comment_parent');
                        if ($this->cage->post->getAlpha('comment_process') == 'editcomment') {
                            $this->hotaru->comment->id = $this->cage->post->testInt('comment_parent');
                        }
                    }
                    
                    if ($this->cage->post->keyExists('comment_subscribe')) {
                        $this->hotaru->comment->subscribe = 1;
                    } else {
                        $this->hotaru->comment->subscribe = 0;
                        $this->hotaru->comment->unsubscribe($this->hotaru->comment->postId);
                    }
                    
                    if ($this->cage->post->getAlpha('comment_process') == 'newcomment')
                    {
                        // A user can unsubscribe by submitting an empty comment, so...
                        if($this->hotaru->comment->content != '') {
                            $this->hotaru->comment->addComment();
                            $this->hotaru->comment->emailCommentSubscribers($this->hotaru->comment->postId);
                        } else {
                            //comment empty so just check subscribe box:
                            $this->hotaru->comment->updateSubscribe($this->hotaru->comment->postId);
                        }
                    }
                    elseif($this->cage->post->getAlpha('comment_process') == 'editcomment')
                    {
                            $this->hotaru->comment->editComment();
                    }
                    
                    header("Location: " . $this->hotaru->url(array('page'=>$this->hotaru->comment->postId)));    // Go to the post
                    die();
                    
                }
    
            }
            
        }
    
        return false;
    }
    
    
    /**
     * Display Admin settings page
     *
     * @return true
     */
    public function admin_plugin_settings()
    {
        require_once(PLUGINS . 'comments/comments_settings.php');
        $comSettings = new CommentsSettings($this->folder, $this->hotaru);
        $comSettings->settings();
        return true;
    }
    
    
    /**
     * Link to comments
     */
    public function submit_show_post_extra_fields()
    {
        echo '<li><a class="comment_link" href="' . $this->hotaru->url(array('page'=>$this->hotaru->post->id)) . '">' . $this->hotaru->comment->countComments() . '</a></li>' . "\n";
    }
    
    
    /**
     * Prepare and display comments wrapper and form
     */
    public function submit_post_show_post()
    {
        // set default
        $this->hotaru->vars['subscribe_check'] = ''; 
        
        // Check if the current_user is the post author
        if ($this->hotaru->post->author == $this->current_user->id) {
            // Check if the user subscribed to comments as a submitter
            if ($this->hotaru->post->subscribe == 1) { 
                $this->hotaru->vars['subscribe_check'] = 'checked';
            } 
        } 
        
        // Check if the user subscribed to comments as a commenter
        $sql = "SELECT COUNT(comment_subscribe) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_user_id = %d AND comment_subscribe = %d";
        $subscribe_result = $this->db->get_var($this->db->prepare($sql, $this->hotaru->post->id, $this->current_user->id, 1));
        
        if ($subscribe_result > 0) { 
            $this->hotaru->vars['subscribe_check'] = 'checked';
        } 
        
        $parents = $this->hotaru->comment->readAllParents($this->hotaru->post->id);
            
        if (!$this->hotaru->isPage('submit2')) {
            echo "<!--  START COMMENTS_WRAPPER -->\n";
            echo "<div id='comments_wrapper'>\n";
            echo "<h2>" . $this->hotaru->comment->countComments(false) . "</h2>\n";
            
            if ($parents) { 
                foreach ($parents as $parent) {
                    $this->displayComment($parent);
                    $this->commentTree($parent->comment_id, 0);
                    $this->hotaru->comment->depth = 0;
                }
            }
            echo "</div><!-- close comments_wrapper -->\n";
            echo "<!--  END COMMENTS -->\n";
        }
        
        if ($this->current_user->getPermission('can_comment') == 'no') {
            echo "<div class='comment_form_off'>" . $this->lang['comments_no_permission'] . "</div>";
            return false;
        }
        
        if (!$this->current_user->loggedIn) {
            echo "<div class='comment_form_off'>" . $this->lang['comments_please_login'] . "</div>";
            return false;
        }
        
        if ($this->hotaru->comment->form != 'checked') {
            echo "<div class='comment_form_off'>" . $this->lang['comments_form_closed'] . "</div>";
            return false;
        }
 
        if (!$this->hotaru->isPage('submit2')) {
            // force non-reply form to have parent "0" and depth "0"
            $this->hotaru->comment->id = 0;
            $this->hotaru->comment->depth = 0;
            $this->hotaru->displayTemplate('comment_form', 'comments', $this->hotaru, false);
        }
    }
    
    
    /**
     * Recurse through comment tree
     *
     * @param int $item_id - id of current comment
     * @param int $depth - for comment nesting
     * @return bool
     */
    public function commentTree($item_id, $depth)
    {
        while ($children = $this->hotaru->comment->readAllChildren($this->hotaru->post->id, $item_id)) {
            foreach ($children as $child) {
                $depth++;
                if ($depth == $this->hotaru->comment->levels) { 
                    // Prevent depth exceeding nesting levels
                    // levels start at 0 so we're using -1.
                    $depth = $this->hotaru->comment->levels - 1;
                }
                $this->hotaru->comment->depth = $depth;
                $this->displayComment($child);
                if ($this->commentTree($child->comment_id, $depth)) {
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
    public function displayComment($item)
    {
        if (!$this->hotaru->isPage('submit2')) {
            $this->hotaru->comment->readComment($item);
            $this->hotaru->displayTemplate('show_comments', 'comments', $this->hotaru, false);
            $this->hotaru->displayTemplate('comment_form', 'comments', $this->hotaru, false);
        }
    }
    
    
    /**
     * Check and update post_submit in Submit step 2 and Post Edit pages
     */
    public function submit_form_2_assign()
    {
        if ($this->cage->post->getAlpha('submit2') == 'true') 
        {
            if ($this->cage->post->keyExists('post_subscribe')) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
        } 
        elseif ($this->cage->post->getAlpha('submit3') == 'edit')
        {
            if ($this->hotaru->post->subscribe == 1) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
        }
        elseif ($this->hotaru->isPage('edit_post')) 
        {
            if ($this->cage->post->getAlpha('edit_post') == 'true') {
                if ($this->cage->post->keyExists('post_subscribe')) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
            } else {
                if ($this->hotaru->post->subscribe == 1) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
            }
        }
        else 
        {
            $subscribe_check = "";
        }
        
        $this->hotaru->vars['subscribe_check'] = $subscribe_check;
    }
    
    
    /**
     * Show post_subscribe option in Submit step 2 and Post Edit
     */
    public function submit_form_2_fields()
    {
        echo "<tr><td colspan='3'>\n";
        echo "<input id='post_subscribe' name='post_subscribe' type='checkbox' " . $this->hotaru->vars['subscribe_check'] . "> " . $this->lang['submit_form_subscribe']; 
        echo "</tr>";
    }
    
    
    /**
     * Save post_subscribe to the database
     */
    public function submit_form_2_process_submission() 
    {
        if ($this->cage->post->keyExists('post_subscribe')) { $this->hotaru->post->subscribe = 1; } else { $this->hotaru->post->subscribe = 0; } 
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
        $perms['options']['can_comment'] = array('yes', 'no');
        $perms['options']['can_edit_comments'] = array('yes', 'no', 'own');
        
        // Permissions for $role
        switch ($role) {
            case 'admin':
                $perms['can_comment'] = 'yes';
                $perms['can_edit_comments'] = 'yes';
                break;
            case 'member':
                $perms['can_comment'] = 'yes';
                $perms['can_edit_comments'] = 'own';
                break;
            default:
                $perms['can_submit'] = 'no';
                $perms['can_edit_comments'] = 'no';
        }
        
        $this->hotaru->vars['perms'] = $perms;
    }
    
}

?>