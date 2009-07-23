<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/submit_step2.php
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
 
global $hotaru, $cage, $lang, $post, $plugin, $post_orig_url, $post_orig_title;

if($cage->post->getAlpha('submit2') == 'true') {
	$title_check = $cage->post->noTags('post_title');	
	$content_check = $cage->post->noTags('post_content');	
	$plugin->check_actions('submit_form_2_assign_from_cage');
} else {
	$title_check = $post_orig_title;
	$content_check = "";
	$plugin->check_actions('submit_form_2_assign_blank');
}

echo "title_check: " . $title_check;
?>
	<div id='main'>
		<p class='breadcrumbs'><a href='<?php echo baseurl ?>'><?php echo $lang['submit_form_home'] ?></a> &raquo; <?php echo $lang["submit_form_step2"] ?></p>
			
		<?php echo $hotaru->show_messages(); ?>
				
		<div class='main_inner'>
		
			<?php echo $lang["submit_form_instructions_2"] ?>
		
			<form name='submit_form_2' action='<?php baseurl ?>index.php?page=submit2&sourceurl=<?php echo $post_orig_url ?>' method='post'>
			<table>
			<tr>
				<td><?php echo $lang["submit_form_url"] ?>&nbsp; </td>
				<td><?php echo $post_orig_url; ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $lang["submit_form_title"] ?>&nbsp; </td>
				<td><input type='text' size=50 id='post_title' name='post_title' value='<?php echo $title_check ?>'></td>
				<td id='ajax_loader'>&nbsp;</td>
			</tr>
			
			<?php if($post->use_content) { ?>
			<tr>
				<td style='vertical-align: top;'><?php echo $lang["submit_form_content"] ?>&nbsp; </td>
				<td colspan='2'><textarea id='post_content' name='post_content' rows='6' maxlength='<?php $post->post_content_length; ?>' style='width: 32em;'><?php echo $content_check ?></textarea></td>
			</tr>
			<?php } ?>
			
			<?php $plugin->check_actions('submit_form_2_fields'); ?>
				
			<input type='hidden' name='post_orig_url' value='<?php echo $post_orig_url; ?>' />
			<input type='hidden' name='submit2' value='true' />
			
			<tr><td colspan=3>&nbsp;</td></tr>
			<tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' name='submit' value='<?php echo $lang['submit_form_submit_button'] ?>' /></td></tr>	
			</table>
			</form>
		</div>
	</div>
