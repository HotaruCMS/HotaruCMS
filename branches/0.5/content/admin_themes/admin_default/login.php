<?php 

/* ******* ADMIN TEMPLATE ************************************************************************** 
 * Theme name: admin_default
 * Template name: login.php
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

global $hotaru, $lang; // don't remove
?>

<p class="breadcrumbs">
	<a href="<?php echo baseurl; ?>"><?php echo site_name?></a> 
	&raquo; <a href="<?php echo url(array(), 'admin'); ?>"><?php echo $lang["admin_theme_main_admin_cp"]?></a> 
	&raquo; <?php echo $lang["admin_theme_login"]; ?>
</p>
		
<?php $hotaru->show_message(); ?>
		
<div class='main_inner'>
	<?php echo $lang["admin_theme_login_instructions"] ?>
	
	<form name='login_form' action='<?php echo baseurl ?>admin/admin_index.php' method='post'>	
	<table>
		<tr>
		<td><?php echo $lang["admin_theme_login_username"] ?>:&nbsp; </td>
		<td><input type='text' size=30 name='username' value='<?php echo $username_check ?>' /></td>
		</tr>
		<tr>
		<td><?php echo $lang["admin_theme_login_password"] ?>:&nbsp; </td>
		<td><input type='password' size=30 name='password' value='<?php echo $password_check ?>' /></td>
		</tr>
		<tr>
		<td>&nbsp; </td>
		<td style='text-align:right;'><input type='submit' value='<?php echo $lang['admin_theme_login_form_submit'] ?>' /></td>
		</tr>			
	</table>
	<input type='hidden' name='login_attempted' value='true'>
	<input type='hidden' name='page' value='admin_login'>
	</form>
</div>