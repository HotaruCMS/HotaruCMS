<?php
/**
 * Users Login
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
 * @author    Nick Ramsay <admin@hotarucms.org>
 * @copyright Copyright (c) 2009, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */
 
if (!$username_check = $h->cage->post->testUsername('username')) { $username_check = ""; } 
if (!$password_check = $h->cage->post->testPassword('password')) { $password_check = ""; }
$return_check = $h->cage->get->getHtmLawed('return');
if (!$return_check) { $return_check = $h->cage->post->getHtmLawed('return'); }
if (!$email_check = $h->cage->post->testEmail('email')) { $email_check = ""; }
if ($h->cage->post->getInt('remember') == 1){ $remember_check = "checked"; } else { $remember_check = ""; }

?>
    <h2><?php echo $h->lang["user_signin_login"]; ?></h2>
    
    <?php echo $h->showMessages(); ?>
    
    <?php $h->pluginHook('user_signin_login_pre_login_form'); ?>
    <?php //echo $h->lang["user_signin_login_instructions"]; ?>
	<form class="span5" name="login_form" action="<?php echo BASEURL; ?>index.php" method="post">
		<div class="control-group">
			<label class="control-label" for="inputUsername"><strong><?php echo $h->lang["user_signin_login_form_submit_username"]; ?></strong></label>
			<div class="controls">
				<input type="text" name="username" value="<?php echo $username_check; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="inputPassword"><strong><?php echo $h->lang["user_signin_login_form_submit_password"]; ?></strong></label>
			<div class="controls">
				<input type="password" name="password" value="<?php echo $password_check; ?>">
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label class="checkbox">
					<input type="checkbox" name="remember" value="1" <?php echo $remember_check; ?> /> <?php echo $h->lang["user_signin_login_form_submit_remember"]; ?>
				</label>
				<input type="hidden" name="page" value="login">
				<input type="hidden" name="return" value="<?php echo $return_check; ?>">
				<input type="hidden" name="csrf" value="<?php echo $h->csrfToken; ?>" />
				<input type="submit" class="btn btn-primary" value="<?php echo $h->lang['user_signin_login_form_submit']; ?>" />
			</div>
		</div>
		<a href="#" class="forgot_password"><?php echo $h->lang["user_signin_login_forgot_password"]; ?></a>
	</form>
   
    
    
    <form id="forgot_password_form" class="form-inline" style="display: none;" name='forgot_password_form' action='<?php echo BASEURL; ?>index.php' method='post'>
		<h4><?php echo $h->lang['user_signin_login_forgot_password_submit_instruct_1']; ?></h4>
		<strong><?php echo $h->lang["user_signin_account_email"]; ?></strong>
		<input type="email" name="email" value="<?php echo $email_check; ?>" />
		<input type="submit" class="btn" value="<?php echo $h->lang['user_signin_login_forgot_password_submit']; ?>" />
		<input type="hidden" name="forgotten_password" value="true">
		<input type="hidden" name="page" value="login">
		<input type="hidden" name="csrf" value="<?php echo $h->csrfToken; ?>" />
		<span class="help-block"><?php echo $h->lang['user_signin_login_forgot_password_submit_instruct_2']; ?></span>
    </form>