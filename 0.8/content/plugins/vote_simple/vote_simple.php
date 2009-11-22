<?php
/**
 * name: Vote Simple
 * description: Adds voting ability to posted stories.
 * version: 0.9
 * folder: vote_simple
 * class: VoteSimple
 * requires: submit 1.4, users 0.8
 * hooks: install_plugin, hotaru_header, submit_hotaru_header_1, post_read_post_1, post_read_post_2, header_include, submit_pre_show_post, submit_show_post_title, admin_plugin_settings, admin_sidebar_plugin_settings, post_add_post, navigation, submit_show_post_extra_fields, submit_show_post_extras, post_delete_post, submit_post_breadcrumbs
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

class VoteSimple extends PluginFunctions
{
    /**
     * Add vote fields to the post table and make a dedicated Votes table.
     */
    public function install_plugin()
    {
        // Add the postvotes table to the database;
        require_once(PLUGINS . 'vote_simple/libs/Vote.php');
        $vote = new Vote($this->db);  // adds Vote object to Hotaru class
        $vote->databaseVoteTable();

        // Default settings
        $vote_settings = $this->getSerializedSettings();
        if (!isset($vote_settings['vote_submit_vote'])) { $vote_settings['vote_submit_vote'] = "checked"; }
        if (!isset($vote_settings['vote_submit_vote_value'])) { $vote_settings['vote_submit_vote_value'] = 1; }
        if (!isset($vote_settings['vote_votes_to_promote'])) { $vote_settings['vote_votes_to_promote'] = 5; }
        if (!isset($vote_settings['vote_use_demote'])) { $vote_settings['vote_use_demote'] = ""; }
        if (!isset($vote_settings['vote_use_alerts'])) { $vote_settings['vote_use_alerts'] = "checked"; }
        if (!isset($vote_settings['vote_alerts_to_bury'])) { $vote_settings['vote_alerts_to_bury'] = 5; }
        if (!isset($vote_settings['vote_physical_delete'])) { $vote_settings['vote_physical_delete'] = ""; }
        if (!isset($vote_settings['vote_upcoming_duration'])) { $vote_settings['vote_upcoming_duration'] = 5; }
        if (!isset($vote_settings['vote_no_front_page'])) { $vote_settings['vote_no_front_page'] = 5; }
        if (!isset($vote_settings['vote_sidebar_posts'])) { $vote_settings['vote_sidebar_posts'] = 'checked'; }
        
        $this->updateSetting('vote_settings', serialize($vote_settings));
        
        // Include language file. Also included in hotaru_header, but needed here so 
        // that the link in the Admin sidebar shows immediately after installation.
        $this->includeLanguage();
       
    }  
    
    
    /**
     * Set things up when the page is first loaded
     */
    public function hotaru_header()
    {
        require_once(PLUGINS . 'vote_simple/libs/Vote.php');
        $this->hotaru->vote = new Vote($this->hotaru);  // adds Vote object to Hotaru class
        
        if (!defined('TABLE_POSTVOTES')) { define("TABLE_POSTVOTES", DB_PREFIX . 'postvotes'); }
        
        $this->includeLanguage();
    }
    

    /**
     * Adds additional member variables when the $post object is read in the Submit plugin
     */
    public function submit_hotaru_header_1()
    {
        $this->hotaru->vars['useAlerts'] = 'checked';    
    }
    
    /**
     * Read vote settings
     */
    public function post_read_post_1()
    {
        // Get settings from the database if they exist...
        $vote_settings = unserialize($this->getSetting('vote_settings')); 

        // Use alerts?
        $this->hotaru->vars['vote_use_alerts'] = $vote_settings['vote_use_alerts'];
        
        // Prevent Hotaru from merging "top" and "new" posts on the same page:
        $this->hotaru->post->useLatest = true; 
    }
    
    
    /**
     * Read number of votes if post exists.
     */
    public function post_read_post_2()
    {
        $post_row = $this->hotaru->post->vars['post_row'];
        $this->hotaru->vars['votesUp'] = $post_row->post_votes_up;
    }
    
    
    /**
     * Includes css and javascript for the vote buttons.
     */
    public function header_include()
    {
        $this->includeCss();
        $this->includeJs();
        $this->includeJs('json2.min');
    }
    
    
     /**
     * ********************************************************************* 
     * *********************** FUNCTIONS FOR VOTING ************************ 
     * *********************************************************************
     * ****************************************************************** */
     
     
    /**
     * If auto-vote is enabled, the new post is automatically voted for by the person who submitted it.
     */
    public function post_add_post()
    {
         //get vote settings
        $vote_settings = unserialize($this->getSetting('vote_settings')); 
        $submit_vote = $vote_settings['vote_submit_vote'];
        $submit_vote_value = $vote_settings['vote_submit_vote_value'];
        
        // Automatically vote for a post when it's submitted...
        if ($submit_vote == 'checked') {
            
            //update the vote count
            $sql = "UPDATE " . TABLE_POSTS . " SET post_votes_up=post_votes_up+%d WHERE post_id = %d";
            $this->db->query($this->db->prepare($sql, $submit_vote_value, $this->hotaru->post->id));
        
            //Insert one vote for each of $submit_vote_value;
            for ($i=0; $i<$submit_vote_value; $i++) {
                $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d)";
                $this->db->query($this->db->prepare($sql, $this->hotaru->post->id, $this->current_user->id, $this->cage->server->testIp('REMOTE_ADDR'), 'vote_simple', 'positive', $this->current_user->id));
            }
        }            
                    
    }
     
    
     /**
     * ********************************************************************* 
     * ******************* FUNCTIONS FOR SHOWING VOTES ********************* 
     * *********************************************************************
     * ****************************************************************** */
     
    
    /**
     * Adds "Top Posts" and "Latest" links to the navigation bar
     *
     * Notes: If you can automatically vote for a story you submit, and that value is equal to or greater than the number of votes you need to get on the Top Stories page, then there's no need for a "Latest" page at all. In that case, we don't add anything to the navigation bar because the "Home" link will show all the stories. HOWEVER, any old posts with "new" status instead of "top" status will become inaccessible.
     */
    public function navigation()
    {    
       //get vote settings
        $vote_settings = unserialize($this->getSetting('vote_settings')); 
        
        if (($vote_settings['vote_submit_vote'] == "checked") && ($vote_settings['vote_submit_vote_value'] >= $vote_settings['vote_votes_to_promote'])) {
            // these settings make the latest page unnecessary so the "Home" link is sufficient, otherwise...
        } else {
        
            if ($this->hotaru->title == 'latest') { $status = "id='navigation_active'"; } else { $status = ""; }
            echo "<li><a " . $status . " href='" . $this->hotaru->url(array('page'=>'latest')) . "'>" . $this->lang["vote_navigation_latest"] . "</a></li>\n";
        }
    }
    
    
     /**
     * Displays the vote button.
     */
    public function submit_pre_show_post()
    {
        // CHECK TO SEE IF THE CURRENT USER HAS VOTED FOR THIS POST
         if ($this->current_user->loggedIn) {
            $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating != %s";
            $this->hotaru->vars['voted'] = $this->db->get_var($this->db->prepare($sql, $this->hotaru->post->id, $this->current_user->id, 'alert'));
        } 

         $this->hotaru->displayTemplate('vote_simple_button', 'vote_simple', NULL, false);
    }
    
    
     /**
     * Displays the vote button.
     */
    public function submit_show_post_title()
    {
        if ($this->hotaru->post->status == 'new' && $this->hotaru->vars['useAlerts'] == "checked") {
            // CHECK TO SEE IF THIS POST IS BEING FLAGGED AND IF SO, ADD IT TO THE DATABASE
            if ($this->cage->get->keyExists('alert') && $this->current_user->loggedIn) {
                // Check if already flagged...
                $sql = "SELECT vote_rating FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_rating = %s";
                $flagged = $this->db->get_var($this->db->prepare($sql, $this->hotaru->post->id, $this->current_user->id, 'alert'));
                if (!$flagged) {
                    $sql = "INSERT INTO " . TABLE_POSTVOTES . " (vote_post_id, vote_user_id, vote_user_ip, vote_date, vote_type, vote_rating, vote_reason, vote_updateby) VALUES (%d, %d, %s, CURRENT_TIMESTAMP, %s, %s, %d, %d)";
                    $this->db->query($this->db->prepare($sql, $this->hotaru->post->id, $this->current_user->id, $this->cage->server->testIp('REMOTE_ADDR'), 'vote_simple', 'alert', $this->cage->get->testInt('alert'), $this->current_user->id));
                    
                    $this->pluginHook('vote_flag_insert');
                }
                else
                {
                    $this->hotaru->message = $this->lang["vote_alert_already_flagged"];
                    $this->hotaru->messageType = "red";
                    $this->hotaru->showMessage();
                }
            }
            
            // CHECK TO SEE IF THIS POST HAS BEEN FLAGGED AND IF SO, SHOW THE ALERT STATUS
        
            // Get settings from the database if they exist...
            $vote_settings = unserialize($this->getSetting('vote_settings')); 
            
            // Check if already flagged...
            $sql = "SELECT * FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d AND vote_rating = %s";
            $flagged = $this->db->get_results($this->db->prepare($sql, $this->hotaru->post->id, 'alert'));
            if ($flagged) {
                $flag_count = 0;
                $reasons = array();
                foreach ($flagged as $flag) {
                    array_push($reasons, $flag->vote_reason);
                    $flag_count++;
                }
                
                // Buries or Deletes a post if this new flag sends it over the limit set in Vote Settings
                if ($this->cage->get->keyExists('alert') && $flag_count >= $vote_settings['vote_alerts_to_bury'])
                {
                    $this->hotaru->post->readPost($this->hotaru->post->id); //make sure we've got all post details
                    
                    if ($vote_settings['vote_physical_delete']) { 
                        $this->hotaru->post->deletePost(); // Akismet uses those details to report the post as spam
                    } else {
                        $this->hotaru->post->changeStatus('buried');
                        $this->pluginHook('vote_post_status_buried'); // Akismet hooks in here to report the post as spam
                    }
                    
                    $this->hotaru->message = $this->lang["vote_alert_post_buried"];
                    $this->hotaru->messageType = "red";
                    $this->hotaru->showMessage();
                    return true; // This will stop the post from showing    
                }
                
                $why_list = "";
                foreach ($reasons as $why) {
                    $alert_lang = "vote_alert_reason_" . $why;
                    $why_list .= $this->lang[$alert_lang] . ", ";
                }
                $why_list = rstrtrim($why_list, ", ");    // removes trailing comma

                $this->hotaru->vars['flag_count'] = $flag_count;
                $this->hotaru->vars['flag_why'] = $why_list;
                $this->hotaru->displayTemplate('vote_simple_alert', 'vote_simple', NULL, false);
            }
        }
    }
    
     /**
     * Add an "alert" link below the story
     */
    public function submit_show_post_extra_fields()
    {
        // Only show the Alert link ("Flag it") on new posts, not top stories
        if ($this->current_user->loggedIn && $this->hotaru->post->status == "new" && ($this->hotaru->vars['useAlerts'] == "checked")) {
            echo "<li><a class='alert_link' href='#'>" . $this->lang["vote_alert"]  . "</a></li>";
        }
    }
    
    
     /**
     * List of alert reasons to choose from.
     */
    public function submit_show_post_extras()
    {
        if ($this->hotaru->post->status == "new" && ($this->hotaru->vars['useAlerts'] == "checked")) {
            echo "<div class='alert_choices' style='display: none;'>";
                echo $this->lang["vote_alert_reason_title"] . "<br />";
                echo "<ul>";
                echo "<li><a href='" . $this->hotaru->url(array('page'=>$this->hotaru->post->id, 'alert'=>1)) . "'>" . $this->lang["vote_alert_reason_1"]  . "</a></li>\n";
                echo "<li><a href='" . $this->hotaru->url(array('page'=>$this->hotaru->post->id, 'alert'=>2)) . "'>" . $this->lang["vote_alert_reason_2"]  . "</a></li>\n";
                echo "<li><a href='" . $this->hotaru->url(array('page'=>$this->hotaru->post->id, 'alert'=>3)) . "'>" . $this->lang["vote_alert_reason_3"]  . "</a></li>\n";
                echo "<li><a href='" . $this->hotaru->url(array('page'=>$this->hotaru->post->id, 'alert'=>4)) . "'>" . $this->lang["vote_alert_reason_4"]  . "</a></li>\n";
                echo "<li><a href='" . $this->hotaru->url(array('page'=>$this->hotaru->post->id, 'alert'=>5)) . "'>" . $this->lang["vote_alert_reason_5"]  . "</a></li>\n";
                echo "<li><a href='" . $this->hotaru->url(array('page'=>$this->hotaru->post->id, 'alert'=>6)) . "'>" . $this->lang["vote_alert_reason_6"]  . "</a></li>\n";
                echo "</ul>";
            echo "</div>";
        }
    }
    
    
    /**
     * Delete votes when post deleted
     */
    public function post_delete_post()
    {
        $sql = "DELETE FROM " . TABLE_POSTVOTES . " WHERE vote_post_id = %d";
        $this->db->query($this->db->prepare($sql, $this->hotaru->post->id));
    }
    
    
    /** 
     * Add sorting options
     */
    public function submit_post_breadcrumbs()
    {
        if ($this->hotaru->isPage('submit2')) { return false; } // don't show sorting on Submit Confirm
        
        // exit if this isn't a page of type list, user or profile
        $page_type = $this->hotaru->pageType;
        if ($page_type != 'list' && $page_type != 'user' && $page_type != 'profile') { return false; }
        
        // go set up the links
        $this->setUpSortLinks();
        
        // display the sort links
        $this->hotaru->displayTemplate('vote_simple_sorting', 'vote_simple');
    }
    
    
    /** 
     * Prepare sort links
     */
    public function setUpSortLinks()
    {
        // check if we're looking at a category
        if ($this->hotaru->cage->get->keyExists('category')) { 
            $category = $this->hotaru->cage->get->noTags('category');
            if (!is_numeric($category)) { 
                require_once(PLUGINS . 'categories/libs/Category.php');
                $cat = new Category($this->db);
                $category = $cat->getCatId($category);
            }
        } 
        
        // check if we're looking at a tag
        if ($this->hotaru->cage->get->keyExists('tag')) { 
            $tag = $this->hotaru->cage->get->noTags('tag');
        } 
        
        // check if we're looking at a user
        if ($this->hotaru->cage->get->keyExists('user')) { 
            $user = $this->hotaru->cage->get->testUsername('user');
        } 
        
        // check if we're looking at a sorted page
        if ($this->hotaru->cage->get->keyExists('sort')) { 
            $sort = $this->hotaru->cage->get->testAlnumLines('sort');
        } 
        
        $pagename = $this->hotaru->getPageName();
        
        // POPULAR LINK
        if ($category) { $url = $this->hotaru->url(array('category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('page'=>'top', 'user'=>$user));
         } else { $url = $this->hotaru->url(array()); } 
        $this->hotaru->vars['popular_link'] = $url;
         
        // POPULAR ACTIVE OR INACTIVE
        if (($pagename == 'main' || $pagename == 'top') && !$sort && $this->hotaru->pageType != 'profile') { 
            $this->hotaru->vars['popular_active'] = "class='active'";
        } else { $this->hotaru->vars['popular_active'] = ""; }
        
        // UPCOMING LINK
        if ($category) { $url = $this->hotaru->url(array('page'=>'upcoming', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('page'=>'upcoming', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('page'=>'upcoming', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('page'=>'upcoming')); }
        $this->hotaru->vars['upcoming_link'] = $url;
        
        // UPCOMING ACTIVE OR INACTIVE
        if ($pagename == 'upcoming' && !$sort) { 
            $this->hotaru->vars['upcoming_active'] = "class='active'";
        } else { $this->hotaru->vars['upcoming_active'] = ""; }
        
        // LATEST LINK
        if ($category) { $url = $this->hotaru->url(array('page'=>'latest', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('page'=>'latest', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('page'=>'latest', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('page'=>'latest')); }
        $this->hotaru->vars['latest_link'] = $url;

        // LATEST ACTIVE OR INACTIVE
        if ($pagename == 'latest' && !$sort) { 
            $this->hotaru->vars['latest_active'] = "class='active'";
        } else { $this->hotaru->vars['latest_active'] = ""; }
        
        // ALL LINK
        if ($category) { $url = $this->hotaru->url(array('page'=>'all', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('page'=>'all', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('page'=>'all', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('page'=>'all')); }
        $this->hotaru->vars['all_link'] = $url;

        // ALL ACTIVE OR INACTIVE
        if ($pagename == 'all' && !$sort) { 
            $this->hotaru->vars['all_active'] = "class='active'";
        } else { $this->hotaru->vars['all_active'] = ""; }
        
        // 24 HOURS LINK
        if ($category) { $url = $this->hotaru->url(array('sort'=>'top-24-hours', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('sort'=>'top-24-hours', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('sort'=>'top-24-hours', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('sort'=>'top-24-hours')); }
        $this->hotaru->vars['24_hours_link'] = $url;

        // 24 HOURS ACTIVE OR INACTIVE
        if ($sort == 'top-24-hours') { 
            $this->hotaru->vars['top_24_hours_active'] = "class='active'";
        } else { $this->hotaru->vars['top_24_hours_active'] = ""; }
        
        // 48 HOURS LINK
        if ($category) { $url = $this->hotaru->url(array('sort'=>'top-48-hours', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('sort'=>'top-48-hours', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('sort'=>'top-48-hours', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('sort'=>'top-48-hours')); }
        $this->hotaru->vars['48_hours_link'] = $url;

        // 48 HOURS ACTIVE OR INACTIVE
        if ($sort == 'top-48-hours') { 
            $this->hotaru->vars['top_48_hours_active'] = "class='active'";
        } else { $this->hotaru->vars['top_48_hours_active'] = ""; }
        
        // 7 DAYS LINK
        if ($category) { $url = $this->hotaru->url(array('sort'=>'top-7-days', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('sort'=>'top-7-days', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('sort'=>'top-7-days', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('sort'=>'top-7-days')); }
        $this->hotaru->vars['7_days_link'] = $url;

        // 7 DAYS ACTIVE OR INACTIVE
        if ($sort == 'top-7-days') { 
            $this->hotaru->vars['top_7_days_active'] = "class='active'";
        } else { $this->hotaru->vars['top_7_days_active'] = ""; }
        
        // 30 DAYS LINK
        if ($category) { $url = $this->hotaru->url(array('sort'=>'top-30-days', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('sort'=>'top-30-days', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('sort'=>'top-30-days', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('sort'=>'top-30-days')); }
        $this->hotaru->vars['30_days_link'] = $url;

        // 30 DAYS ACTIVE OR INACTIVE
        if ($sort == 'top-30-days') { 
            $this->hotaru->vars['top_30_days_active'] = "class='active'";
        } else { $this->hotaru->vars['top_30_days_active'] = ""; }
        
        // 365 DAYS LINK
        if ($category) { $url = $this->hotaru->url(array('sort'=>'top-365-days', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('sort'=>'top-365-days', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('sort'=>'top-365-days', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('sort'=>'top-365-days')); }
        $this->hotaru->vars['365_days_link'] = $url;

        // 365 DAYS ACTIVE OR INACTIVE
        if ($sort == 'top-365-days') { 
            $this->hotaru->vars['top_365_days_active'] = "class='active'";
        } else { $this->hotaru->vars['top_365_days_active'] = ""; }
        
        // ALL TIME LINK
        if ($category) { $url = $this->hotaru->url(array('sort'=>'top-all-time', 'category'=>$category));
         } elseif ($tag) { $url = $this->hotaru->url(array('sort'=>'top-all-time', 'tag'=>$tag));
         } elseif ($user) { $url = $this->hotaru->url(array('sort'=>'top-all-time', 'user'=>$user));
         } else { $url = $this->hotaru->url(array('sort'=>'top-all-time')); }
        $this->hotaru->vars['all_time_link'] = $url;
        
        // ALL TIME ACTIVE OR INACTIVE
        if ($sort == 'top-all-time') { 
            $this->hotaru->vars['top_all_time_active'] = "class='active'";
        } else { $this->hotaru->vars['top_all_time_active'] = ""; }
        
    }
}

?>