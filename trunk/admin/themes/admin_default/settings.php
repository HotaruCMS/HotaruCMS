<?php 

/* ******* ADMIN TEMPLATE ************************************************************************** 
 * Theme name: admin_default
 * Template name: settings.php
 * Template author: Nick Ramsay
 * Version: 0.1
 * License:
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

global $hotaru, $plugin, $admin, $cage, $lang; // don't remove
?>

<h2><a href="<?php echo url(array(), 'admin'); ?>"><?php echo site_name;?> Admin Control Panel</a> &raquo; Settings</h2>

<?php 
	
	$plugin->check_actions('admin_settings_top'); 

	$plugin->show_message();	// display any success or failure messages
	
	$loaded_settings = $admin->get_all_admin_settings();	// get all admin settings from the database
	
	$error = 0;
	
	if($cage->post->noTags('settings_update')  == 'true') {
		foreach($loaded_settings as $setting_name) {
			if($cage->post->keyExists($setting_name->settings_name)) {
				$setting_value = $cage->post->noTags($setting_name->settings_name);
				if($setting_value && $setting_value != $setting_name->settings_value) {
					$admin->admin_setting_update($setting_name->settings_name, $setting_value);

				} else {
					if(!$setting_value) {
						// empty value 
						$error = 1; 
					} else { 
						// No change to the value
						$error = 0; 
					}
				}
			} else {
				// error, setting doesn't exist.
				$error = 1;
			}
		}
		
		if($error == 0) {
			$plugin->message = $lang['admin_settings_update_success'];
			$plugin->message_type = 'green';
			$plugin->show_message();		
		} else {
			$plugin->message = $lang['admin_settings_update_failure'];
			$plugin->message_type = 'red';
			$plugin->show_message();
		}
	}
	
	// Reload all the settings to display the most up-to-date form.
	$loaded_settings = $admin->get_all_admin_settings();	// get all admin settings from the database
	
?>
	
	<h1>Hotaru Settings</h1>
	<form id='settings_form' name='settings_form' action='<?php echo baseurl; ?>admin/admin_index.php?page=settings' method='post'>
	
	<table id="settings">	
	<tr><td><b><u>Setting</u></b></td><td><b><u>Value</u></b></td><td><b><u>Default</u></b></td><td><b><u>Notes</u></b></td></tr>
	<?php foreach($loaded_settings as $ls) { 
		$name = ucfirst(preg_replace('/_/', ' ', $ls->settings_name));
	?>
	<tr>
	<td><?php echo $name; ?>: </td><td><input type='text' size=20 name='<?php echo $ls->settings_name; ?>' value='<?php echo $ls->settings_value; ?>' /></td><td><?php echo $ls->settings_default; ?></td><td><i><?php echo $ls->settings_note; ?></i></td>
	</tr>
	<?php } ?>
	<br />
	<input type='hidden' name='settings_update' value='true' />
	</table>
	<input id='settings_submit' type='submit' value='Save' />
	</form>
	
	
	
<?php $plugin->check_actions('admin_settings_bottom'); ?>
