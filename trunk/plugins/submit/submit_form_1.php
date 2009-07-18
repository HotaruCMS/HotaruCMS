<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/submit_form_1.php
 *  Purpose: Step 1 for submitting a new story.
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
 *  Function: sub_submit_form_1
 *  Parameters: None
 *  Purpose: Step 1 - Enter source url 
 *  Notes: ---
 ********************************************************************** */
 
function sub_submit_form_1() {
	global $hotaru, $cage, $lang, $post;
		
	echo "<div id='main'>";
		echo "<p class='breadcrumbs'><a href='" . baseurl . "'>Home</a> &raquo; Submit a Story 1/2</p>\n";
			
		$hotaru->show_message();
				
		echo "<div class='main_inner'>";
		echo $lang["submit_form_instructions_1"] . "\n";
		
		$error = 0;
		
		if($cage->post->getAlpha('submit1') == 'true') {
			if(!sub_check_for_errors_1()) { 
				// No errors found, return the url being submitted so it can be passed to submit_form_2.
				return $cage->post->testUri('post_orig_url'); 
			} else {
				$post_orig_url_check = $cage->post->testUri('post_orig_url');
			}
		} else {
			$post_orig_url_check = "";
		}
		
	
			echo "<form name='submit_form_1' action='" . baseurl . "index.php?page=submit' method='post'>\n";
			echo "<table>";
?>
			
			<tr>
				<td><?php echo $lang["submit_form_url"] ?>:&nbsp; </td>
				<td><input type='text' size=50 id='post_orig_url' name='post_orig_url' value='<?php echo $post_orig_url_check ?>' /></td>
				<td>&nbsp;</td>
			</tr>

			
			<input type='hidden' name='submit1' value='true' />

<?php				
			echo "<tr><td colspan=3>&nbsp;</td></tr>";
			echo "<tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' name='submit' value='" . $lang['submit_form_submit_button'] . "' /></td></tr>\n";	
			echo "</table>\n";
			echo "</form>\n";
		echo "</div>\n";
	echo "</div>\n";
}


/* ******************************************************************** 
 *  Function: sub_check_for_errors_1
 *  Parameters: None
 *  Purpose: Checks submit_form_1 for errors
 *  Notes: ---
 ********************************************************************** */

function sub_check_for_errors_1() {
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
	
	// Return true if error is found
	if($error == 1) { return true; } else { return false; }
}



?>