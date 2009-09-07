<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/submit_step1.php
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
 
global $hotaru, $cage, $lang, $post, $post_orig_url;

?>

	<p class='breadcrumbs'><a href='<?php echo baseurl ?>'><?php echo $lang['submit_form_home'] ?></a> &raquo; <?php echo $lang["submit_form_step1"] ?></p>
		
	<?php echo $hotaru->show_message(); ?>
			

	<?php echo $lang["submit_form_instructions_1"] ?>
	
	<form name='submit_form_1' action='<?php echo baseurl ?>index.php?page=submit' method='post'>
	<table>
	<tr>
		<td><?php echo $lang["submit_form_url"] ?>&nbsp; </td>
		<td><input type='text' size=50 id='post_orig_url' name='post_orig_url' value='<?php echo $post_orig_url; ?>' /></td>
		<td>&nbsp;</td>
	</tr>

	<input type='hidden' name='submit1' value='true' />

	<tr><td colspan=3>&nbsp;</td></tr>
	<tr><td>&nbsp; </td><td>&nbsp; </td><td style='text-align:right;'><input type='submit' name='submit' value='<?php echo $lang['submit_form_submit_button'] ?>' /></td></tr>	
	</table>
	</form>
