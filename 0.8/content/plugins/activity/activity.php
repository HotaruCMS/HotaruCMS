<?php
/**
 * name: Activity
 * description: Show recent activity
 * version: 0.1
 * folder: activity
 * class: Activity
 * requires: users 0.8
 * hooks: hotaru_header, header_include, comment_post_add_comment, comment_update_comment, com_man_approve_all_comments, comment_delete_comment, post_add_post, post_update_post, post_change_status, post_delete_post, userbase_killspam, vote_positive_vote, vote_negative_vote, vote_flag_insert
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

class Activity extends PluginFunctions
{
    /**
     * Add activity when new comment posted
     */
    public function comment_post_add_comment()
    {
        $comment_id = $this->hotaru->comment->vars['last_insert_id'];
        $comment_user_id = $this->hotaru->comment->author;
        $comment_post_id = $this->hotaru->comment->postId;
        $comment_status = $this->hotaru->comment->status;
        
        if ($comment_status != "approved") { $status = "hide"; } else { $status = "show"; }
        
        $sql = "INSERT INTO " . TABLE_USERACTIVITY . " SET useract_archived = %s, useract_userid = %d, useract_status = %s, useract_key = %s, useract_value = %s, useract_key2 = %s, useract_value2 = %s, useract_date = CURRENT_TIMESTAMP, useract_updateby = %d";
        $this->db->query($this->db->prepare($sql, 'N', $comment_user_id, $status, 'comment', $comment_id, 'post', $comment_post_id, $this->current_user->id));
    }
    
    
    /**
     * Update show/hide status when a comment is edited
     */
    public function comment_update_comment()
    {
        $comment_status = $this->hotaru->comment->status;
        
        if ($comment_status != "approved") { $status = "hide"; } else { $status = "show"; }
        
        $sql = "UPDATE " . TABLE_USERACTIVITY . " SET useract_status = %s, useract_updateby = %d WHERE useract_key = %s AND useract_value = %d";
        $this->db->query($this->db->prepare($sql, $status, $this->current_user->id, 'comment', $this->hotaru->comment->id));
    }
    
    
    /**
     * Make all comments "show" when mass-approved in comment manager
     */
    public function com_man_approve_all_comments()
    {
        $sql = "UPDATE " . TABLE_USERACTIVITY . " SET useract_status = %s, useract_updateby = %d WHERE useract_key = %s AND useract_status = %d";
        $this->db->query($this->db->prepare($sql, 'show', $this->current_user->id, 'comment', 'hide'));
    }
    
    
    /**
     * Delete comment from activity table
     */
    public function comment_delete_comment()
    {
        $sql = "DELETE FROM " . TABLE_USERACTIVITY . " WHERE useract_key = %s AND useract_value = %d";
        $this->db->query($this->db->prepare($sql, 'comment', $this->hotaru->comment->id));
    }


    /**
     * Add activity when new post submitted
     */
    public function post_add_post()
    {
        $post_id = $this->hotaru->post->vars['last_insert_id'];
        $post_author = $this->hotaru->post->author;
        $post_status = $this->hotaru->post->status;
        
        if ($post_status != 'new' && $post_status != 'top') { $status = "hide"; } else { $status = "show"; }
        
        $sql = "INSERT INTO " . TABLE_USERACTIVITY . " SET useract_archived = %s, useract_userid = %d, useract_status = %s, useract_key = %s, useract_value = %s, useract_date = CURRENT_TIMESTAMP, useract_updateby = %d";
        $this->db->query($this->db->prepare($sql, 'N', $post_author, $status, 'post', $post_id, $this->current_user->id));
    }
    
    
    /**
     * Update activity when post is updated
     */
    public function post_update_post()
    {
        $post_status = $this->hotaru->post->status;
        
        if ($post_status != 'new' && $post_status != 'top') { $status = "hide"; } else { $status = "show"; }
        
        $sql = "UPDATE " . TABLE_USERACTIVITY . " SET useract_status = %s, useract_updateby = %d WHERE useract_key = %s AND useract_value = %d";
        $this->db->query($this->db->prepare($sql, $status, $this->current_user->id, 'post', $this->hotaru->post->id));
    }
    
    
    /**
     * Update activity when post status is changed
     */
    public function post_change_status()
    {
        $this->post_update_post();
    }
    
    
    /**
     * Delete post from activity table
     */
    public function post_delete_post()
    {
        $sql = "DELETE FROM " . TABLE_USERACTIVITY . " WHERE useract_key = %s AND useract_value = %d";
        $this->db->query($this->db->prepare($sql, 'post', $this->hotaru->post->id));
    }
    
    
    /**
     * Delete votes from activity table
     */
    public function userbase_killspam($vars = array())
    {
        $user_id = $vars['target_user'];
        
        $sql = "DELETE FROM " . TABLE_USERACTIVITY . " WHERE useract_userid = %d AND useract_key = %s";
        $this->db->query($this->db->prepare($sql, 'vote', $user_id));
    }
    
    
    /**
     * Add activity when voting on a post
     */
    public function vote_positive_vote($vars)
    {
        $user_id = $vars['user'];
        $post_id = $vars['post'];
        
        // we don't need the status because if the post wasn't visible, it couldn't be voted for.
        
        $sql = "INSERT INTO " . TABLE_USERACTIVITY . " SET useract_archived = %s, useract_userid = %d, useract_status = %s, useract_key = %s, useract_value = %s, useract_key2 = %s, useract_value2 = %s, useract_date = CURRENT_TIMESTAMP, useract_updateby = %d";
        $this->db->query($this->db->prepare($sql, 'N', $user_id, 'show', 'vote', 'up', 'post', $post_id, $this->current_user->id));
    }
    
    
    /**
     * Add activity when voting down or removing a vote from a post
     */
    public function vote_negative_vote($vars)
    {
        $user_id = $vars['user'];
        $post_id = $vars['post'];
        
        // we don't need the status because if the post wasn't visible, it couldn't be voted for.
        
        $sql = "INSERT INTO " . TABLE_USERACTIVITY . " SET useract_archived = %s, useract_userid = %d, useract_status = %s, useract_key = %s, useract_value = %s, useract_key2 = %s, useract_value2 = %s, useract_date = CURRENT_TIMESTAMP, useract_updateby = %d";
        $this->db->query($this->db->prepare($sql, 'N', $user_id, 'show', 'vote', 'down', 'post', $post_id, $this->current_user->id));
    }
    
    
    /**
     * Add activity when flagging a post
     */
    public function vote_flag_insert()
    {
        // we don't need the status because if the post wasn't visible, it couldn't be voted for.
        
        $sql = "INSERT INTO " . TABLE_USERACTIVITY . " SET useract_archived = %s, useract_userid = %d, useract_status = %s, useract_key = %s, useract_value = %s, useract_key2 = %s, useract_value2 = %s, useract_date = CURRENT_TIMESTAMP, useract_updateby = %d";
        $this->db->query($this->db->prepare($sql, 'N', $this->current_user->id, 'show', 'vote', 'flag', 'post', $this->hotaru->post->id, $this->current_user->id));
    }

}
?>