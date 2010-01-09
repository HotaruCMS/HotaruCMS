<?php
/**
 * name: Stats
 * description: Displays basic stats about your Hotaru site
 * version: 0.3
 * folder: stats
 * class: Stats
 * type: stats
 * requires: widgets 0.6
 * hooks: install_plugin, header_include
 * author: Kyle Carlson
 * authorurl: http://hotarucms.org/member.php?49-rushnp774
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
 
class Stats
{
	public function install_plugin($h)
	{
        // widget
        $h->addWidget('stats', 'stats_display');  // plugin name, function name, optional arguments
	}  
	
    public function widget_stats_display($h)
    {
	    $h->vars['stats_numMembers'] = $h->db->get_var("SELECT COUNT(*) FROM " . TABLE_USERS);
	    $h->vars['stats_numPosts'] = $h->db->get_var("SELECT COUNT(*) FROM " . TABLE_POSTS);
	    $h->vars['stats_numComments'] = $h->db->get_var("SELECT COUNT(*) FROM " . TABLE_COMMENTS);
	    $h->vars['stats_numVotes'] = $h->db->get_var("SELECT COUNT(*) FROM " . TABLE_POSTVOTES);
	    
	    //get # of days from table for the stats/day statistics
	    
	    //User
		//-newest
		$sql = "SELECT user_username FROM " . TABLE_USERS . " ORDER BY user_date DESC";
		$h->vars['newestMemberName'] = $h->db->get_var($h->db->prepare($sql));
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
		$popPost= $h->db->get_row($h->db->prepare($sql)); // use get_row to grab more than one var
		
		if ($popPost) {
    		// access each var like this
    		$popPostId = $popPost->post_id;
    		$popPostTitle = $popPost->post_title;
            
            // read the post using its ID
            $h->readPost($popPostId); 
        }
	    
		//Display the stats
	    $h->displayTemplate('stats_template', 'stats');
    }
}  