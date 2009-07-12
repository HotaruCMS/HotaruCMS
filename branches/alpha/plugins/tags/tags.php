<?php
/* ********** PLUGIN *********************************************************************************
 * name: Tags
 * description: Enables tags for posts
 * version: 0.1
 * folder: tags
 * prefix: tg
 * hooks: submit_form_2_assign_from_cage, submit_form_2_assign_blank, submit_form_2_fields, submit_form_2_check_for_errors, submit_form_2_process_submission, submit_posts_list_extra_fields_1, submit_post_page_extra_fields_1, submit_settings_get_values, submit_settings_form, submit_save_settings
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
	if($post->use_tags) { 
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
	if($post->use_tags) {
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
	$post->post_tags = $cage->post->getMixedString2('post_tags');
}


 /* ******************************************************************** 
 * ********************************************************************* 
 * ******************* FUNCTIONS FOR SHOWING POSTS ********************* 
 * *********************************************************************
 * ****************************************************************** */
 

/* ******************************************************************** 
 *  Function: tg_submit_posts_list_extra_fields_1
 *  Parameters: None
 *  Purpose: Shows tags in each post on Posts List page
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_posts_list_extra_fields_1() {
	global $post;
	if($post->use_tags) { 
		echo "<div class='show_post_tags'>" . $post->post_tags . "</div>";
	}
}


/* ******************************************************************** 
 *  Function: tg_submit_post_page_extra_fields_1
 *  Parameters: None
 *  Purpose: Shows tags in each post on Post Page page
 *  Notes: ---
 ********************************************************************** */
 
function tg_submit_post_page_extra_fields_1() {
	global $post;
	if($post->use_tags) { 
		echo "<div class='show_post_tags'>" . $post->post_tags . "</div>";
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
		$post->use_tags = true;
	} else { 
		$tags = ''; 
		$post->use_tags = false;
	}
		
	// Tags length
	if($cage->post->keyExists('max_tags')) { 
		$max_tags = $cage->post->getInt('max_tags'); 
		if(empty($max_tags)) { $max_tags = $post->post_max_tags; }
	} else { 
		$max_tags = $post->post_max_tags; 
	} 
	
	$plugin->plugin_settings_update('submit', 'submit_tags', $tags);
	$plugin->plugin_settings_update('submit', 'submit_max_tags', $max_tags);
}

?>