<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/submit_form.php
 *  Purpose: Step 2 for submitting a new story.
 *  Notes: This file is part of the Submit plugin. The main file is /plugins/submit/submit.php
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
 *  Function: sub_submit_form_2
 *  Parameters: None
 *  Purpose: Step 2 - Enter title, description, tags, etc.
 *  Notes: ---
 ********************************************************************** */
 
function sub_submit_form_2($post_orig_url, $post_orig_title) {
	global $hotaru, $cage, $lang, $post, $plugin;
		
	echo "<div id='main'>";
		echo "<p class='breadcrumbs'><a href='" . baseurl . "'>Home</a> &raquo; Submit a Story 2/2</p>\n";
			
		$hotaru->show_message();
				
		echo "<div class='main_inner'>";
		echo $lang["submit_form_instructions_2"] . "\n";
		
		$error = 0;
		
		if($cage->post->getAlpha('submit2') == 'true') {
			if(!sub_check_for_errors_2()) { 
				return true; // No errors found, return true.
			} else {
				$title_check = $cage->post->noTags('post_title');	
				$content_check = $cage->post->noTags('post_content');	
				$plugin->check_actions('submit_form_2_assign_from_cage');
			}
		} else {
			$title_check = $post_orig_title;
			$content_check = "";
			$plugin->check_actions('submit_form_2_assign_blank');
		}
		
	
			echo "<form name='submit_form_2' action='" . baseurl . "index.php?page=submit2&sourceurl=" . $post_orig_url . "' method='post'>\n";
			echo "<table>";
?>
			
			<tr>
				<td><?php echo $lang["submit_form_url"] ?>:&nbsp; </td>
				<td><?php echo $post_orig_url; ?></td>
				<td>&nbsp;</td>
			</tr>




			<tr>
				<td><?php echo $lang["submit_form_title"] ?>:&nbsp; </td>
				<td><input type='text' size=50 id='post_title' name='post_title' value='<?php echo $title_check ?>'></td>
				<td id='ajax_loader'>&nbsp;</td>
			</tr>
			
			
			
			
			<?php if($post->use_content) { ?>
			<tr>
				<td style='vertical-align: top;'><?php echo $lang["submit_form_content"] ?>:&nbsp; </td>
				<td colspan='2'><textarea id='post_content' name='post_content' rows='6' maxlength='<?php $post->post_content_length; ?>' style='width: 32em;'><?php echo $content_check ?></textarea></td>
			</tr>
			<?php } ?>
			
			
			
			<?php $plugin->check_actions('submit_form_2_fields'); ?>
				
			
			<input type='hidden' name='submit2' value='true' />

<?php				
			echo "<tr><td colspan=3>&nbsp;</td></tr>";
			echo "<tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' name='submit' value='" . $lang['submit_form_submit_button'] . "' /></td></tr>\n";	
			echo "</table>\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}


/* ******************************************************************** 
 *  Function: sub_check_for_errors_2
 *  Parameters: None
 *  Purpose: Checks submit_form_2 for errors
 *  Notes: ---
 ********************************************************************** */

function sub_check_for_errors_2() {
	global $hotaru, $post, $cage, $plugin, $lang;

	// ******** CHECK TITLE ********
	
	$title_check = $cage->post->noTags('post_title');	
	if(!$title_check) {
		// No title present...
		$hotaru->message = $lang['submit_form_title_not_present_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error_title= 1;
	} elseif($post->title_exists($title_check )) {
		// URL already exists...
		$hotaru->message = $lang['submit_form_title_already_exists_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error_title = 1;
	} else {
		// title is okay.
		$error_title = 0;
	}
	
	// ******** CHECK DESCRIPTION ********
	if($post->use_content) {
		$content_check = $cage->post->noTags('post_content');	
		if(!$content_check) {
			// No content present...
			$hotaru->message = $lang['submit_form_content_not_present_error'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error_content = 1;
		} elseif(strlen($content_check) < $post->post_content_length) {
			// content is too short
			$hotaru->message = $lang['submit_form_content_too_short_error'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error_content = 1;
		} else {
			// content is okay.
			$error_content = 0;
		}
	}
	
	// Check for errors from plugin fields, e.g. Tags
	$error_check_actions = 0;
	$error_array = $plugin->check_actions('submit_form_2_check_for_errors');
	if(is_array($error_array)) {
		foreach($error_array as $err) { if($err == 1) { $error_check_actions = 1; } }
	}
	
	// Return true if error is found
	if($error_title == 1 || $error_content == 1 || $error_check_actions == 1) { return true; } else { return false; }
}


/* ******************************************************************** 
 *  Function: sub_process_submission
 *  Parameters: None
 *  Purpose: Saves the submitted story to the database
 *  Notes: ---
 ********************************************************************** */
 
function sub_process_submission($post_orig_url) {
	global $hotaru, $cage, $plugin, $current_user, $post;
		
	$post->post_orig_url = $post_orig_url;
	$post->post_url = $cage->post->getFriendlyUrl('post_title');
	$post->post_title = $cage->post->getMixedString2('post_title');
	$post->post_content = $cage->post->getMixedString2('post_content');
	$post->post_status = "new";
	$post->post_author = $current_user->id;
	
	$plugin->check_actions('submit_form_2_process_submission');
	
	$post->add_post();

}

?>