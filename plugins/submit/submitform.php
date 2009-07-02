<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/submitform.php
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
 *  Function: sub_submitform
 *  Parameters: None
 *  Purpose: Enables a user to submit a story
 *  Notes: ---
 ********************************************************************** */
 
function sub_submitform() {
	global $plugin, $cage, $lang;
	
	echo "<div id='main'>";
		echo "<h2><a href='" . baseurl . "'>Home</a> &raquo; Submit a Story</h2>\n";
			
		$plugin->show_message();
		
		echo "<div class='main_inner'>";
		echo $lang["submit_submitform_instructions_1"] . "\n";
		
		if($cage->post->getAlpha('submit_type') == 'submit') {
			$source_url_check = $cage->post->noTags('story_title');	// checks it's a url
			if($source_url_check) {
				//$current_user->username = $username_check;
			} else {
				$plugin->message = $lang['submit_submitform_title_error'];
				$plugin->message_type = 'red';
				$plugin->show_message();
				$story_title_check = "";
				$error = 1;
			}
		} else {
			$source_url_check = "";
		}
	
			echo "<form name='submit_form' action='" . baseurl . "index.php?page=submit' method='post'>\n";
			echo "<table>";
?>
			
			<tr>
				<td>Source url:&nbsp; </td>
				<td><input type='text' size=50 id='source_url' name='source_url' value='<?php echo $source_url_check ?>' /></td>
				<td style="text-align:right;"><a href="#" onclick="submit_url('<?php echo baseurl; ?>', '<?php echo baseurl; ?>plugins/submit/fetch_source.php');"><b><?php echo $lang['submit_submitform_get_title_button']; ?></b></a></td>
			</tr>

			<tr>
				<td>Title:&nbsp; </td>
				<td><input type='text' size=50 id='submit_return_value' name='post_title' value=''></td>
				<td id='ajax_loader'>&nbsp;</td>
			</tr>
			<input type='hidden' name='submit_type' value='submit' />

<?php				
			echo "<tr><td colspan=3>&nbsp;</td></tr>";
			echo "<tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' name='submit' value='" . $lang['submit_submitform_submit_button'] . "' /></td></tr>\n";	
			echo "</table>\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}

?>