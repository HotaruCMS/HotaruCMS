<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/hello_universe/form_example.php
 *  Purpose: Example form.
 *  Notes: This file is part of the Hello Universe plugin. 
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

global $hotaru, $cage, $lang;

echo "<div id='main'>";
	echo "<h2>Example Form</h2>\n";
	
	echo "This form is in the form_example.php file in the Hello Universe folder. It's called via Function #1 in hello_universe.php and includes a special language file which is included using Function #4.";
	
	echo "<div class='main_inner'>";
	echo $lang["hello_universe_question"] . "\n";
	
	$error = 0;
	if($cage->post->getAlpha('submit_example') == 'true') {
		$answer = $cage->post->getMixedString2('answer');
		if($answer && $answer == 'Paris') {
			$hotaru->message = $lang['hello_universe_success'];
			$hotaru->message_type = 'green';
			$hotaru->show_message();
		} else {
			$hotaru->message = $lang['hello_universe_failure'];
			$hotaru->message_type = 'red';
			$hotaru->show_message();
			$error = 1;
		}
	} else {
		$answer = '';
	}
			
	
		echo "<form name='update_form' action='" . baseurl . "index.php?page=form_example' method='post'>\n";	
		echo "<table>";
		echo "<tr><td>" . $lang['hello_universe_answer'] . "&nbsp; </td><td><input type='text' size=30 name='answer' value='" . $answer . "' /></td></tr>\n";
		echo "<input type='hidden' name='submit_example' value='true' />\n";
		echo "<tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='" . $lang['hello_universe_form_submit'] . "' /></td></tr>\n";			
		echo "</table>\n";
		echo "</form>\n";
	echo "</div>\n";
	
	echo "<p><a href='" . baseurl . "'>Back Home</a></p>\n";
echo "</div>\n";


?>