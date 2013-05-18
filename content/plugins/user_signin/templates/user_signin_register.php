<?php
/**
 * Users Register
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
 
if ($h->cage->post->getAlpha('users_type') == 'register') {
    $username_check = $h->cage->post->testUsername('username');
    $password_check = "";
    $password2_check = "";
    $email_check = $h->cage->post->testEmail('email');    
} else {
    $username_check = "";
    $password_check = "";
    $password2_check = "";
    $email_check = "";
}
?>
    <h2><?php echo $h->lang["user_signin_register"]; ?></h2>
    
    <?php echo $h->showMessages(); ?>
    
    <?php $h->pluginHook('user_signin_register_pre_register_form'); ?>
     
	<?php echo $h->lang["user_signin_register_instructions"]; ?>
	<form class="form-horizontal" action='<?php echo BASEURL; ?>index.php?page=register' method='post'>
		<div class="control-group">
			<label class="control-label" for="username"><?php echo $h->lang["user_signin_register_username"]; ?></label>
			<div class="controls">
				<input type="text" name="username" value="<?php echo $username_check; ?>">
				<span class="help-block"><?php echo $h->lang["user_signin_register_username_error_short"]; ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="email"><?php echo $h->lang["user_signin_register_email"]; ?></label>
			<div class="controls">
				<input type="text" name="email" value="<?php echo $email_check; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password"><?php echo $h->lang["user_signin_register_password"]; ?></label>
			<div class="controls">
				<input type="password" name="password" value="<?php echo $password_check; ?>">
				<span class="help-block"><?php echo $h->lang["user_signin_register_password_error_short"]; ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password2"><?php echo $h->lang["user_signin_register_password_verify"]; ?></label>
			<div class="controls">
				<input type="password" name="password2" value="<?php echo $password2_check; ?>">
			</div>
		</div>
		<?php $h->pluginHook('user_signin_register_register_form'); ?>
		<?php if ($h->vars['useRecaptcha']) { ?>
                <?php $h->pluginHook('show_recaptcha'); ?>
        <?php  } ?>
		<div class="control-group">
			<div class="controls">
				<input type='hidden' name='users_type' value='register' />
				<input type='hidden' name='page' value='register'>
				<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
				<input type="submit" class="btn btn-primary" value="<?php echo $h->lang['user_signin_register_form_submit']; ?>" />
			</div>
		</div>
	</form>
