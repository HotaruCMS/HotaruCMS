<?php
/**
 * Users Update Login, Email and Password
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
 
global $hotaru, $cage, $lang, $current_user;
        
if ($cage->post->testAlnumLines('users_type') == 'update_general') {    
    $username_check = $cage->post->testUsername('username');
    $email_check = $cage->post->testEmail('email');    
} else {
    $username_check = $current_user->username;
    $email_check = $current_user->email;
}

$password_check_old = "";
$password_check_new = "";
$password_check_new2 = "";

?>
    
    <div id='breadcrumbs'><a href='<?php echo BASEURL; ?>'><?php echo $lang["users_home"]; ?></a> &raquo; <?php echo $lang["users_update_user_settings"]; ?></div>
            
    <h2><?php echo $lang["users_update_user_settings"]; ?></h2>
    
    <?php echo $hotaru->show_messages(); ?>
    
    <?php echo $lang["users_update_instructions"]; ?>        
    <form name='update_form' action='<?php echo BASEURL; ?>index.php?page=profile' method='post'>    
    <table>
    <tr><td><?php echo $lang["users_update_username"]; ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $username_check; ?>' /></td></tr>
    <tr><td><?php echo $lang["users_update_email"]; ?>&nbsp; </td><td><input type='text' size=30 name='email' value='<?php echo $email_check; ?>' /></td></tr>
    <input type='hidden' name='users_type' value='update_general' />
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='<?php echo $lang['users_update_form_submit']; ?>' /></td></tr>
    </table>    
    </form>
    
    <?php echo $lang["users_update_password_instruct"]; ?>
    <form name='update_form' action='<?php echo BASEURL; ?>index.php?page=profile' method='post'>
    <table>
    <tr><td><?php echo $lang["users_update_old_password"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_old' value='<?php echo $password_check_old; ?>' /></td></tr>
    <tr><td><?php echo $lang["users_update_new_password"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_new' value='<?php echo $password_check_new; ?>' /></td></tr>
    <tr><td><?php echo $lang["users_update_new_password_verify"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_new2' value='<?php echo $password_check_new2; ?>' /></td></tr>
    <input type='hidden' name='users_type' value='update_password' />
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' value='<?php echo $lang['users_update_form_submit']; ?>' /></td></tr>            
    </table>
    </form>

