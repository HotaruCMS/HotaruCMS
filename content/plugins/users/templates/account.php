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
extract($hotaru->vars['checks']); // extracts $username_check, etc.
$username = $hotaru->vars['username'] = $username_check; // used for user_tabs template
if ($username_check == 'deleted') { $hotaru->showMessage(); return true; } // shows "User deleted" notice
?>
    
    <div id='breadcrumbs'><a href='<?php echo BASEURL; ?>'><?php echo $hotaru->lang["users_home"]; ?></a> 
        &raquo; <a href='<?php echo $hotaru->url(array('user' => $username)); ?>'><?php echo $username; ?></a> 
        &raquo; <?php echo $hotaru->lang["users_account"]; ?></div>
    
    <?php $hotaru->displayTemplate('user_tabs', 'users'); ?>
    
    <h2><?php echo $hotaru->lang["users_account"]; ?>: <?php echo $username; ?></h2>
    
    <?php echo $hotaru->showMessages(); ?>

    <form name='update_form' class='users_form' action='<?php echo BASEURL; ?>index.php' method='post'>    
    <table>
    <tr><td><?php echo $hotaru->lang["users_account_username"]; ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $username; ?>' /></td></tr>
    <tr><td><?php echo $hotaru->lang["users_account_email"]; ?>&nbsp; </td><td><input type='text' size=30 name='email' value='<?php echo $email_check; ?>' /></td></tr>
    <?php 
        // show role picker to anyone who can access admin, but not to yourself!
        if (($hotaru->current_user->getPermission('can_access_admin') == 'yes') 
        && ($hotaru->current_user->name != $username)) { 
    ?>
        <tr><td colspan=2><?php echo $hotaru->lang["users_account_role_note"]; ?></td></tr>
        <tr><td><?php echo $hotaru->lang["users_account_role"]; ?>&nbsp; </td>
        <td><select name='user_role'>
                <option value='<?php echo $role_check; ?>'><?php echo $role_check; ?></option>
                <?php 
                    $roles = $hotaru->current_user->getUniqueRoles(); 
                    if ($roles) {
                        foreach ($roles as $role) {
                            if ($role != $role_check) {
                                echo "<option value='" . $role . "'>" . $role . "</option>\n";
                            }
                        }
                    }
                ?>
            </select>
        </td>
    <?php } ?>
    <input type='hidden' name='userid' value='<?php echo $hotaru->vars['userid']; ?>' />
    <input type='hidden' name='page' value='account' />
    <input type='hidden' name='update_type' value='update_general' />
    <input type='hidden' name='token' value='<?php echo $hotaru->token; ?>' />
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' class='submit' value='<?php echo $hotaru->lang['users_account_update']; ?>' /></td></tr>
    </table>    
    </form>
    
    <?php $hotaru->plugins->pluginHook('users_account_pre_password'); ?>
    
    <?php if ($hotaru->vars['userid'] == $hotaru->current_user->id) { // must be looking at own account so show password change form: ?>
    
        <?php $hotaru->plugins->pluginHook('users_account_pre_password_user_only'); ?>
            
        <b><?php echo $hotaru->lang["users_account_password_instruct"]; ?></b>
        <form name='update_form' class='users_form' action='<?php echo BASEURL; ?>index.php' method='post'>
        <table>
        <tr><td><?php echo $hotaru->lang["users_account_old_password"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_old' value='<?php echo $password_check_old; ?>' /></td></tr>
        <tr><td><?php echo $hotaru->lang["users_account_new_password"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_new' value='<?php echo $password_check_new; ?>' /></td></tr>
        <tr><td><?php echo $hotaru->lang["users_account_new_password_verify"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_new2' value='<?php echo $password_check_new2; ?>' /></td></tr>
        <input type='hidden' name='userid' value='<?php echo $hotaru->vars['userid']; ?>' />
        <input type='hidden' name='page' value='account' />
        <input type='hidden' name='update_type' value='update_password' />
        <input type='hidden' name='token' value='<?php echo $hotaru->token; ?>' />
        <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' class='submit' value='<?php echo $hotaru->lang['users_account_update']; ?>' /></td></tr>            
        </table>
        </form>
    <?php } ?>

