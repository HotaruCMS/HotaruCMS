<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/submit_form.php
 *  Purpose: For submitting a new story.
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
 *  Function: sub_submit_form
 *  Parameters: None
 *  Purpose: Enables a user to submit a story
 *  Notes: ---
 ********************************************************************** */
 
function sub_submit_form() {
	global $hotaru, $cage, $lang, $post;
		
	echo "<div id='main'>";
		echo "<h2><a href='" . baseurl . "'>Home</a> &raquo; Submit a Story</h2>\n";
			
		$hotaru->show_message();
				
		echo "<div class='main_inner'>";
		echo $lang["submit_form_instructions_1"] . "\n";
		
		$error = 0;
		
		if($cage->post->getAlpha('submitted') == 'true') {
			if(!sub_check_for_errors()) { 
				return true; // No errors found, return true.
			} else {
				$post_orig_url_check = $cage->post->testUri('post_orig_url');
				$title_check = $cage->post->noTags('post_title');	
				$content_check = $cage->post->noTags('post_content');	
				$tags_check = $cage->post->noTags('post_tags');
			}
		} else {
			$post_orig_url_check = "";
			$title_check = "";
			$content_check = "";
			$tags_check = "";
		}
		
	
			echo "<form name='submit_form' action='" . baseurl . "index.php?page=submit' method='post'>\n";
			echo "<table>";
?>
			
			<tr>
				<td><?php echo $lang["submit_form_url"] ?>:&nbsp; </td>
				<td><input type='text' size=50 id='post_orig_url' name='post_orig_url' value='<?php echo $post_orig_url_check ?>' /></td>
				<td style="text-align:right;"><a href="#" onclick="submit_url('<?php echo baseurl; ?>', '<?php echo baseurl; ?>plugins/submit/fetch_source.php');"><b><?php echo $lang['submit_form_get_title_button']; ?></b></a></td>
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
			
			
			
			
			<?php if($post->use_tags) { ?>
			<tr>
				<td><?php echo $lang["submit_form_tags"] ?>:&nbsp; </td>
				<td><input type='text' size=50 name='post_tags' value='<?php echo $tags_check ?>'></td>
				<td>&nbsp;</td>
			</tr>
			<?php } ?>
			
			
			
			<input type='hidden' name='submitted' value='true' />

<?php				
			echo "<tr><td colspan=3>&nbsp;</td></tr>";
			echo "<tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' name='submit' value='" . $lang['submit_form_submit_button'] . "' /></td></tr>\n";	
			echo "</table>\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}


/* ******************************************************************** 
 *  Function: sub_check_for_errors
 *  Parameters: None
 *  Purpose: Checks submit_form for errors
 *  Notes: ---
 ********************************************************************** */

function sub_check_for_errors() {
	global $hotaru, $post, $cage, $lang;

	// ******** CHECK URL ********
	
	$post_orig_url_check = $cage->post->testUri('post_orig_url');
	if(!$post_orig_url_check) {
		// No url present...
		$hotaru->message = $lang['submit_form_url_not_present_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error = 1;
	} elseif($post->url_exists($post_orig_url_check)) {
		// URL already exists...
		$hotaru->message = $lang['submit_form_url_already_exists_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error = 1;
	} else {
		// URL is okay.
		$error = 0;
	}
	
	// ******** CHECK TITLE ********
	
	$title_check = $cage->post->noTags('post_title');	
	if(!$title_check) {
		// No title present...
		$hotaru->message = $lang['submit_form_title_not_present_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error = 1;
	} elseif($post->title_exists($title_check )) {
		// URL already exists...
		$hotaru->message = $lang['submit_form_title_already_exists_error'];
		$hotaru->message_type = 'red';
		$hotaru->show_message();
		$error = 1;
	} else {
		// title is okay.
		$error = 0;
	}
	
	// ******** CHECK DESCRIPTION ********
	if($post->use_content) {
		$content_check = $cage->post->noTags('post_content');	
		if(!$content_check) {
			// No content present...
			$hotaru->message = $lang['submit_form_content_not_present_error'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error = 1;
		} elseif(strlen($content_check) < $post->post_content_length) {
			// content is too short
			$hotaru->message = $lang['submit_form_content_too_short_error'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error = 1;
		} else {
			// content is okay.
			$error = 0;
		}
	}
	
	// ******** CHECK TAGS ********
	if($post->use_tags) {
		$tags_check = $cage->post->noTags('post_tags');	
		if(!$tags_check) {
			// No content present...
			$hotaru->message = $lang['submit_form_tags_not_present_error'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error = 1;
		} elseif(strlen($tags_check) > $post->post_max_tags) {
			// tags are too long
			$hotaru->message = $lang['submit_form_tags_length_error'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error = 1;
		} else {
			// tags are okay.
			$error = 0;
		}
	}
	
	// Return true if error is found
	if($error == 1) { return true; } else { return false; }
}


/* ******************************************************************** 
 *  Function: sub_process_submission
 *  Parameters: None
 *  Purpose: Saves the submitted story to the database
 *  Notes: ---
 ********************************************************************** */
 
function sub_process_submission() {
	global $hotaru, $cage, $current_user, $post;
		
	$post->post_orig_url = $cage->post->testUri('post_orig_url');
	$post->post_url = $cage->post->getAlnum('post_title');
	$post->post_title = $cage->post->noTags('post_title');
	$post->post_content = $cage->post->noTags('post_content');
	$post->post_tags = $cage->post->noTags('post_tags');
	$post->post_status = "new";
	$post->post_author = $current_user->id;
	$post->add_post();

}

?>