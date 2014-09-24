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
 * @author    Hotaru CMS Team
 * @copyright Copyright (c) 2009 - 2013, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

/* check for account updates */
//    $h->vars['checks'] = $h->vars['user']->updateAccount($h);
//    $h->vars['user']->name = $h->vars['checks']['username_check'];           
 
// ****************** was in users page at theme_index_top

extract($h->vars['checks']); // extracts $username_check, etc.
$username = $username_check; // used for user_tabs template
if ($username_check == 'deleted') { $h->showMessage(); return true; } // shows "User deleted" notice

?>
<div id="users_account" class="users_content">

    <h2><?php echo $h->lang("users_account"); ?>: <?php echo $username; ?></h2>
    
    <?php echo $h->showMessages(); ?>

    <form name='update_form' class='users_form' action='<?php echo BASEURL; ?>index.php?page=account' method='post'>    
    <table>
    <tr><td><?php echo $h->lang["users_account_username"]; ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $username; ?>' /></td></tr>
    <tr><td colspan='2'><small><?php echo $h->lang["users_account_username_requirements"]; ?></small></td></tr>
    <tr><td><?php echo $h->lang["users_account_email"]; ?>&nbsp; </td><td><input type='text' size=30 name='email' value='<?php echo $email_check; ?>' /></td></tr>
    <?php 
        // show role picker to anyone who can access admin, but not to yourself!
        if (($h->currentUser->getPermission('can_access_admin') == 'yes') 
        && ($h->currentUser->id != $userid_check)) { 
    ?>
        <tr><td colspan=2><?php echo $h->lang["users_account_role_note"]; ?></td></tr>
        <tr><td><?php echo $h->lang["users_account_role"]; ?>&nbsp; </td>
        <td><select name='user_role'>
                <option value='<?php echo $role_check; ?>'><?php echo $role_check; ?></option>
                <?php 
                    $roles = $h->getUniqueRoles(); 
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
    <?php } else { // your own role as a hidden field:?>
        <input type='hidden' name='user_role' value='<?php echo $role_check; ?>' />
    <?php } ?>
    
    <input type='hidden' name='userid' value='<?php echo $userid_check; ?>' />
    <input type='hidden' name='page' value='account' />
    <input type='hidden' name='update_type' value='update_general' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' class='submit' value='<?php echo $h->lang['users_account_update']; ?>' /></td></tr>
    </table>    
    </form>
    
    <?php $h->pluginHook('users_account_pre_password'); ?>
    
    <?php if ($h->vars['user']->id == $h->currentUser->id) { // must be looking at own account so show password change form: ?>
    
        <?php $h->pluginHook('users_account_pre_password_user_only'); ?>
            
        <b><?php echo $h->lang["users_account_password_instruct"]; ?></b>
        <form name='update_form' class='users_form' action='<?php echo BASEURL; ?>index.php' method='post'>
        <table>
        <tr><td colspan='2'><small><?php echo $h->lang["users_account_password_requirements"]; ?></small></td></tr>
        <tr><td><?php echo $h->lang["users_account_old_password"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_old' value='<?php echo $password_check_old; ?>' /></td></tr>
        <tr><td><?php echo $h->lang["users_account_new_password"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_new' value='<?php echo $password_check_new; ?>' /></td></tr>
        <tr><td><?php echo $h->lang["users_account_new_password_verify"]; ?>&nbsp; </td><td><input type='password' size=30 name='password_new2' value='<?php echo $password_check_new2; ?>' /></td></tr>
        <input type='hidden' name='userid' value='<?php echo $userid_check; ?>' />
        <input type='hidden' name='page' value='account' />
        <input type='hidden' name='update_type' value='update_password' />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        <tr><td>&nbsp;</td><td style='text-align:right;'><input type='submit' class='submit' value='<?php echo $h->lang['users_account_update']; ?>' /></td></tr>            
        </table>
        </form>
    <?php } ?>
</div>