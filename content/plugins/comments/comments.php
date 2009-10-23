<?php
/**
 * name: Comments
 * description: Enables logged-in users to comment on posts
 * version: 0.9
 * folder: comments
 * class: Comments
 * requires: submit 0.7, users 0.5
 * hooks: header_include, install_plugin, upgrade_plugin, hotaru_header, theme_index_replace, submit_show_post_extra_fields, submit_post_show_post, admin_plugin_settings, admin_sidebar_plugin_settings, submit_form_2_assign, submit_form_2_fields, submit_edit_post_admin_fields, submit_form_2_process_submission, userbase_default_permissions, post_delete_post
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
     * Upgrade plugin
     */
    public function upgrade_plugin()
    {
        if (!$this->db->column_exists('comments', 'comment_status')) {
            // add new comment_status field
            $sql = "ALTER TABLE " . DB_PREFIX . "comments ADD comment_status varchar(32)  NOT NULL DEFAULT 'approved' AFTER comment_date";
            $this->db->query($this->db->prepare($sql));
     
            // make content field fulltext for better searching
            $sql = "ALTER TABLE " . DB_PREFIX . "comments ADD FULLTEXT(comment_content)";
            $this->db->query($this->db->prepare($sql));
        }
        
        if (!$this->db->column_exists('posts', 'post_comments')) {
            // add new post_comments field
            $sql = "ALTER TABLE " . DB_PREFIX . "posts ADD post_comments ENUM(%s, %s) NOT NULL DEFAULT %s AFTER post_subscribe";
            $this->db->query($this->db->prepare($sql, 'open', 'closed', 'open'));
        }
    }


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
              `comment_status` varchar(32) NOT NULL DEFAULT 'approved',
              `comment_content` text NOT NULL,
              `comment_votes` int(20) NOT NULL DEFAULT '0',
              `comment_subscribe` tinyint(1) NOT NULL DEFAULT '0',
              `comment_updateby` int(20) NOT NULL DEFAULT 0,
              FULLTEXT (`comment_content`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Comments';";
            $this->db->query($sql); 
        } else {
            $this->upgrade_plugin();
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
        
        // Add post_comments field to the posts table for opening/closing individual comment threads
        if (!$this->db->column_exists('posts', 'post_comments')) {
            $sql = "ALTER TABLE " . DB_PREFIX . "posts ADD post_comments ENUM(%s, %s) NOT NULL DEFAULT %s AFTER post_subscribe";
            $this->db->query($this->db->prepare($sql, 'open', 'closed', 'open'));
        }
        
        // Default settings 
        $comments_settings['comment_form'] = "checked";
        if ($this->isActive('gravatar')) {
            $comments_settings['comment_avatars'] = "checked";
        } else {
            $comments_settings['comment_avatars'] = "";
        }
        $comments_settings['comment_voting'] = "";
        $comments_settings['comment_levels'] = 5;
        $comments_settings['comment_email'] = SITE_EMAIL;
        $comments_settings['comment_allowable_tags'] = "<b><i><u><a><blockquote><strike>";
        $comments_settings['comment_set_pending'] = ""; // sets all new comments to pending (needs Comment Manager to view them)
        
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
        $this->hotaru->comment->avatars = $comments_settings['comment_avatars'];
        $this->hotaru->comment->voting = $comments_settings['comment_voting'];
        $this->hotaru->comment->email = $comments_settings['comment_email'];
        $this->hotaru->comment->allowableTags = $comments_settings['comment_allowable_tags'];
        $this->hotaru->comment->levels = $comments_settings['comment_levels'];
        $this->hotaru->comment->setPending = $comments_settings['comment_set_pending'];
        $this->hotaru->comment->allforms = $comments_settings['comment_all_forms'];
    }
    
    
    /**
     * Process a new comment
     *
     * @return bool
     */
    public function theme_index_replace()
    {
        // Is the comment form open on this thread? 
        $this->hotaru->comment->thisform = $this->hotaru->comment->formStatus('select'); // returns 'open' or 'closed'

        if (   ($this->hotaru->isPage('comments')) 
            && ($this->hotaru->comment->thisform == 'open')
            && ($this->hotaru->comment->allforms == 'checked')) {
            
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
                        // before posting, we need to be certain this user has permission:
                        $safe = false;
                        $can_comment = $this->hotaru->current_user->getPermission('can_comment');
                        if ($can_comment == 'yes') { $safe = true; }
                        if ($can_comment == 'mod') { $safe = true; $this->hotaru->comment->status = 'pending'; }
                        
                        // Okay, safe to add the comment...
                        if ($safe) {
                            // A user can unsubscribe by submitting an empty comment, so...
                            if($this->hotaru->comment->content != '') {
                                $this->hotaru->comment->addComment();
                                // email comment subscribers if this comment has 'approved' status:
                                if ($this->hotaru->comment->status == 'approved') {
                                    $this->hotaru->comment->emailCommentSubscribers($this->hotaru->comment->postId);
                                }
                            } else {
                                //comment empty so just check subscribe box:
                                $this->hotaru->comment->updateSubscribe($this->hotaru->comment->postId);
                            }
                        }
                    }
                    elseif($this->cage->post->getAlpha('comment_process') == 'editcomment')
                    {
                        // before editing, we need to be certain this user has permission:
                        $safe = false;
                        $can_edit = $this->hotaru->current_user->getPermission('can_edit_comments');
                        if ($can_edit == 'yes') { $safe = true; }
                        if (($can_edit == 'own') && ($this->hotaru->current_user->id == $this->hotaru->comment->author)) { $safe = true; }
                        if ($safe) {
                            $this->hotaru->comment->editComment();
                        }
                    }
                    
                    header("Location: " . $this->hotaru->url(array('page'=>$this->hotaru->comment->postId)));    // Go to the post
                    die();
                    
                }
                
                // set current comment and responses to pending:
                if ($this->cage->get->getAlpha('action') == 'setpending') { 
                
                    // before setting pending, we need to be certain this user has permission:
                    if ($this->hotaru->current_user->getPermission('can_set_comments_pending') == 'yes') {
                        $cid = $this->cage->get->testInt('cid'); // comment id
                        $comment = $this->hotaru->comment->getComment($cid);
                        $this->hotaru->comment->readComment($comment); // read comment
                        $this->hotaru->comment->status = 'pending'; // set to pending
                        $this->hotaru->comment->editComment();  // update this comment
    
                        $this->hotaru->comment->postId = $this->cage->get->testInt('pid');  // post id
                        $this->hotaru->comment->setPendingCommentTree($cid);   // set all responses to 'pending', too.
                        
                        // redirect back to thread:
                        $this->hotaru->post = new Post($this->hotaru);
                        $this->hotaru->post->readPost($this->hotaru->comment->postId);
                        header("Location: " . $this->hotaru->url(array('page'=>$this->hotaru->post->id)));    // Go to the post
                        die();
                    }
                }
                
                // delete current comment and responses:
                if ($this->cage->get->getAlpha('action') == 'delete') { 
                
                    // before deleting a comment, we need to be certain this user has permission:
                    if ($this->hotaru->current_user->getPermission('can_delete_comments') == 'yes') {
                        $cid = $this->cage->get->testInt('cid'); // comment id
                        $comment = $this->hotaru->comment->getComment($cid);
                        $this->hotaru->comment->readComment($comment); // read comment
                        
                        $this->pluginHook('comments_delete_comment');
                        
                        $this->hotaru->comment->deleteComment(); // delete this comment
    
                        $this->hotaru->comment->postId = $this->cage->get->testInt('pid');  // post id
                        $this->hotaru->comment->deleteCommentTree($cid);   // delete all rsponses, too.
                        
                        // redirect back to thread:
                        $this->hotaru->post = new Post($this->hotaru);
                        $this->hotaru->post->readPost($this->hotaru->comment->postId);
                        header("Location: " . $this->hotaru->url(array('page'=>$this->hotaru->comment->postId)));    // Go to the post
                        die();
                    }
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
        
        if (($this->hotaru->comment->thisform == 'closed') 
            || ($this->hotaru->comment->allforms != 'checked')) {
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
        while ($children = $this->hotaru->comment->readAllChildren($item_id)) {
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
            if ($this->hotaru->comment->status == 'approved') {
                $this->hotaru->displayTemplate('show_comments', 'comments', $this->hotaru, false);
                
                // don't show the reply form in these cases:
                if ($this->current_user->getPermission('can_comment') == 'no') { return false; }
                if (!$this->current_user->loggedIn) { return false; }
                if ($this->hotaru->comment->thisform == 'closed') { return false; }
                if ($this->hotaru->comment->allforms != 'checked') { return false; }
        
                // show the reply form:
                $this->hotaru->displayTemplate('comment_form', 'comments', $this->hotaru, false);
            }
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
     * Show Enable comment form option in Post Edit
     */
    public function submit_edit_post_admin_fields()
    {
        $this->hotaru->comment->thisform = $this->hotaru->comment->formStatus('select'); // returns 'open' or 'closed'
        if ($this->hotaru->comment->thisform == 'open') { $form_open = 'checked'; } else { $form_open = ''; }

        echo "<tr><td colspan='3'>\n";
        echo "<input id='enable_comments' name='enable_comments' type='checkbox' " . $form_open . "> " . $this->lang['submit_form_enable_comments']; 
        echo "</tr>";
    }
    
    
    /**
     * Save post_subscribe to the database
     */
    public function submit_form_2_process_submission() 
    {
        if ($this->cage->post->keyExists('post_subscribe')) { $this->hotaru->post->subscribe = 1; } else { $this->hotaru->post->subscribe = 0; } 
        
        if ($this->cage->post->keyExists('edit_post'))
        {
            // enable/disable comment form for this post
            if ($this->cage->post->keyExists('enable_comments')) { 
                $this->hotaru->comment->formStatus('open');
            } else {
                $this->hotaru->comment->formStatus('closed');
            }
        }
    }
    
    
    /**
     * Delete comments when post deleted
     */
    public function post_delete_post()
    {
        $sql = "DELETE FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->id));
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
        $perms['options']['can_comment'] = array('yes', 'no', 'mod');
        $perms['options']['can_edit_comments'] = array('yes', 'no', 'own');
        $perms['options']['can_set_comments_pending'] = array('yes', 'no');
        $perms['options']['can_delete_comments'] = array('yes', 'no');
        
        // Permissions for $role
        switch ($role) {
            case 'admin':
            case 'supermod':
                $perms['can_comment'] = 'yes';
                $perms['can_edit_comments'] = 'yes';
                $perms['can_set_comments_pending'] = 'yes';
                $perms['can_delete_comments'] = 'yes';
                break;
            case 'moderator':
                $perms['can_comment'] = 'yes';
                $perms['can_edit_comments'] = 'yes';
                $perms['can_set_comments_pending'] = 'yes';
                $perms['can_delete_comments'] = 'no';
                break;
            case 'member':
                $perms['can_comment'] = 'yes';
                $perms['can_edit_comments'] = 'own';
                $perms['can_set_comments_pending'] = 'no';
                $perms['can_delete_comments'] = 'no';
                break;
            case 'undermod':
                $perms['can_comment'] = 'mod';
                $perms['can_edit_comments'] = 'own';
                $perms['can_set_comments_pending'] = 'no';
                $perms['can_delete_comments'] = 'no';
                break;
            default:
                $perms['can_comment'] = 'no';
                $perms['can_edit_comments'] = 'no';
                $perms['can_set_comments_pending'] = 'no';
                $perms['can_delete_comments'] = 'no';
        }
        
        $this->hotaru->vars['perms'] = $perms;
    }
    
}

?>