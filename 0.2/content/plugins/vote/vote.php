<?php
/* ********** PLUGIN *********************************************************************************
 * name: Vote
 * description: Adds voting ability to posted stories.
 * version: 0.1
 * folder: vote
 * prefix: vote
 * requires: submit 0.2, users 0.1
 * hooks: install_plugin, hotaru_header, submit_hotaru_header_1, submit_class_post_read_post_1, submit_class_post_read_post_2, header_include, submit_pre_show_post, admin_plugin_settings, admin_sidebar_plugin_settings
 *
 *
 *  License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

/* ******************************************************************** 
 *  Function: vote_install_plugin
 *  Parameters: None
 *  Purpose: Install: Add vote fields to the post table and make a dedicated Votes table.
 *  Notes: ---
 ********************************************************************** */
 
function vote_install_plugin() {
	global $db, $plugin;
	
	// Create a new table column called "post_votes_up" if it doesn't already exist
	$exists = $db->column_exists('posts', 'post_votes_up');
	if(!$exists) {
		$db->query("ALTER TABLE " . table_posts . " ADD post_votes_up smallint(11) NOT NULL DEFAULT '0' AFTER post_content");
	} 
	
	// Create a new table column called "post_votes_down" if it doesn't already exist
	$exists = $db->column_exists('posts', 'post_votes_down');
	if(!$exists) {
		$db->query("ALTER TABLE " . table_posts . " ADD post_votes_down smallint(11) NOT NULL DEFAULT '0' AFTER post_votes_up");
	} 
	
	// Create a new empty table called "votes" if it doesn't already exist
	$exists = $db->table_exists('votes');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "votes` (
		  `vote_post_id` int(11) NOT NULL DEFAULT '0',
		  `vote_user_id` int(11) NOT NULL DEFAULT '0',
		  `vote_date` timestamp NULL,
		  `vote_type` varchar(32) NOT NULL DEFAULT 'post',
		  `vote_rating` enum('positive','negative') NULL,
		  `vote_reason` varchar(255) NULL,
		  `vote_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
 		  `vote_updateby` int(20) NOT NULL DEFAULT 0,
 		  INDEX  (`vote_post_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Votes';";
		$db->query($sql); 
	}   
    
	// Default settings
	$plugin->plugin_settings_update('vote', 'vote_vote_bury', 'checked');  
	$plugin->plugin_settings_update('vote', 'vote_up_down', '');    
	$plugin->plugin_settings_update('vote', 'vote_yes_no', '');  
	
	// Include language file. Also included in hotaru_header, but needed here so 
	// that the link in the Admin sidebar shows immediately after installation.
	$plugin->include_language_file('vote');
   
}  


/* ******************************************************************** 
 *  Function: vote_hotaru_header
 *  Parameters: None
 *  Purpose: Set things up when the page is first loaded
 *  Notes: ---
 ********************************************************************** */
 
function vote_hotaru_header() {
	global $plugin, $post;
	
	if(!defined('table_votes')) { define("table_votes", db_prefix . 'votes'); }
	
	$plugin->include_language_file('vote');	
}

/* ******************************************************************** 
 *  Function: vote_submit_hotaru_header_1
 *  Parameters: None
 *  Purpose: Adds additional member variables when the $post object is read in the Submit plugin.
 *  Notes: ---
 ********************************************************************** */
 
function vote_submit_hotaru_header_1() {
	global $post, $hotaru, $plugin, $cage;
		
	$post->post_vars['post_votes_up'] = 0;
	$post->post_vars['post_votes_down'] = 0;	
}

/* ******************************************************************** 
 *  Function: vote_submit_class_post_read_post_1
 *  Parameters: None
 *  Purpose: Read vote settings
 *  Notes: ---
 ********************************************************************** */
 
function vote_submit_class_post_read_post_1() {
	global $plugin, $post;
	
	if($plugin->plugin_active('vote')) { 
		// Determine vote type
		if(($plugin->plugin_settings('vote', 'vote_vote_bury') == 'checked')) {
			$post->post_vars['vote_type'] = "vote_bury";
		} elseif(($plugin->plugin_settings('vote', 'vote_up_down') == 'checked')) {
			$post->post_vars['vote_type'] = "up_down";
		} else {
			$post->post_vars['vote_type'] = "yes_no";
		}
	}
}


/* ******************************************************************** 
 *  Function: vote_submit_class_post_read_post_2
 *  Parameters: None
 *  Purpose: Read number of votes if post exists.
 *  Notes: ---
 ********************************************************************** */
 
function vote_submit_class_post_read_post_2() {
	global $post, $post_row;
	$post->post_vars['post_votes_up'] = $post_row->post_votes_up;
	$post->post_vars['post_votes_down'] = $post_row->post_votes_down;
}

/* ******************************************************************** 
 *  Function: vote_header_include
 *  Parameters: None
 *  Purpose: Includes css for the vote buttons.
 *  Notes: ---
 ********************************************************************** */
 
function vote_header_include() {
	global $plugin, $lang, $hotaru;
	$plugin->include_css_file('vote');
	$plugin->include_js_file('vote');
	$plugin->include_js_file('vote', 'json2.min');
}


 /* ******************************************************************** 
 * ********************************************************************* 
 * *********************** FUNCTIONS FOR VOTING ************************ 
 * *********************************************************************
 * ****************************************************************** */
 
 
 /* ******************************************************************** 
 *  Function: vote_submit_pre_show_post
 *  Parameters: None
 *  Purpose: Displays the vote button.
 *  Notes: ---
 ********************************************************************** */
 
