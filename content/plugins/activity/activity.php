<?php
/**
 * name: Activity
 * description: Show recent activity
 * version: 0.7
 * folder: activity
 * class: Activity
 * requires: users 1.1, widgets 0.6
 * hooks: install_plugin, header_include, comment_post_add_comment, comment_update_comment, com_man_approve_all_comments, comment_delete_comment, post_add_post, post_update_post, post_change_status, post_delete_post, userbase_killspam, vote_positive_vote, vote_negative_vote, vote_flag_insert, admin_sidebar_plugin_settings, admin_plugin_settings, theme_index_top, theme_index_main, profile, breadcrumbs
 * author: Nick Ramsay
 * authorurl: http://hotarucms.org/member.php?1-Nick
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

class Activity
{
    /**
     *  Add default settings for Sidebar Comments plugin on installation
     */
    public function install_plugin($h)
    {
        // Default settings
        $activity_settings = $h->getSerializedSettings();
        
        if ($h->isActive('avatar')) {
            if (!isset($activity_settings['widget_avatar'])) { $activity_settings['widget_avatar'] = "checked"; }
        } else {
            if (!isset($activity_settings['widget_avatar'])) { $activity_settings['widget_avatar'] = ""; }
        }
        if (!isset($activity_settings['widget_avatar_size'])) { $activity_settings['widget_avatar_size'] = 16; }
        if (!isset($activity_settings['widget_user'])) { $activity_settings['widget_user'] = ''; }
        if (!isset($activity_settings['widget_number'])) { $activity_settings['widget_number'] = 10; }
        if (!isset($activity_settings['number'])) { $activity_settings['number'] = 20; }
        if (!isset($activity_settings['rss_number'])) { $activity_settings['rss_number'] = 20; }
        if (!isset($activity_settings['time'])) { $activity_settings['time'] = "checked"; }
        
        $h->updateSetting('activity_settings', serialize($activity_settings));
        
        // widget
        $h->addWidget('activity', 'activity', '');  // plugin name, function name, optional arguments
    }
    
    
    /**
     * Add activity when new comment posted
     */
    public function comment_post_add_comment($h)
    {
        if ($h->comment->status != "approved") { $status = "hide"; } else { $status = "show"; }

        $args['userid'] = $h->comment->author;
        $args['status'] = $status;
        $args['key'] = 'comment';
        $args['value'] = $h->vars['last_insert_id'];
        $args['key2'] = 'post';
        $args['value2'] = $h->comment->postId;
        
        $h->insertActivity($args);
    }
    
    
    /**
     * Update show/hide status when a comment is edited
     */
    public function comment_update_comment($h)
    {
        if ($h->comment->status != "approved") { $status = "hide"; } else { $status = "show"; }
        
        $args['status'] = $status;
        $args['where']['key'] = 'comment';
        $args['where']['value'] = $h->comment->id;
        
        $h->updateActivity($args);
    }
    
    
    /**
     * Delete comment from activity table
     */
    public function comment_delete_comment($h)
    {
        $args['key'] = 'comment';
        $args['value'] = $h->comment->id;
        
        $h->removeActivity($args);
        
        $h->clearCache('html_cache', false);
    }
    
    
    /**
     * Make all comments "show" when mass-approved in comment manager
     */
    public function com_man_approve_all_comments($h)
    {
        $args['status'] = 'show';
        $args['where']['key'] = 'comment';
        $args['where']['status'] = 'hide';
        
        $h->updateActivity($args);
    }


    /**
     * Add activity when new post submitted
     */
    public function post_add_post($h)
    {
        if ($h->post->status != 'new' && $h->post->status != 'top') { $status = "hide"; } else { $status = "show"; }
        
        $args['userid'] = $h->post->author;
        $args['status'] = $status;
        $args['key'] = 'post';
        $args['value'] = $h->post->vars['last_insert_id'];
        
        $h->insertActivity($args);
    }
    
    
    /**
     * Update activity when post is updated
     */
    public function post_update_post($h)
    {
        if ($h->post->status != 'new' && $h->post->status != 'top') { $status = "hide"; } else { $status = "show"; }
        
        $args['status'] = 'show';
        $args['where']['key'] = 'post';
        $args['where']['value'] = $h->post->id;
        
        $h->updateActivity($args);
    }
    
    
    /**
     * Update activity when post status is changed
     */
    public function post_change_status($h)
    {
        $this->post_update_post($h);
    }
    
    
    /**
     * Delete post from activity table
     */
    public function post_delete_post($h)
    {
        $h->removeActivity(array('key'=>'post', 'value'=>$h->post->id));
        $h->removeActivity(array('key'=>'comment', 'value2'=>$h->post->id));
        $h->removeActivity(array('key'=>'vote', 'value2'=>$h->post->id));

        $h->clearCache('html_cache', false);
    }
    
    
    /**
     * Delete votes from activity table
     */
    public function userbase_killspam($h, $vars = array())
    {
        $user_id = $vars['target_user'];
        
        $sql = "DELETE FROM " . TABLE_USERACTIVITY . " WHERE useract_userid = %d AND useract_key = %s";
        $h->db->query($h->db->prepare($sql, 'vote', $user_id));
        
        $h->clearCache('html_cache', false);
    }
    
    
    /**
     * Add activity when voting on a post
     */
    public function vote_positive_vote($h, $vars)
    {
        $user_id = $vars['user'];
        $post_id = $vars['post'];
        
        // if we're voting down something we previously voted up, we should remove the previous vote:
        $sql = "DELETE FROM " . TABLE_USERACTIVITY . " WHERE useract_userid = %d AND useract_key = %s AND useract_value = %s AND useract_key2 = %s AND useract_value2 = %d";
        $result = $h->db->query($h->db->prepare($sql, $user_id, 'vote', 'down', 'post', $post_id));
        
        // if there wasn't a previous vote, i.e. nothing was found when we tried to delete it, then we can add it as an up vote:
        if (!$result) {
            $sql = "INSERT INTO " . TABLE_USERACTIVITY . " SET useract_archived = %s, useract_userid = %d, useract_status = %s, useract_key = %s, useract_value = %s, useract_key2 = %s, useract_value2 = %s, useract_date = CURRENT_TIMESTAMP, useract_updateby = %d";
            $h->db->query($h->db->prepare($sql, 'N', $user_id, 'show', 'vote', 'up', 'post', $post_id, $h->currentUser->id));
        } else {
            $h->clearCache('html_cache', false); // clear the html cache in order to update the activity widget after the deletion
        }
    }
    
    
    /**
     * Add activity when voting down or removing a vote from a post
     */
    public function vote_negative_vote($h, $vars)
    {
        $user_id = $vars['user'];
        $post_id = $vars['post'];
        
        // if we're un-voting or voting up something we previously voted down, we should remove the previous vote:
        $sql = "DELETE FROM " . TABLE_USERACTIVITY . " WHERE useract_userid = %d AND useract_key = %s AND useract_value = %s AND useract_key2 = %s AND useract_value2 = %d";
        $result = $h->db->query($h->db->prepare($sql, $user_id, 'vote', 'up', 'post', $post_id));
        
        // if there wasn't a previous vote, i.e. nothing was found when we tried to delete it, then we can add it as a down vote:
        if (!$result) {
            $sql = "INSERT INTO " . TABLE_USERACTIVITY . " SET useract_archived = %s, useract_userid = %d, useract_status = %s, useract_key = %s, useract_value = %s, useract_key2 = %s, useract_value2 = %s, useract_date = CURRENT_TIMESTAMP, useract_updateby = %d";
            $h->db->query($h->db->prepare($sql, 'N', $user_id, 'show', 'vote', 'down', 'post', $post_id, $h->currentUser->id));
        } else {
            $h->clearCache('html_cache', false); // clear the html cache in order to update the activity widget after the deletion
        }
    }
    
    
    /**
     * Add activity when flagging a post
     */
    public function vote_flag_insert($h)
    {
        // we don't need the status because if the post wasn't visible, it couldn't be voted for.
        
        $sql = "INSERT INTO " . TABLE_USERACTIVITY . " SET useract_archived = %s, useract_userid = %d, useract_status = %s, useract_key = %s, useract_value = %s, useract_key2 = %s, useract_value2 = %s, useract_date = CURRENT_TIMESTAMP, useract_updateby = %d";
        $h->db->query($h->db->prepare($sql, 'N', $h->currentUser->id, 'show', 'vote', 'flag', 'post', $h->post->id, $h->currentUser->id));
    }
    
    
    /**
     * Display the latest activity in a widget block
     */
    public function widget_activity($h)
    {
        // Get settings from database if they exist...
        $activity_settings = $h->getSerializedSettings('activity');
        
        $activity = $this->getLatestActivity($h, $activity_settings['widget_number']);
        
        // build link that will link the widget title to all activity...
        
        $anchor_title = htmlentities($h->lang["activity_title_anchor_title"], ENT_QUOTES, 'UTF-8');
        $title = "<a href='" . $h->url(array('page'=>'activity')) . "' title='" . $anchor_title . "'>";
        $title .= $h->lang['activity_title'] . "</a>";
        
        if (isset($activity) && !empty($activity)) {
            
            $output = "<h2 class='widget_head activity_widget_title'>\n";
            $link = BASEURL;
            $output .= $title;
            $output .= "<a href='" . $h->url(array('page'=>'rss_activity')) . "' title='" . $anchor_title . "'>\n";
            $output .= "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png' alt='RSS' />\n</a>"; // RSS icon
            $output .= "</h2>\n"; 
                
            $output .= "<ul class='widget_body activity_widget_items'>\n";
            
            $output .= $this->getWidgetActivityItems($h, $activity, $activity_settings);
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
    public function getLatestActivity($h, $limit = 0, $userid = 0)
    {
        if (!$limit) { $limit = ""; } else { $limit = "LIMIT " . $limit; }
        
        if (!$userid) {
            $sql = "SELECT * FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s ORDER BY useract_date DESC " . $limit;
            $activity = $h->db->get_results($h->db->prepare($sql, 'show'));
        } else {
            $sql = "SELECT * FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s AND useract_userid = %d ORDER BY useract_date DESC " . $limit;
            $activity = $h->db->get_results($h->db->prepare($sql, 'show', $userid));
        }
        
        if ($activity) { return $activity; } else { return false; }
    }
    
    
    /**
     * Get activity count
     *
     * @param int $limit
     * @param int $userid
     * @param string $return 'activity', 'query' or 'count'
     * return array $activity
     */
    public function getLatestActivityCount($h, $userid = 0)
    {
        if (!$userid) {
            $sql = "SELECT count(useract_id) AS number FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s ORDER BY useract_date DESC ";
            $activity = $h->db->get_var($h->db->prepare($sql, 'show'));
        } else {
            $sql = "SELECT count(useract_id) AS number FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s AND useract_userid = %d ORDER BY useract_date DESC ";
            $activity = $h->db->get_var($h->db->prepare($sql, 'show', $userid));
        }
        
        if ($activity) { return $activity; } else { return false; }
    }
    
    
    /**
     * Get activity
     *
     * return array $activity
     */
    public function getLatestActivityQuery($h, $userid = 0)
    {
        if (!$userid) {
            $sql = "SELECT * FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s ORDER BY useract_date DESC ";
            $query = $h->db->prepare($sql, 'show');
            return $query;
        } else {
            $sql = "SELECT * FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s AND useract_userid = %d ORDER BY useract_date DESC ";
            $query = $h->db->prepare($sql, 'show', $userid);
            return $query;
        }
    }
    
    
    /**
     * Get sidebar activity items
     *
     * @param array $activity 
     * @param array $activity_settings
     * return string $output
     */
    public function getWidgetActivityItems($h, $activity = array(), $cache = true)
    {
        $need_cache = false;
        $label = 'sb_act';
        
        if ($cache) {
            // check for a cached version and use it if no recent update:
            $output = $h->smartCache('html', 'useractivity', 10, '', $label);
            if ($output) {
                return $output;
            } else {
                $need_cache = true;
            }
        }
                
        if (!$activity) { return false; }
        
        $output = $this->getActivityItems($h, $activity);
        
        if ($need_cache) {
            $h->smartCache('html', 'useractivity', 10, $output, $label); // make or rewrite the cache file
        }
        
        return $output;
    }
    
    
    /**
     * Get activity items
     *
     * @param array $activity 
     * @param array $activity_settings
     * return string $output
     */
    public function getActivityItems($h, $activity = array())
    {
        $output = '';
        
        // Get settings from database if they exist... (should be in cache by now)
        $activity_settings = $h->getSerializedSettings('activity');
        
        if (!isset($user)) { $user = new UserBase(); }
        
        foreach ($activity as $item)
        {
            // Post used in Hotaru's url function
            if ($item->useract_key == 'post') {
                $h->readPost($item->useract_value);
            } elseif  ($item->useract_key2 == 'post') {
                $h->readPost($item->useract_value2);
            }
            
            // Hide activity if its post has been buried or set to pending:
            if ($h->post->status == 'pending' || $h->post->status == 'buried') { continue; }
                       
            // get user details
            $user->getUserBasic($h, $item->useract_userid);
            
            $h->post->vars['catSafeName'] =  $h->getCatSafeName($h->post->category);

            // OUTPUT ITEM
            $output .= "<li class='activity_widget_item'>\n";
            
            if($h->isActive('avatar') && $activity_settings['widget_avatar']) {
                $h->setAvatar($user->id, $activity_settings['widget_avatar_size']);
                $output .= "<div class='activity_widget_avatar'>\n";
                $output .= $h->linkAvatar();
                $output .= "</div> \n";
            }
            
            if ($activity_settings['widget_user']) {
                $output .= "<a class='activity_widget_user' href='" . $h->url(array('user' => $user->name)) . "'>" . $user->name . "</a> \n";
            }
            
            $output .= "<div class='activity_widget_content'>\n";
            
            $post_title = stripslashes(html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8'));
            $title_link = $h->url(array('page'=>$h->post->id));
            $cid = ''; // comment id string
            
            switch ($item->useract_key) {
                case 'comment':
                    $output .= $h->lang["activity_commented"] . " ";
                    $cid = "#c" . $item->useract_value; // comment id to be put on the end of the url
                    break;
                case 'post':
                	$post_lang = "activity_submitted_" . $h->post->type; // e.g. news, blog, etc.
                	$output .= $h->lang[$post_lang] . " ";
                    break;
                case 'vote':
                    switch ($item->useract_value) {
                        case 'up':
                            $output .= $h->lang["activity_voted_up"] . " ";
                            break;
                        case 'down':
                            $output .= $h->lang["activity_voted_down"] . " ";
                            break;
                        case 'flag':
                            $output .= $h->lang["activity_voted_flagged"] . " ";
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;
            }
            
            $output .= "&quot;<a href='" . $title_link . $cid . "' >" . $post_title . "</a>&quot; \n";
            
            if ($activity_settings['time']) { 
                // Commented this out because "8 mins ago" will never change when cached!
                //$output .= "<small>[" . time_difference(unixtimestamp($item->useract_date), $h->lang);
                //$output .= " " . $h->lang["submit_post_ago"] . "]</small>";
                $output .= "<small>[" . date('g:ia, M jS', strtotime($item->useract_date)) . "]</small>";
            }
            
            $output .= "</div>\n";
            $output .= "</li>\n\n";
        }
        
        return $output;
    }


    /**
     * Check if the post this action applies to can be shown
     *
     * @param array $activity 
     * return bool
     */
    public function postSafe($h, $item = array())
    {
        // Post used in Hotaru's url function
        if ($item->useract_key == 'post') {
            $h->readPost($item->useract_value);
        } elseif  ($item->useract_key2 == 'post') {
            $h->readPost($item->useract_value2);
        }
        
        // return status
        if ($h->post->status == 'buried' || $h->post->status == 'pending') { 
            return false;
        } else {
            return true;
        }
    }
    

    /**
     * Get activity content (Profile and Activity Pages only)
     *
     * @param array $activity 
     * return string $output
     */
    public function activityContent($h, $item = array())
    {
        if (!$item) { return false; }
        
        $output = '';
        
        // Post used in Hotaru's url function
        if ($item->useract_key == 'post') {
            $h->readPost($item->useract_value);
        } elseif  ($item->useract_key2 == 'post') {
            $h->readPost($item->useract_value2);
        }
        
        $h->post->vars['catSafeName'] =  $h->getCatSafeName($h->post->category);
       
        // content
        $post_title = stripslashes(html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8'));
        $title_link = $h->url(array('page'=>$h->post->id));
        $cid = ''; // comment id string
        
        switch ($item->useract_key) {
            case 'comment':
                $output .= $h->lang["activity_commented"] . " ";
                $cid = "#c" . $item->useract_value; // comment id to be put on the end of the url
                break;
            case 'post':
                $post_lang = "activity_submitted_" . $h->post->type; // e.g. news, blog, etc.
                $output .= $h->lang[$post_lang] . " ";
                break;
            case 'vote':
                switch ($item->useract_value) {
                    case 'up':
                        $output .= $h->lang["activity_voted_up"] . " ";
                        break;
                    case 'down':
                        $output .= $h->lang["activity_voted_down"] . " ";
                        break;
                    case 'flag':
                        $output .= $h->lang["activity_voted_flagged"] . " ";
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
        
        $output .= "&quot;<a href='" . $title_link . $cid . "' >" . $post_title . "</a>&quot; \n";
        
        return $output;
    }
    
    
    /**
     * Redirect to Activity RSS
     *
     * @return bool
     */
    public function theme_index_top($h)
    {
        if (!$h->isPage('rss_activity')) { return false; }
        $this->rssFeed($h);
        return true;
    }
    
    
    /**
     * Display All Activity page
     */
    public function theme_index_main($h)
    {
        if ($h->pageName != 'activity') { return false; }
        
        $this->activityPage($h);
        
        return true;
    }
    
    
    /**
     * Display All Activity page
     */
    public function activityPage($h)
    {
        // Get settings from database if they exist...
        $activity_settings = $h->getSerializedSettings('activity');
        
        // gets query and total count for pagination
        $act_query = $this->getLatestActivityQuery($h);
        $act_count = $this->getLatestActivityCount($h);
        
        // pagination 
        $h->vars['pagedResults'] = $h->pagination($act_query, $act_count, $activity_settings['number'], 'useractivity');
        
        $h->displayTemplate('activity');

    }
    
    
    /**
     * Display activity on Profile page
     */
    public function profile($h)
    {
        $user = $h->cage->get->testUsername('user');
        $userid = $h->getUserIdFromName($user);
        $h->vars['user_name'] = $user;
                
        // Get settings from database if they exist...
        $activity_settings = $h->getSerializedSettings('activity');

        // gets query and total count for pagination
        $act_query = $this->getLatestActivityQuery($h, $userid);
        $act_count = $this->getLatestActivityCount($h, $userid);
        
        // pagination 
        $h->vars['pagedResults'] = $h->pagination($act_query, $act_count, $activity_settings['number'], 'useractivity');
        
        $h->displayTemplate('activity_profile');
    }
    
    
    /**
     * Add Activity RSS link to breadcrumbs
     */
    public function breadcrumbs($h)
    {
        if ($h->pageName != 'activity') { return false; }
        
        $crumbs = $h->pageTitle;
        $crumbs .= "<a href='" . $h->url(array('page'=>'rss_activity')) . "'>";
        $crumbs .= " <img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png' alt='" . $h->pageTitle . " RSS' /></a>\n";
        
        return $crumbs;
    }
    
    
    /**
     * Publish content as an RSS feed
     * Uses the 3rd party RSS Writer class.
     */    
    public function rssFeed($h)
    {
        require_once(EXTENSIONS . 'RSSWriterClass/rsswriter.php');
        
        $select = '*';

        $limit = $h->cage->get->getInt('limit');
        $user = $h->cage->get->testUsername('user');
        
        if ($user) { 
            $userid = $h->getUserIdFromName($user);
        } else {
            $userid = 0;
        }
        
        $h->pluginHook('activity_rss_feed');
        
        $feed           = new RSS();
        $feed->title    = SITE_NAME;
        $feed->link     = BASEURL;
        
        if ($user) { 
            $feed->description = $h->lang["activity_rss_latest_from_user"] . " " . $user; 
        } else {
            $feed->description = $h->lang["activity_rss_latest"] . SITE_NAME;
        }
        
        $user = new UserBase($this->hotaru);

        // Get settings from database if they exist...
        $activity_settings = $h->getSerializedSettings('activity');
        
        if (!$limit) { $limit = $activity_settings['rss_number']; }
        
        // get latest activity
        $activity = $this->getLatestActivity($h, $limit, $userid);
        
        if (!$activity) { echo $feed->serve(); return false; } // displays empty RSS feed
                
        foreach ($activity as $act) 
        {
            // Post used in Hotaru's url function
            if ($act->useract_key == 'post') {
                $h->readPost($act->useract_value);
            } elseif  ($act->useract_key2 == 'post') {
                $h->readPost($act->useract_value2);
            }
            
            $user->getUserBasic($h, $act->useract_userid);
            
            $name = $user->name;
            $post_title = stripslashes(html_entity_decode(urldecode($h->post->title), ENT_QUOTES,'UTF-8'));
            $title_link = $h->url(array('page'=>$h->post->id));
            $cid = ''; // comment id string
            
            switch ($act->useract_key) {
                case 'comment':
                    $action = $h->lang["activity_commented"] . " ";
                    $cid = "#c" . $act->useract_value; // comment id to be put on the end of the url
                    break;
                case 'post':
                	$post_lang = "activity_submitted_" . $h->post->type; // e.g. news, blog, etc.
                    $action = $h->lang[$post_lang] . " ";
                    break;
                case 'vote':
                    switch ($act->useract_value) {
                        case 'up':
                            $action = $h->lang["activity_voted_up"] . " ";
                            break;
                        case 'down':
                            $action = $h->lang["activity_voted_down"] . " ";
                            break;
                        case 'flag':
                            $action = $h->lang["activity_voted_flagged"] . " ";
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;
            }
            
            $item = new RSSItem();

            $item->title = $name . " " . $action . " \"" . $post_title . "\"";
            $item->link  = $h->url(array('page'=>$h->post->id)) . $cid;
            $item->setPubDate($act->useract_date); 
            $feed->addItem($item);
        }
        
        echo $feed->serve();
    }
}
?>
