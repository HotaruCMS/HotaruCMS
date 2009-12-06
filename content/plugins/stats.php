<?php
/**
 * name: Stats
 * description: Displays basic stats about your Hotaru site
 * version: 0.2
 * folder: stats
 * class: Stats
 * requires: sidebar_widgets 0.4
 * hooks: install_plugin, hotaru_header, header_include
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
 * @author    Kyle Carlson <rushnp774@gmail.com>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */ 
 
 class Stats extends PluginFunctions
{
	public function install_plugin()
	{
	    require_once(PLUGINS . 'sidebar_widgets/libs/Sidebar.php');
	    $sidebar = new Sidebar($this->hotaru);
	    $sidebar->addWidget('stats', 'stats_display');  // plugin name, function name, optional arguments
	}  
	
    public function sidebar_widget_stats_display()
    {
	    $this->hotaru->vars['stats_numMembers'] = $this->db->get_var("SELECT COUNT(*) FROM " . TABLE_USERS);
	    $this->hotaru->vars['stats_numPosts'] = $this->db->get_var("SELECT COUNT(*) FROM " . TABLE_POSTS);
	    $this->hotaru->vars['stats_numComments'] = $this->db->get_var("SELECT COUNT(*) FROM " . TABLE_COMMENTS);
	    $this->hotaru->vars['stats_numVotes'] = $this->db->get_var("SELECT COUNT(*) FROM " . TABLE_POSTVOTES);
	    
	    //get # of days from table for the stats/day statistics
	    
	    //User
		//-newest
		$sql = "SELECT user_username FROM " . TABLE_USERS . " ORDER BY user_date DESC";
		$this->hotaru->vars['newestMemberName'] = $this->db->get_var($this->db->prepare($sql));
	    //-most active
		//-most active in each category
		//-highest rated - highest (comments+votes algorithm) on stories submitted
		
		//Categories
		//stories in each category
	    //most/least popular category
	    //most popular story in each category
	    //charts
	    
	    //Posts    	 
	    //most popular (votes + comments algorithm)
	    //most comments
	    //most votes
		$sql = "SELECT post_id, post_title FROM " . TABLE_POSTS . " ORDER BY post_votes_up DESC";
		$popPost= $this->db->get_row($this->db->prepare($sql)); // use get_row to grab more than one var
		
		// access each var like this
		$popPostId = $popPost->post_id;
		$popPostTitle = $popPost->post_title;
		
		// do we already have the post object available, if not make it
        if (!isset($this->hotaru->post)) { 
            $this->hotaru->post = new Post($this->hotaru); // used to get post information
        }
        
        // read the post using its ID
        $this->hotaru->post->readPost($popPostId); 
	    
		//Display the stats
	    $this->includeLanguage();
	    $this->hotaru->displayTemplate('stats_template', 'stats');
    }
}  