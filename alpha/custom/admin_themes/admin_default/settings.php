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

global $plugin, $admin, $cage, $lang; // don't remove
?>

<p class="breadcrumbs">
	<a href="<?php echo baseurl; ?>"><?php echo site_name?></a> 
	&raquo; <a href="<?php echo url(array(), 'admin'); ?>"><?php echo $lang["admin_theme_main_admin_cp"]?></a> 
	&raquo; <?php echo $lang["admin_theme_settings"]; ?>
</p>

<?php $plugin->check_actions('admin_settings_top'); ?>
	
	<h2><?php echo $lang["admin_theme_settings_title"] ?></h2>
	
	<?php $loaded_settings = $admin->settings();	// Prepare or process the form ?>
	
	<form id='settings_form' name='settings_form' action='<?php echo baseurl; ?>admin/admin_index.php?page=settings' method='post'>
	
	<table id="settings">	
	<tr>
		<td><b><u><?php echo $lang["admin_theme_settings_setting"] ?></u></b></td>
		<td><b><u><?php echo $lang["admin_theme_settings_value"] ?></u></b></td>
		<td><b><u><?php echo $lang["admin_theme_settings_default"] ?></u></b></td>
		<td><b><u><?php echo $lang["admin_theme_settings_notes"] ?></u></b></td>
	</tr>
	
	<?php 	// **********************************************************
	
		// Loop through the settings, displaying each one as a row...	
		foreach($loaded_settings as $ls) { 
		
			// replace underscores with spaces and make the first character of the setting name uppercase.
			$name = ucfirst(preg_replace('/_/', ' ', $ls->settings_name));	
		?>
			<tr>
			<td><?php echo $name; ?>: </td>
			<td><input type='text' size=20 name='<?php echo $ls->settings_name; ?>' value='<?php echo $ls->settings_value; ?>' /></td>
			<td><?php echo $ls->settings_default; ?></td>
			<td><i><?php echo $ls->settings_note; ?></i></td>
			</tr>
	 
	<?php 	} // End loop ********************************************************** 	?>
	
	<br />
	<input type='hidden' name='settings_update' value='true' />
	</table>
	<input id='settings_submit' type='submit' value='Save' />
	</form>
	
	
	
<?php $plugin->check_actions('admin_settings_bottom'); ?>
