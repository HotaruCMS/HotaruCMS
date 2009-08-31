<?php
 
/* **************************************************************************************************** 
 *  File: /plugins/users/users_settings.php
 *  Purpose: Admin settings for the Users plugin
 *  Notes: This file is part of the Users plugin. The main file is /plugins/users/users.php
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
 *  Function: usr_settings
 *  Parameters: None
 *  Purpose: Admin settings for the Users plugin
 *  Notes: ---
 ********************************************************************** */
 
function usr_settings() {
	global $hotaru, $plugin, $cage, $lang;
	
	// If the form has been submitted, go and save the data...
	if($cage->post->getAlpha('submitted') == 'true') { 
		usr_save_settings(); 
	}	
	
	echo "<h1>" . $lang["users_settings_header"] . "</h1>\n";
	
	// Get settings from database if they exist...
	$recaptcha_enabled = $plugin->plugin_settings('users', 'users_recaptcha_enabled');
	$recaptcha_pubkey = $plugin->plugin_settings('users', 'users_recaptcha_pubkey');
	$recaptcha_privkey = $plugin->plugin_settings('users', 'users_recaptcha_privkey');
	$emailconf_enabled = $plugin->plugin_settings('users', 'users_emailconf_enabled');


	$plugin->check_actions('users_settings_get_values');
	
	//...otherwise set to blank:
	if(!$recaptcha_enabled) { $recaptcha_enabled = ''; }
	if(!$recaptcha_pubkey) { $recaptcha_pubkey = ''; }
	if(!$recaptcha_privkey) { $recaptcha_privkey = ''; }
	if(!$emailconf_enabled) { $emailconf_enabled = ''; }
	
	echo "<form name='users_settings_form' action='" . baseurl . "admin/admin_index.php?page=plugin_settings&amp;plugin=users' method='post'>\n";
	
	echo "<p>" . $lang["users_settings_instructions"] . "</p><br />";
	
	echo "<b>" . $lang["users_settings_registration"] . "</b><br /><br />";
	
	$thisdomain =  rstrtrim(str_replace("http://", "", baseurl), '/');
	echo "<input type='checkbox' name='rc_enabled' value='enabled' " . $recaptcha_enabled . " >&nbsp;&nbsp;" . $lang["users_settings_recaptcha_enable"] . " <a href='http://recaptcha.net/api/getkey?domain=" . $thisdomain . "&app=HotaruCMS'>reCAPTCHA.net</a><br /><br />\n";	
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $lang["users_settings_recaptcha_public_key"] . ": <input type='text' name='rc_pubkey' value='" . $recaptcha_pubkey . "'><br /><br />\n";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $lang["users_settings_recaptcha_private_key"] . ": <input type='text' name='rc_privkey' value='" . $recaptcha_privkey . "'><br /><br />\n";
	echo "<input type='checkbox' name='emailconf' value='emailconf' " . $emailconf_enabled . ">&nbsp;&nbsp;" . $lang["users_settings_email_conf"] . "<br />\n";

	$plugin->check_actions('users_settings_form');
			
	echo "<br /><br />\n";	
	echo "<input type='hidden' name='submitted' value='true' />\n";
	echo "<input type='submit' value='" . $lang["users_settings_save"] . "' />\n";
	echo "</form>\n";
}


/* ******************************************************************** 
 *  Function: usr_save_settings
 *  Parameters: None
 *  Purpose: Takes updated Users settings and saves them in the pluginsettings database table.
 *  Notes: Also updates settings in Post class so we can easily reference them: if($userbase->use_recaptcha) ... etc.
 ********************************************************************** */
 
function usr_save_settings() {
	global $cage, $hotaru, $plugin, $userbase, $lang;

	// Recaptcha Enabled
	if($cage->post->keyExists('rc_enabled')) { 
		$recaptcha_enabled = 'checked'; 
		$userbase->userbase_vars['use_recaptcha'] = true;
	} else { 
		$recaptcha_enabled = ''; 
		$userbase->userbase_vars['use_recaptcha'] = false;
	}
	
	// Email Confirmation Enabled
	if($cage->post->keyExists('emailconf')) { 
		$emailconf_enabled = 'checked'; 
		$userbase->userbase_vars['use_emailconf'] = true;
	} else { 
		$emailconf_enabled = ''; 
		$userbase->userbase_vars['use_emailconf'] = false;
	}
	
	// ReCaptcha Public Key
	if($cage->post->keyExists('rc_pubkey')) { 
		$recaptcha_pubkey = $cage->post->getAlnum('rc_pubkey');
	} else { 
		$recaptcha_pubkey = "";
	}
	
	// ReCaptcha Private Key
	if($cage->post->keyExists('rc_privkey')) { 	
		$recaptcha_privkey = $cage->post->getAlnum('rc_privkey');
	} else { 
		$recaptcha_privkey = ""; 
	}
	
	
	$plugin->check_actions('users_save_settings');
	
	$plugin->plugin_settings_update('users', 'users_recaptcha_enabled', $recaptcha_enabled);	
	$plugin->plugin_settings_update('users', 'users_recaptcha_pubkey', $recaptcha_pubkey);	
	$plugin->plugin_settings_update('users', 'users_recaptcha_privkey', $recaptcha_privkey);
	$plugin->plugin_settings_update('users', 'users_emailconf_enabled', $emailconf_enabled);		
	
	if(($recaptcha_enabled == 'checked') && ($recaptcha_pubkey == "" || $recaptcha_privkey == "")) {
		$hotaru->message = $lang["users_settings_no_keys"];
		$hotaru->message_type = "red";
		$hotaru->show_message();
	} else {
		$hotaru->message = $lang["users_settings_saved"];
		$hotaru->message_type = "green";
		$hotaru->show_message();
	}
	
	return true;	
}
?>