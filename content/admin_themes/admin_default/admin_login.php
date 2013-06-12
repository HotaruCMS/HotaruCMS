<?php 
/**
 * Theme name: admin_default
 * Template name: admin_login.php
 * Template author: shibuya246
 *
 * PHP version 5
 *
 * LICENSE: Hotaru CMS is free software: you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of 
 * the License, or (at your option) any later version. 
 *
 * Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * You should have received a copy of the GNU General Public License along 
 * with Hotaru CMS. If not, see http://www.gnu.org/licenses/.
 * 
 * @category  Content Management System
 * @package   HotaruCMS
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

?>

<?php $h->showMessage(); ?>

<?php //echo $h->lang("admin_theme_login_instructions"); ?>

<div id ="login_form">
    <center>

    <form name='login_form' action='<?php echo SITEURL; ?>admin_index.php' method='post'>
        <h3>Admin Login</h3>
        
	<table>
		<tr>
		<td><?php echo $h->lang("admin_theme_login_username"); ?>&nbsp; </td>
		<td><input id='admin_login_name' type='text' size=20 name='username' value='<?php echo $username_check; ?>' /></td>
		</tr>
		<tr>
		<td><?php echo $h->lang("admin_theme_login_password"); ?>&nbsp; </td>
		<td><input id='admin_login_password' type='password' size=20 name='password' value='<?php echo $password_check; ?>' /></td>
		</tr>
		<tr>
		<td>&nbsp; </td>
		<td style='text-align:right;'><input id='admin_login_button' type='submit' value='<?php echo $h->lang('admin_theme_login_form_submit'); ?>'  /></td>
		</tr>
	</table>

	<input type='hidden' name='login_attempted' value='true'>
	<input type='hidden' name='page' value='admin_login'>
	<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </form>
        
        <p><a href="<?php echo SITEURL; ?>">Back to Site</a></p>

    <a href="#" class="forgot_password"><?php echo $h->lang("admin_theme_login_forgot_password"); ?></a>
 </div>

<form style="display: none;" id='forgot_password_form' name='forgot_password_form' action='<?php echo SITEURL; ?>admin_index.php' method='post'>    
	<?php echo $h->lang('admin_theme_login_forgot_password_submit_instruct_1'); ?>
<table>
	<tr>
	<td><?php echo $h->lang("admin_theme_update_email"); ?>&nbsp; </td>
	<td><input type='text' size=30 name='email' value='<?php echo $email_check; ?>' /></td>
	<td><input type='submit' value='<?php echo $h->lang('admin_theme_login_forgot_password_submit'); ?>' /></td>
	</tr>
</table>
<input type='hidden' name='forgotten_password' value='true'>
<input type='hidden' name='page' value='admin_login'>
<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
	<?php echo $h->lang('admin_theme_login_forgot_password_submit_instruct_2'); ?>
</form>
</center>