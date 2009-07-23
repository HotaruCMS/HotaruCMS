<?php

/* **************************************************************************************************** 
 *  File: /plugins/users/register.php
 *  Purpose: Allows a user to register.
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
 
global $hotaru, $cage, $lang;
	
if($cage->post->getAlpha('users_type') == 'register') {
	$username_check = $cage->post->testUsername('username');
	$password_check = $cage->post->testPassword('password');
	$email_check = $cage->post->testEmail('email');	
} else {
	$username_check = "";
	$password_check = "";
	$email_check = "";
}
?>

	<div id='main'>
		<p class='breadcrumbs'><a href='<?php echo baseurl ?>'><?php echo $lang["users_home"] ?></a> &raquo; <?php echo $lang["users_register"] ?></p>
			
		<h2><?php echo $lang["users_register"] ?></h2>
		
		<?php echo $hotaru->show_message(); ?>
			
		<div class='main_inner'>
		<?php echo $lang["users_register_instructions"] ?>
				
			<form name='register_form' action='<?php echo baseurl ?>index.php?page=register' method='post'>	
			<table>
			<tr><td><?php echo $lang["users_register_username"] ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $username_check ?>' /></td></tr>
			<tr><td><?php echo $lang["users_register_email"] ?>&nbsp; </td><td><input type='text' size=30 name='email' value='<?php echo $email_check ?>' /></td></tr>
			<tr><td><?php echo $lang["users_register_password"] ?>&nbsp; </td><td><input type='password' size=30 name='password' value='<?php echo $password_check ?>' /></td></tr>
			<input type='hidden' name='users_type' value='register' />
			<tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='<?php echo $lang['users_register_form_submit'] ?>' /></td></tr>			
			</table>
			</form>
		</div>
	</div>	
