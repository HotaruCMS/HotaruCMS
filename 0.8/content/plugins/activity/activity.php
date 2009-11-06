<?php
/**
 * name: Activity
 * description: Show recent activity
 * version: 0.1
 * folder: activity
 * class: Activity
 * requires: users 0.8
 * hooks: install_plugin, hotaru_header, header_include, comment_post_add_comment, comment_update_comment, com_man_approve_all_comments, comment_delete_comment, post_add_post, post_update_post, post_change_status, post_delete_post, userbase_killspam, vote_positive_vote, vote_negative_vote, vote_flag_insert, admin_sidebar_plugin_settings, admin_plugin_settings, theme_index_replace, theme_index_main, profile
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
        if (!isset($activity_settings['activity_number'])) { $activity_settings['activity_number'] = 20; }
        if (!isset($activity_settings['activity_time'])) { $activity_settings['activity_time'] = "checked"; }
        
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
        
        $activity = $this->getLatestActivity($activity_settings, $activity_settings['activity_sidebar_number']);
        
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
            
            $output .= $this->getSidebarActivityItems($activity, $activity_settings);
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
    public function getLatestActivity($activity_settings, $limit = 0, $userid = 0)
    {
        if (!$limit) { $limit = ""; } else { $limit = "LIMIT " . $limit; }
        
        if (!$userid) {
            $sql = "SELECT * FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s ORDER BY useract_date DESC " . $limit;
            $activity = $this->db->get_results($this->db->prepare($sql, 'show'));
        } else {
            $sql = "SELECT * FROM " . TABLE_USERACTIVITY . " WHERE useract_status = %s AND useract_userid = %d ORDER BY useract_date DESC " . $limit;
            $activity = $this->db->get_results($this->db->prepare($sql, 'show', $userid));
        }
        
        if ($activity) { return $activity; } else { return false; }
    }
    
    
    /**
     * Get sidebar activity items
     *
     * @param array $activity 
     * return string $output
     */
    public function getSidebarActivityItems($activity = array(), $activity_settings)
    {
        if (!isset($cat)) {
            // we need categories for the url
            if ($this->hotaru->post->vars['useCategories']) {
                require_once(PLUGINS . 'categories/libs/Category.php');
                $cat = new Category($this->db);
            }
        }
        
        if (!isset($this->hotaru->post)) { 
            $this->hotaru->post = new Post($this->hotaru); // used to get post information
        }
        
        if (!isset($user)) { $user = new UserBase($this->hotaru); }
                
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
                            break;
                    }
                    break;
                default:
                    break;
            }
            
            // for plugins to add their own activity:
            $this->hotaru->vars['activity_output'] = array($output, $title_link, $cid, $post_title);
            $this->pluginHook('activity_output');
            list($output, $title_link, $cid, $post_title) = $this->hotaru->vars['activity_output'];
            
            $output .= "&quot;<a href='" . $title_link . $cid . "' >" . $post_title . "</a>&quot; \n";
            
            if ($activity_settings['activity_time']) { 
                $output .= "<small>[" . time_difference(unixtimestamp($item->useract_date), $this->hotaru->lang);
                $output .= " " . $this->hotaru->lang["submit_post_ago"] . "]</small>";
            }
            
            $output .= "<div>\n";
            $output .= "</li>\n\n";
        }
        
        return $output;
    }


    /**
     * Redirect to Activity RSS
     *
     * @return bool
     */
    public function theme_index_replace()
    {
        if (!$this->hotaru->isPage('rss_activity')) { return false; }
        $this->rssFeed();
        return true;
    }
    
    
    /**
     * Display All Activity page
     */
    public function theme_index_main($profile = false)
    {
        if ($this->hotaru->isPage('activity') || $profile == true) {
        
            if ($this->hotaru->pageType == 'profile') {
                $user = $this->cage->get->testUsername('user');
                $userid = $this->current_user->getUserIdFromName($user);
            } else {
                $userid = 0;
            }
                
            // Get settings from database if they exist...
            $activity_settings = $this->getSerializedSettings('activity');
            // gets however many are items shown per page on activity pages:
            $activity = $this->getLatestActivity($activity_settings, 0, $userid); // 0 means no limit, ALL activity 
        
            
            if ($this->hotaru->pageType == 'profile') {
                $anchor_title = htmlentities($this->lang["activity_title_anchor_title"], ENT_QUOTES, 'UTF-8');
                echo "<h2>\n";
                echo "<a href='" . $this->hotaru->url(array('page'=>'rss_activity', 'user'=>$user)) . "' title='" . $anchor_title . "'>\n";
                echo "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_16.png'>\n</a>&nbsp;"; // RSS icon
                echo $this->lang['activity_title'] . "</h2>\n"; 
            } else {
                /* BREADCRUMBS */
                echo "<div id='breadcrumbs'>";
                echo "<a href='" . BASEURL . "'>" .  $this->hotaru->lang['main_theme_home'] . "</a> &raquo; ";
                $this->hotaru->plugins->pluginHook('breadcrumbs');
                echo $this->hotaru->lang['activity_all'];
                echo "<a href='" . $this->hotaru->url(array('page'=>'rss_activity')) . "'> ";
                echo "<img src='" . BASEURL . "content/themes/" . THEME . "images/rss_10.png'></a>";
                echo "</div>";
            }
            
            // for pagination:
            require_once(PLUGINS . 'submit/libs/Post.php');
            require_once(EXTENSIONS . 'Paginated/Paginated.php');
            require_once(EXTENSIONS . 'Paginated/DoubleBarLayout.php');
            
            $pg = $this->hotaru->cage->get->getInt('pg');
            $pagedResults = new Paginated($activity, $activity_settings['activity_number'], $pg);
                        
            $output = "<div id='activity'>";
            $output .= "<ul class='sidebar_widget_body activity_sidebar_items'>\n";
            
            while($action = $pagedResults->fetchPagedRow()) {
                $output .= $this->getSidebarActivityItems(array($action), $activity_settings);
            }
            
            $output .= "</ul>\n\n";
            $output .= "</div>";
            
            echo $output;
            
            $pagedResults->setLayout(new DoubleBarLayout());
            echo $pagedResults->fetchPagedNavigation('', $this->hotaru);
        }
    }
    
    
    /**
     * Show activity on a user's profile
     */    
    public function profile()
    {
        $this->theme_index_main(true);
    }
    
    
    /**
     * Publish content as an RSS feed
     * Uses the 3rd party RSS Writer class.
     */    
    public function rssFeed()
    {
        require_once(EXTENSIONS . 'RSSWriterClass/rsswriter.php');
        
        $select = '*';

        $limit = $this->cage->get->getInt('limit');
        $user = $this->cage->get->testUsername('user');

        if (!$limit) { $limit = 10; }
        
        if ($user) { 
            $userid = $this->current_user->getUserIdFromName($user);
        } else {
            $userid = 0;
        }
        
        $this->pluginHook('activity_rss_feed');
        
        $feed           = new RSS();
        $feed->title    = SITE_NAME;
        $feed->link     = BASEURL;
        
        if ($user) { 
            $feed->description = $this->lang["activity_rss_latest_from_user"] . " " . $user; 
        } else {
            $feed->description = $this->lang["activity_rss_latest"] . SITE_NAME;
        }
        
        $this->hotaru->post = new Post($this->hotaru); // used to get post information
        $userBase = new UserBase($this->hotaru);

        // Get settings from database if they exist...
        $activity_settings = $this->getSerializedSettings('activity');
        $activity = $this->getLatestActivity($activity_settings, $activity_settings['activity_sidebar_number'], $userid);
        
        if (!$activity) { echo $feed->serve(); return false; } // displays empty RSS feed
                
        foreach ($activity as $act) 
        {
            // Post used in Hotaru's url function
            if ($act->useract_key == 'post') {
                $this->hotaru->post->readPost($act->useract_value);
            } elseif  ($act->useract_key2 == 'post') {
                $this->hotaru->post->readPost($act->useract_value2);
            }
            
            $userBase->getUserBasic($act->useract_userid);
            
            $name = $userBase->name;
            $post_title = stripslashes(html_entity_decode(urldecode($this->hotaru->post->title), ENT_QUOTES,'UTF-8'));
            $title_link = $this->hotaru->url(array('page'=>$this->hotaru->post->id));
            $cid = ''; // comment id string
            
            // make category object
            if (!isset($cat)) {
                // we need categories for the url
                if ($this->hotaru->post->vars['useCategories']) {
                    require_once(PLUGINS . 'categories/libs/Category.php');
                    $cat = new Category($this->db);
                }
            }
            
            // category for url
            if ($this->hotaru->post->vars['useCategories'] && ($this->hotaru->post->vars['category'] != 1)) {
                $this->hotaru->post->vars['category'] = $this->hotaru->post->vars['category'];
                $this->hotaru->post->vars['catSafeName'] =  $cat->getCatSafeName($this->hotaru->post->vars['category']);
            }
            
            switch ($act->useract_key) {
                case 'comment':
                    $action = $this->hotaru->lang["activity_commented"] . " ";
                    $cid = "#c" . $act->useract_value; // comment id to be put on the end of the url
                    break;
                case 'post':
                    $action = $this->hotaru->lang["activity_submitted"] . " ";
                    break;
                case 'vote':
                    switch ($act->useract_value) {
                        case 'up':
                            $action = $this->hotaru->lang["activity_voted_up"] . " ";
                            break;
                        case 'down':
                            $action = $this->hotaru->lang["activity_voted_down"] . " ";
                            break;
                        case 'flag':
                            $action = $this->hotaru->lang["activity_voted_flagged"] . " ";
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
            $item->link  = $this->hotaru->url(array('page'=>$this->hotaru->post->id)) . $cid;
            $item->setPubDate($act->useract_date); 
            $feed->addItem($item);
        }
        
        echo $feed->serve();
    }
}
?>
