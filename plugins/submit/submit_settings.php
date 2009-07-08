<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/submit/submit_settings.php
 *  Purpose: Admin settings for the Submit plugin
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
 *  Function: sub_settings
 *  Parameters: None
 *  Purpose: Admin settings for the Submit plugin
 *  Notes: ---
 ********************************************************************** */
 
function sub_settings() {
	global $hotaru, $plugin, $cage, $lang;
	
	// If the form has been submitted, go and save the data...
	if($cage->post->getAlpha('submitted') == 'true') { 
		sub_save_settings(); 
	}	
	
	echo "<h1>" . $lang["submit_settings_header"] . "</h1>\n";
	
	// Get settings from database if they exist...
	$author = $plugin->plugin_settings('submit', 'submit_author');
	$date = $plugin->plugin_settings('submit', 'submit_date');
	$content = $plugin->plugin_settings('submit', 'submit_content');
	$content_length = $plugin->plugin_settings('submit', 'submit_content_length');
	$tags = $plugin->plugin_settings('submit', 'submit_tags');
	$max_tags = $plugin->plugin_settings('submit', 'submit_max_tags');
	
	//...otherwise set to blank:
	if(!$content) { $content = ''; }
	if(!$author) { $author = ''; }
	if(!$date) { $date = ''; }
	if(!$content_length) { $content_length = ''; }
	if(!$tags) { $tags = ''; }
	if(!$max_tags) { $max_tags = ''; }
	
	echo "<form name='submit_settings_form' action='" . baseurl . "admin/admin_index.php?page=plugin_settings&amp;plugin=submit' method='post'>\n";
	
	echo "<p>" . $lang["submit_settings_instructions"] . "</p><br />";
		
	echo "<input type='checkbox' name='title' value='title' checked disabled>&nbsp;&nbsp;" . $lang["submit_settings_title"] . "<br />\n";
	echo "<input type='checkbox' name='author' value='author' " . $author . ">&nbsp;&nbsp;" . $lang["submit_settings_author"] . "<br />\n";
	echo "<input type='checkbox' name='date' value='date' " . $author . ">&nbsp;&nbsp;" . $lang["submit_settings_date"] . "<br />\n";
	echo "<input type='checkbox' name='content' value='content' " . $content . ">&nbsp;&nbsp;" . $lang["submit_settings_content"];
	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo $lang["submit_settings_content_length"] . ": <input type='text' size=5 name='content_length' value='" . $content_length . "' /><br />\n";
	echo "<input type='checkbox' name='tags' value='tags' " . $tags . ">&nbsp;&nbsp;" . $lang["submit_settings_tags"];
	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo $lang["submit_settings_max_tags"] . ": <input type='text' size=5 name='max_tags' value='" . $max_tags . "' /><br />\n";

			
	echo "<br />\n";	
	echo "<input type='hidden' name='submitted' value='true' />\n";
	echo "<input type='submit' value='" . $lang["submit_settings_save"] . "' />\n";
	echo "</form>\n";
}


/* ******************************************************************** 
 *  Function: sub_save_settings
 *  Parameters: None
 *  Purpose: Takes updated Submit settings and saves them in the pluginsettings database table.
 *  Notes: Also updates settings in Post class so we can easily reference them: if($post->use_tags) ... etc.
 ********************************************************************** */
 
function sub_save_settings() {
	global $cage, $hotaru, $plugin, $post, $lang;

	// Author
	if($cage->post->keyExists('author')) { 
		$author = 'checked'; 
		$post->use_author = true;
	} else { 
		$author = ''; 
		$post->use_author = false;
	}
	
	// Date
	if($cage->post->keyExists('date')) { 
		$date = 'checked'; 
		$post->use_date = true;
	} else { 
		$date = ''; 
		$post->use_date = false;
	}
	
	// Description
	if($cage->post->keyExists('content')) { 
		$content = 'checked'; 
		$post->use_content = true;
	} else { 
		$content = ''; 
		$post->use_content = false;
	}
	
	// Description length
	if($cage->post->keyExists('content_length')) { 
		$content_length = $cage->post->getInt('content_length'); 
		if(empty($content_length)) { $content_length = $post->post_content_length; }
	} else { 
		$content_length = $post->post_content_length; 
	} 
	
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

	$plugin->plugin_settings_update('submit', 'submit_author', $author);	
	$plugin->plugin_settings_update('submit', 'submit_date', $date);	
	$plugin->plugin_settings_update('submit', 'submit_content', $content);	
	$plugin->plugin_settings_update('submit', 'submit_content_length', $content_length);	
	$plugin->plugin_settings_update('submit', 'submit_tags', $tags);
	$plugin->plugin_settings_update('submit', 'submit_max_tags', $max_tags);
	
	$hotaru->message = $lang["submit_settings_saved"];
	$hotaru->message_type = "green";
	$hotaru->show_message();
	
	return true;	
}
?>