<?php
/**
 * name: Activity
 * description: Show recent activity
 * version: 0.1
 * folder: activity
 * class: Activity
 * requires: users 0.8
 * hooks: install_plugin, hotaru_header, header_include, comment_post_add_comment, comment_update_comment, com_man_approve_all_comments, comment_delete_comment, post_add_post, post_update_post, post_change_status, post_delete_post, userbase_killspam, vote_positive_vote, vote_negative_vote, vote_flag_insert, admin_sidebar_plugin_settings, admin_plugin_settings
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
     *  Add default settings for Sidebar Comments plugin on installation
     */
    public function install_plugin()
    {
        // Default settings
        $activity_settings = $this->getSerializedSettings();
        
        if ($this->isActive('gravatar')) {
            if (!isset($activity_settings['activity_sidebar_avatar'])) { $activity_settings['activity_sidebar_avatar'] = "checked"; }
        } else {
            if (!isset($activity_settings['activity_sidebar_avatar'])) { $activity_settings['activity_sidebar_avatar'] = ""; }
        }
        if (!isset($activity_settings['activity_sidebar_avatar_size'])) { $activity_settings['activity_sidebar_avatar_size'] = 16; }
        if (!isset($activity_settings['activity_sidebar_user'])) { $activity_settings['activity_sidebar_user'] = ''; }
        if (!isset($activity_settings['activity_sidebar_number'])) { $activity_settings['activity_sidebar_number'] = 10; }
        
        $this->updateSetting('activity_settings', serialize($activity_settings));
        
        // Default settings
        require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
        $sidebar = new Sidebar($this->hotaru);
        // plugin name, function name, optional arguments
        $sidebar->addWidget('activity', 'activity', '');
    }
    
    
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
    
    
    /**
     * Display the latest activity in the sidebar
     */
    public function sidebar_widget_activity()
    {
        $this->includeLanguage();
        
        // Get settings from database if they exist...
        $activity_settings = $this->getSerializedSettings('activity');
        
        $activity = $this->getLatestActivity($activity_settings);
        
        // build link that will link the widget title to all activity...
        
        $anchor_title = htmlentities($this->lang["activity_title_anchor_title"], ENT_QUOTES, 'UTF-8');
        $title = "<a href='" . $this->hotaru->url(array('page'=>'activity')) . "' title='" . $anchor_title . "'>";
        $title .= $this->lang['activity_title'] . "</a>";
        
        if (isset($activity) && !empty($activity)) {
            
            $output = "<h2 class='sidebar_widget_head activity_sidebar_title'>\n";
            $output .= "<a href='" . $this->hotaru->url(array('page'=>'rss_activity')) . "' title='" . $anchor_title . "'>\n";
            $output .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png'>\n</a>&nbsp;"; // RSS icon
            $link = BASEURL;
            $output .= $title . "</h2>\n"; 
                
            $output .= "<ul class='sidebar_widget_body activity_sidebar_items'>\n";
            
            $output .= $this->getActivityItems($activity, $activity_settings);
            $output .= "</ul>\n\n";
        }
        
        // Display the whole thing:
        if (isset($output) && $output != '') { echo $output; }
    }
    
    
    /**
     * Get activity
     *
     * return array $activity
     */
    public function getLatestActivity($activity_settings)
    {
        $sql = "SELECT * FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s ORDER BY useract_date DESC LIMIT " . $activity_settings['activity_sidebar_number'];
        $activity = $this->db->get_results($this->db->prepare($sql, 'show'));
        
        if ($activity) { return $activity; } else { return false; }
    }
    
    
    /**
     * Get activity items
     *
     * @param array $activity 
     * return string $output
     */
    public function getActivityItems($activity = array(), $activity_settings)
    {
        // we need categories for the url
        if ($this->hotaru->post->vars['useCategories']) {
            require_once(PLUGINS . 'categories/libs/Category.php');
            $cat = new Category($this->db);
        }
        
        $this->hotaru->post = new Post($this->hotaru); // used to get post information
        $user = new UserBase($this->hotaru);
                
        if (!$activity) { return false; }
        
        foreach ($activity as $item)
        {
            // Post used in Hotaru's url function
            if ($item->useract_key == 'post') {
                $this->hotaru->post->readPost($item->useract_value);
            } elseif  ($item->useract_key2 == 'post') {
                $this->hotaru->post->readPost($item->useract_value2);
            }
                       
            // get user details
            $user->getUserBasic($item->useract_userid);
            
            if ($this->hotaru->post->vars['useCategories'] && ($this->hotaru->post->vars['category'] != 1)) {
                $this->hotaru->post->vars['category'] = $this->hotaru->post->vars['category'];
                $this->hotaru->post->vars['catSafeName'] =  $cat->getCatSafeName($this->hotaru->post->vars['category']);
            }

            // OUTPUT ITEM
            $output .= "<li class='activity_sidebar_item'>\n";
            
            if ($activity_settings['activity_sidebar_avatar'] && $this->isActive('gravatar')) {
                $this->hotaru->vars['gravatar_size'] = $activity_settings['activity_sidebar_avatar_size'];
                $grav = new Gravatar('', $this->hotaru);
                $output .= "<div class='activity_sidebar_avatar'>\n" . $grav->showGravatarLink($user->name, $user->email, true) . "</div> \n";
            }
            
            if ($activity_settings['activity_sidebar_user']) {
                $output .= "<a class='activity_sidebar_user' href='" . $this->hotaru->url(array('user' => $user->name)) . "'>" . $user->name . "</a> \n";
            }
            
            $output .= "<div class='activity_sidebar_content'>\n";
            
            $post_title = stripslashes(html_entity_decode(urldecode($this->hotaru->post->title), ENT_QUOTES,'UTF-8'));
            $title_link = $this->hotaru->url(array('page'=>$this->hotaru->post->id));
            $cid = ''; // comment id string
            
            switch ($item->useract_key) {
                case 'comment':
                    $output .= $this->hotaru->lang["activity_commented"] . " ";
                    $cid = "#c" . $item->useract_value; // comment id to be put on the end of the url
                    break;
                case 'post':
                    $output .= $this->hotaru->lang["activity_submitted"] . " ";
                    break;
                case 'vote':
                    switch ($item->useract_value) {
                        case 'up':
                            $output .= $this->hotaru->lang["activity_voted_up"] . " ";
                            break;
                        case 'down':
                            $output .= $this->hotaru->lang["activity_voted_down"] . " ";
                            break;
                        case 'flag':
                            $output .= $this->hotaru->lang["activity_voted_flagged"] . " ";
                            break;
                        default:
                            $output .= "Error: Invaild activity vote value.";
                    }
                    break;
                default:
                    $output .= "Error: Invaild activity key.";
            }
                
            $output .= "&quot;<a href='" . $title_link . $cid . "' >" . $post_title . "</a>&quot;\n";
            $output .= "<div>\n";
            $output .= "</li>\n\n";
        }
        
        return $output;
    }

}
?>