<?php
/* ********** PLUGIN *********************************************************************************
 * name: Tags
 * description: Enables tags for posts
 * version: 0.1
 * folder: tags
 * prefix: tg
 * requires: submit 0.1
 * hooks: install_plugin, submit_hotaru_header_1, submit_class_post_read_post_1, submit_class_post_read_post_2, submit_class_post_add_post, submit_form_2_assign_from_cage, submit_form_2_assign_blank, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_show_post_extra_fields, submit_settings_get_values, submit_settings_form, submit_save_settings, submit_posts_list_filter
 *
 * Requires the Submit plugin.
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
 * ********************************************************************* 
 * ********************* FUNCTIONS FOR POST CLASS ********************** 
 * *********************************************************************
 * ****************************************************************** */
 
 
/* ******************************************************************** 
 *  Function: tg_install_plugin
 *  Parameters: None
 *  Purpose: If it doesn't already exist, add a post_tags field to posts table.
 *  Notes: Happens when the plugin is installed. The field is never deleted.
 ********************************************************************** */
 
function tg_install_plugin() {
	global $db, $plugin, $post;
	
	// Create a new table column called "post_tags" if it doesn't already exist
	$exists = $db->column_exists('posts', 'post_tags');
	if(!$exists) {
		$db->query("ALTER TABLE " . table_posts . " ADD post_tags TEXT NULL AFTER post_content");
		$db->query("ALTER TABLE " . table_posts . " ADD FULLTEXT (post_tags)"); // Make it fulltext searchable
	} 
	
	// Create a new empty table called "tags" if it doesn't already exist
	$exists = $db->table_exists('tags');
	if(!$exists) {
		//echo "table doesn't exist. Stopping before creation."; exit;
		$sql = "CREATE TABLE `" . db_prefix . "tags` (
		  `tags_post_id` int(11) NOT NULL DEFAULT '0',
		  `tags_date` timestamp NULL,
		  `tags_word` varchar(64) NOT NULL DEFAULT '',
		  `tags_updatedts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
 		  `tags_updateby` int(20) NOT NULL DEFAULT 0, 
		  UNIQUE KEY `tags_post_id` (`tags_post_id`,`tags_word`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Post Tags';";
		$db->query($sql); 
	}
	
	// Default settings (Note: we can't use $post->post_vars because it hasn't been filled yet.)
	$plugin->plugin_settings_update('submit', 'submit_tags', 'checked');
	$plugin->plugin_settings_update('submit', 'submit_max_tags', 50);
	
	// Could possibly do with some code here that extracts all existingtags from the posts table and populates the tags table with them.
	// Maybe in a later version.
}


/* ******************************************************************** 
 *  Function: tg_submit_hotaru_header_1
 *  Parameters: None
 *  Purpose: Adds additional member variables when the $post object is read in the Submit plugin.
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_hotaru_header_1() {
	global $post, $plugin;
	
	define("table_tags", db_prefix . 'tags');
	
	// include language file
	$plugin->include_language_file('tags');
	
	$post->post_vars['post_tags'] = '';
	$post->post_vars['post_max_tags'] = 50;	// max characters for tags
	$post->post_vars['use_tags'] = true;
	
}


/* ******************************************************************** 
 *  Function: tg_submit_class_post_read_post_1
 *  Parameters: None
 *  Purpose: Read tag settings
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_class_post_read_post_1() {
	global $plugin, $post;
	
	//tags
	if(($plugin->plugin_settings('submit', 'submit_tags') == 'checked') && ($plugin->plugin_active('tags'))) { 
		$post->post_vars['use_tags'] = true; 
	} else { 
		$post->post_vars['use_tags'] = false; 
	}
	
	$max_tags = $plugin->plugin_settings('submit', 'submit_max_tags');
	if(!empty($max_tags)) { $post->post_vars['post_max_tags'] = $max_tags; }
}


/* ******************************************************************** 
 *  Function: tg_submit_class_post_read_post_2
 *  Parameters: None
 *  Purpose: Read tag settings if post_id exists.
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_class_post_read_post_2() {
	global $post, $post_row;
	$post->post_vars['post_tags'] = urldecode($post_row->post_tags);
}


/* ******************************************************************** 
 *  Function: tg_submit_class_post_add_post
 *  Parameters: None
 *  Purpose: Adds tags to the posts table
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_class_post_add_post() {
	global $post, $db, $last_insert_id, $current_user;
	
	$sql = "UPDATE " . table_posts . " SET post_tags = %s WHERE post_id = %d";
	$db->query($db->prepare($sql, urlencode(trim($post->post_vars['post_tags'])), $last_insert_id));
		
	if(!empty($post->post_vars['post_tags'])) {
		$tags_array = explode(',', $post->post_vars['post_tags']);
		if($tags_array) {
			foreach($tags_array as $tag) {
				$sql = "INSERT INTO " . table_tags . " SET tags_post_id = %d, tags_date = CURRENT_TIMESTAMP, tags_word = %s, tags_updateby = %d";
				$db->query($db->prepare($sql, $last_insert_id, urlencode(trim($tag)), $current_user->id));
			}
		}
	}
}


 /* ******************************************************************** 
 * ********************************************************************* 
 * ********************* FUNCTIONS FOR SUBMIT FORM ********************* 
 * *********************************************************************
 * ****************************************************************** */
 

/* ******************************************************************** 
 *  Function: tg_submit_form_2_assign_from_cage
 *  Parameters: None
 *  Purpose: Sets $tags_check to the value submitted through the form
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_form_2_assign_from_cage() {
	global $cage, $tags_check;
	$tags_check = $cage->post->noTags('post_tags');
}


/* ******************************************************************** 
 *  Function: tg_submit_form_2_assign_blank
 *  Parameters: None
 *  Purpose: Sets $tags_check to blank
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_form_2_assign_blank() {
	global $tags_check;
	$tags_check = "";
}


/* ******************************************************************** 
 *  Function: tg_submit_form_2_fields
 *  Parameters: None
 *  Purpose: Adds a tags field to submit form 2
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_form_2_fields() {
	global $lang, $post, $tags_check;

	if($post->post_vars['use_tags']) { 
		echo "<tr>";
			echo "<td>" . $lang["submit_form_tags"] . ":&nbsp; </td>";
			echo "<td><input type='text' size=50 name='post_tags' value='" . $tags_check . "'></td>";
			echo "<td>&nbsp;</td>";
		echo "</tr>";
	}
}


/* ******************************************************************** 
 *  Function: tg_submit_form_2_check_for_errors
 *  Parameters: None
 *  Purpose: Adds a tags field to submit form 2
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_form_2_check_for_errors() {
	global $hotaru, $lang, $post, $cage, $lang, $tags_check;
	
	// ******** CHECK TAGS ********
	if($post->post_vars['use_tags']) {
		$tags_check = $cage->post->noTags('post_tags');	
		if(!$tags_check) {
			// No tags present...
			$hotaru->message = $lang['submit_form_tags_not_present_error'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error_tags = 1;
		} elseif(strlen($tags_check) > $post->post_max_tags) {
			// total tag length is too long
			$hotaru->message = $lang['submit_form_tags_length_error'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error_tags = 1;
		} else {
			// tags are okay.
			$error_tags = 0;
		}
	}
	
	return $error_tags;
}


/* ******************************************************************** 
 *  Function: tg_submit_form_2_process_submission
 *  Parameters: None
 *  Purpose: Sets $post->post_tags to submitted string of tags
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_form_2_process_submission() {
	global $cage, $post;
	$post->post_vars['post_tags'] = $cage->post->getMixedString2('post_tags');
}


 /* ******************************************************************** 
 * ********************************************************************* 
 * ******************* FUNCTIONS FOR SHOWING POSTS ********************* 
 * *********************************************************************
 * ****************************************************************** */
 

/* ******************************************************************** 
 *  Function: tg_submit_posts_list_filter
 *  Parameters: None
 *  Purpose: Gets a tag from the url and sets the filter for get_posts
 *  Notes: This hook is at the top of posts_list.php in the Sumit plugin.
 ********************************************************************** */
 
function tg_submit_posts_list_filter() {
	global $post, $cage, $filter;
	
	// friendly URLs: FALSE
	$tag = $cage->get->getMixedString2('tag'); 
	
	// friendly URLs: TRUE
	if(!$tag) { $tag = $cage->get->getMixedString2('pos2'); } 
	
	if($tag) {
		$filter = array('post_tags LIKE %s' => '%' . $tag . '%'); 
		return true;	
	}
	
	return false;	
}


/* ******************************************************************** 
 *  Function: tg_submit_show_post_extra_fields
 *  Parameters: None
 *  Purpose: Shows tags in each post
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_show_post_extra_fields() { 
	global $post;
	
	if($post->post_vars['use_tags'] && $post->post_vars['post_tags']) { 
		echo "<div class='show_post_tags'>";
		$tags = explode(',', $post->post_vars['post_tags']);
		
		echo "Tags: ";
		
		foreach($tags as $tag) {
			echo "<a href='" . url(array('tag' => trim($tag))) . "'>" . trim($tag) . "</a>&nbsp;";
		}
		echo "</div>";
	}		
}



 /* ******************************************************************** 
 * ********************************************************************* 
 * ****************** FUNCTIONS FOR SUBMIT SETTINGS ******************** 
 * *********************************************************************
 * ****************************************************************** */
 

/* ******************************************************************** 
 *  Function: tg_submit_settings_get_values
 *  Parameters: None
 *  Purpose: Gets current tag settings from the database
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_settings_get_values() {
	global $plugin, $tags, $max_tags;
	
	// Get settings from database if they exist...
	$tags = $plugin->plugin_settings('submit', 'submit_tags');
	$max_tags = $plugin->plugin_settings('submit', 'submit_max_tags');
	
	// otherwise set to blank...
	if(!$tags) { $tags = ''; }
	if(!$max_tags) { $max_tags = ''; }
}


/* ******************************************************************** 
 *  Function: tg_submit_settings_form
 *  Parameters: None
 *  Purpose: Add tags field to the submit settings form
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_settings_form() {
	global $plugin, $lang, $tags, $max_tags;
	
	echo "<input type='checkbox' name='tags' value='tags' " . $tags . ">&nbsp;&nbsp;" . $lang["submit_settings_tags"];
	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo $lang["submit_settings_max_tags"] . ": <input type='text' size=5 name='max_tags' value='" . $max_tags . "' /><br />\n";
}


/* ******************************************************************** 
 *  Function: tg_submit_save_settings
 *  Parameters: None
 *  Purpose: Save tag settings.
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_save_settings() {
	global $plugin, $cage, $lang, $tags, $max_tags;
	
	// Tags
	if($cage->post->keyExists('tags')) { 
		$tags = 'checked'; 
		$post->post_vars['use_tags'] = true;
	} else { 
		$tags = ''; 
		$post->post_vars['use_tags'] = false;
	}
		
	// Tags length
	if($cage->post->keyExists('max_tags')) { 
		$max_tags = $cage->post->getInt('max_tags'); 
		if(empty($max_tags)) { $max_tags = $post->post_vars['post_max_tags']; }
	} else { 
		$max_tags = $post->post_vars['post_max_tags']; 
	} 
	
	$plugin->plugin_settings_update('submit', 'submit_tags', $tags);
	$plugin->plugin_settings_update('submit', 'submit_max_tags', $max_tags);
}

?>