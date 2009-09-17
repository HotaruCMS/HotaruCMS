<?php
/**
 * name: Comments
 * description: Enables logged-in users to comment on posts
 * version: 0.3
 * folder: comments
 * class: Comments
 * requires: submit 0.6, users 0.4
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

class Comments extends pluginFunctions
{
    /**
     * Default settings on install
     */
    public function install_plugin()
    {
        global $db, $lang;
            
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
        global $hotaru;
        
        $hotaru->includeCss('comments', 'comments');
        $hotaru->includeJs('comments', 'comments');
        $hotaru->includeJs('urldecode.min', 'comments');
    }
    
    
    /**
     * Define table name, include language file and creat global Comments object
     */
    public function hotaru_header()
    {
        global $lang, $comment;
    
        if (!defined('TABLE_COMMENTS')) { define("TABLE_COMMENTS", DB_PREFIX . 'comments'); }
        if (!defined('TABLE_COMMENTVOTES')) { define("TABLE_COMMENTVOTES", DB_PREFIX . 'commentvotes'); }
        
        $this->includeLanguage();
        
        // Create a new global object called "comments".
        require_once(PLUGINS . 'comments/libs/Comment.php');
        $comment = new Comment();
        
        // Get settings from database if they exist...
        $comments_settings = $this->getSerializedSettings();
    
        // Assign settings to class member
        $comment->setForm($comments_settings['comment_form']);
        $comment->setAvatars($comments_settings['comment_avatars']);
        $comment->setVoting($comments_settings['comment_voting']);
        $comment->setEmail($comments_settings['comment_email']);
        $comment->setAllowableTags($comments_settings['comment_allowable_tags']);
        $comment->setLevels($comments_settings['comment_levels']);
            
        $vars['comment'] = $comment; 
        return $vars; 
    }
    
    
    /**
     * Process a new comment
     *
     * @return bool
     */
    public function theme_index_replace()
    {
        global $hotaru, $cage, $post, $current_user, $comment;
        
        if (($hotaru->isPage('comments')) && ($comment->getForm() == 'checked')) {
        
            if ($current_user->getLoggedIn()) {

                if (($cage->post->getAlpha('comment_process') == 'newcomment') || 
                    ($cage->post->getAlpha('comment_process') == 'editcomment'))
                {
                    
                    //Include HTMLPurifier which we'll use on comment_content
                    $cage->post->loadHTMLPurifier(EXTENSIONS . 'HTMLPurifier/HTMLPurifier.standalone.php');
        
                    if ($cage->post->keyExists('comment_content')) {
                        $comment->setContent(sanitize($cage->post->getPurifiedHTML('comment_content'), 2, $comment->getAllowableTags()));
                    }
                    
                    if ($cage->post->keyExists('comment_post_id')) {
                        $comment->setPostId($cage->post->testInt('comment_post_id'));
                        echo $comment->getPostId();
                    }
                    
                    if ($cage->post->keyExists('comment_user_id')) {
                        echo "userid: " . $cage->post->testInt('comment_user_id') . "<br />";
                        $comment->setAuthor($cage->post->testInt('comment_user_id'));
                    }
                    
                    if ($cage->post->keyExists('comment_parent')) {
                        $comment->setParent($cage->post->testInt('comment_parent'));
                    }
                    
                    if ($cage->post->keyExists('comment_subscribe')) {
                        $comment->setSubscribe(1);
                    } else {
                        $comment->setSubscribe(0);
                        $comment->unsubscribe($comment->getPostId());
                    }
                    
                    if ($cage->post->getAlpha('comment_process') == 'newcomment')
                    {
                        // A user can unsubscribe by submitting an empty comment, so...
                        if($comment->getContent() != '') {
                            $comment->addComment();
                            $comment->emailCommentSubscribers($comment->getPostId());
                        } else {
                            //comment empty so just check subscribe box:
                            $comment->updateSubscribe($comment->getPostId());
                        }
                    }
                    elseif($cage->post->getAlpha('comment_process') == 'editcomment')
                    {
                            $comment->editComment();
                    }
                    
                    header("Location: " . url(array('page'=>$comment->getPostId())));    // Go to the post
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
        $comSettings = new CommentsSettings();
        $comSettings->settings($this->folder);
        return true;
    }
    
    
    /**
     * Link to comments
     */
    public function submit_show_post_extra_fields()
    {
        global $post, $lang, $comment;
        
        echo '<li><a class="comments_comments_link" href="' . url(array('page'=>$post->getId())) . '">' . $comment->countComments() . '</a></li>' . "\n";
    }
    
    
    /**
     * Prepare and display comments wrapper and form
     */
    public function submit_post_show_post()
    {
        global $db, $hotaru, $comment, $post, $current_user;
        
        // set default
        $current_user->vars['postSubscribed'] = false; 
        
        // Check if the current_user is the post author
        if ($post->getAuthor() == $current_user->getId()) {
            // Check if the user subscribed to comments as a submitter
            if ($post->getSubscribe() == 1) { 
                $current_user->vars['postSubscribed'] = true; 
            } 
        } 
        
        // Check if the user subscribed to comments as a commenter
        $sql = "SELECT COUNT(comment_subscribe) FROM " . TABLE_COMMENTS . " WHERE comment_post_id = %d AND comment_user_id = %d AND comment_subscribe = %d";
        $subscribe_result = $db->get_var($db->prepare($sql, $post->getId(), $current_user->getId(), 1));
        
        if ($subscribe_result > 0) { 
            $current_user->vars['postSubscribed'] = true; 
        } 
        
        $parents = $comment->readAllParents($post->getId());
        if ($parents) { 
            
            echo "<!--  START COMMENTS_WRAPPER -->\n";
            echo "<div id='comments_wrapper'>\n";
            echo "<h2>" . $comment->countComments() . "</h2>\n";
            
            foreach ($parents as $parent) {
                
                $this->displayComment($parent);
                $this->commentTree($parent->comment_id, 0);
            }
            
            echo "</div><!-- close comments_wrapper -->\n";
            echo "<!--  END COMMENTS -->\n";
            
        }
        
        if ($comment->getForm() == 'checked' && !$hotaru->isPage('submit2')) {
            // force non-reply form to have parent "0" and depth "0"
            $comment->setId(0);
            $comment->setDepth(0);
            $hotaru->displayTemplate('comment_form', 'comments', false);
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
        global $comment, $post;
            
        while ($children = $comment->readAllChildren($post->getId(), $item_id)) {
            foreach ($children as $child) {
                $depth++;
                if ($depth == $comment->getLevels()) { 
                    // Prevent depth exceeding nesting levels
                    // levels start at 0 so we're using -1.
                    $depth = $comment->getLevels() - 1;
                }
                $comment->setDepth($depth);
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
        global $hotaru, $comment, $current_user;
           
        if (!$hotaru->isPage('submit2')) {
            $comment->readComment($item);
        
            $hotaru->displayTemplate('show_comments', 'comments', false);
            $hotaru->displayTemplate('comment_form', 'comments', false);
        }
    }
    
    
    /**
     * Check and update post_submit in Submit step 2 and Post Edit pages
     */
    public function submit_form_2_assign()
    {
        global $hotaru, $cage, $post, $subscribe_check;
        
        if ($cage->post->getAlpha('submit2') == 'true') 
        {
            if ($cage->post->keyExists('post_subscribe')) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
        } 
        elseif ($cage->post->getAlpha('submit3') == 'edit')
        {
            if ($post->getSubscribe() == 1) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
        }
        elseif ($hotaru->isPage('edit_post')) 
        {
            if ($cage->post->getAlpha('edit_post') == 'true') {
                if ($cage->post->keyExists('post_subscribe')) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
            } else {
                if ($post->getSubscribe() == 1) { $subscribe_check = 'checked'; } else { $subscribe_check = ''; }
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
    public function submit_form_2_fields()
    {
        global $lang, $subscribe_check;
        
        echo "<tr><td colspan='3'>\n";
        echo "<input id='post_subscribe' name='post_subscribe' type='checkbox' " . $subscribe_check . "> " . $lang['submit_form_subscribe']; 
        echo "</tr>";
    }
    
    
    /**
     * Save post_subscribe to the database
     */
    public function submit_form_2_process_submission() 
    {
        global $post, $cage;
        
        if ($cage->post->keyExists('post_subscribe')) { $post->setSubscribe(1); } else { $post->setSubscribe(0); } 
    }
    
}

?>