function vote_submit_pre_show_post() {
	global $hotaru, $db, $post, $current_user, $voted;
	
	$sql = "SELECT vote_rating FROM " . table_votes . " WHERE vote_post_id = %d AND vote_user_id = %d AND vote_type = %s";
 	$voted = $db->get_var($db->prepare($sql, $post->post_id, $current_user->id, 'vote_bury'));
  	
 	$hotaru->display_template('vote_button', 'vote');
}

 /* ******************************************************************** 
 * ********************************************************************* 
 * ******************* FUNCTIONS FOR ADMIN SETTINGS ******************** 
 * *********************************************************************
 * ****************************************************************** */
 
/* ******************************************************************** 
 *  Function: vote_admin_sidebar_plugin_settings
 *  Parameters: None
 *  Purpose: Link to settings page in the Admin sidebar
 *  Notes: ---
 ********************************************************************** */
 
function vote_admin_sidebar_plugin_settings() {
	global $lang;
	
	echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>'vote'), 'admin') . "'>" . $lang["vote_admin_sidebar"] . "</a></li>";
}


/* ******************************************************************** 
 *  Function: vote_admin_plugin_settings
 *  Parameters: None
 *  Purpose: Vote Settings Page
 *  Notes: ---
 ********************************************************************** */
 
function vote_admin_plugin_settings() {
	global $hotaru, $plugin, $cage, $lang;
	
	// If the form has been submitted, go and save the data...
	if($cage->post->getAlpha('submitted') == 'true') { 
		vote_save_settings(); 
	}    
	
	echo "<h1>" . $lang["vote_settings_header"] . "</h1>\n";
	
	// Get settings from the database if they exist...
	$vote_bury = $plugin->plugin_settings('vote', 'vote_vote_bury');
	$up_down = $plugin->plugin_settings('vote', 'vote_up_down');
	$yes_no = $plugin->plugin_settings('vote', 'vote_yes_no');
	
	//...otherwise set to blank:
	if(!$vote_bury) { $vote_bury = ''; }
	if(!$up_down) { $up_down = ''; }
	if(!$yes_no) { $yes_no = ''; }
	
	// A plugin hook so other plugin developers can add settings
	$plugin->check_actions('vote_settings_get_values');
	
	// The form should be submitted to the admin_index.php page:
	echo "<form name='vote_settings_form' action='" . baseurl . "admin/admin_index.php?page=plugin_settings&amp;plugin=vote' method='post'>\n";
	
	echo "<p>" . $lang["vote_settings_instructions"] . "</p><br />";
	
	echo "<input type='radio' name='vote_type' value='vote_bury' " . $vote_bury . " >&nbsp;&nbsp;" . $lang["vote_settings_vote_bury"] . "<br />\n";    
	echo "<input type='radio' name='vote_type' value='up_down' " . $up_down . " >&nbsp;&nbsp;" . $lang["vote_settings_up_down"] . "<br />\n"; 
	echo "<input type='radio' name='vote_type' value='yes_no' " . $yes_no . " >&nbsp;&nbsp;" . $lang["vote_settings_yes_no"] . "<br />\n"; 
	
	// A plugin hook so other plugin developers can show settings
	$plugin->check_actions('vote_settings_form');
	    
	echo "<br /><br />\n";    
	echo "<input type='hidden' name='submitted' value='true' />\n";
	echo "<input type='submit' value='" . $lang["vote_settings_save"] . "' />\n";
	echo "</form>\n";
}


/* ******************************************************************** 
 *  Function: vote_save_settings
 *  Parameters: None
 *  Purpose: Save Vote Settings
 *  Notes: ---
 ********************************************************************** */

function vote_save_settings() {
	global $cage, $hotaru, $plugin, $lang;
	
	// Check the status of our checkbox
	if($cage->post->keyExists('vote_type')) { 
		$selected = $cage->post->testAlnumLines('vote_type'); 
		switch($selected) {
			case 'vote_bury':
				$vote_bury = "checked";
				$up_down = "";
				$yes_no = "";
				break;
			case 'up_down':
				$vote_bury = "";
				$up_down = "checked";
				$yes_no = "";
				break;
			case 'yes_no':
				$vote_bury = "";
				$up_down = "";
				$yes_no = "checked";
				break;
			default:
				$vote_bury = "checked";
				$up_down = "";
				$yes_no = "";
				break;
		}

	}
	
	// A plugin hook so other plugin developers can save settings   
	$plugin->check_actions('vote_save_settings');
	
	$plugin->plugin_settings_update('vote', 'vote_vote_bury', $vote_bury);
	$plugin->plugin_settings_update('vote', 'vote_up_down', $up_down);
	$plugin->plugin_settings_update('vote', 'vote_yes_no', $yes_no);
	
	// This is just a radio selction, so we'll assume it was updated successfully:
	$hotaru->message = $lang["vote_settings_saved"];
	$hotaru->message_type = "green";
	$hotaru->show_message();
	
	return true;    
} 

?>