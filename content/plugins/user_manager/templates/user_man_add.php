<?php
/**
 * Plugin name: User Manager
 * Template name: plugins/user_manager/user_man_add.php
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

<h2><?php echo $h->lang['user_man_add']; ?></h2>

<?php echo $h->lang["user_man_add_desc"]; ?>

<p id="user_man_navigation">
    <a href='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager&page=plugin_settings'><?php echo $h->lang["user_man"]; ?></a>&nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_perms'>" . $h->lang["user_man_default_perms"] . "</a>"; ?>&nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_settings'>" . $h->lang["user_man_default_settings"] . "</a>"; ?> &nbsp;&nbsp;
    <b><u><?php echo $h->lang["user_man_add"]; ?></u></b>
</p>

<?php echo $h->showMessages(); ?>

<div id="user_man_add_user">

    <!-- REQUEST CREATE NEW USER -->
    <form name='user_man_add_user' action='<?php echo BASEURL; ?>admin_index.php' method='post'>
    <h3><?php echo $h->lang['user_man_add_new_user']; ?></h3>
    <p><?php echo $h->lang["user_man_add_detail"]; ?></p>
        <table class='user_man_add'>
            <tr><td>
                <?php if (!isset($h->vars['user_man_username_1'])) { $h->vars['user_man_username_1'] = ''; } ?>
                <?php echo $h->lang["user_signin_register_username"]; ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $h->vars['user_man_username_1']; ?>' />
                <br /><small><?php echo $h->lang["user_signin_register_username_error_short"]; ?></small>
            </td></tr>
            
            <tr><td>
                <?php if (!isset($h->vars['user_man_email'])) { $h->vars['user_man_email'] = ''; } ?>
                <?php echo $h->lang["user_signin_register_email"]; ?>&nbsp; </td><td><input type='text' size=30 name='email' value='<?php echo $h->vars['user_man_email']; ?>' />
            </td></tr>
        </table>
        <input type='hidden' name='plugin' value='user_manager' />
        <input type='hidden' name='page' value='plugin_settings' />
        <input type='hidden' name='subpage' value='add_user' />
        <input type='hidden' name='submitted' value='new_user' />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        <div style='text-align: right'><input class='submit' id='user_man_submit' type='submit' value='<?php echo  $h->lang['user_man_create_send']; ?>' /></div>
    </form>
    
    <!-- SEND NEW PASSWORD -->
    <form name='user_man_send_password' action='<?php echo BASEURL; ?>admin_index.php' method='post'>
        <h3><?php echo $h->lang['user_man_send_new_password']; ?></h3>
        <p><?php echo $h->lang["user_man_send_password_detail"]; ?></p>
        <table class='user_man_send_password'>
            <tr><td>
                <?php if (!isset($h->vars['user_man_username_2'])) { $h->vars['user_man_username_2'] = ''; } ?>
                <?php echo $h->lang["user_signin_register_username"]; ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $h->vars['user_man_username_2']; ?>' />
            </td></tr>
        </table>
        <input type='hidden' name='plugin' value='user_manager' />
        <input type='hidden' name='page' value='plugin_settings' />
        <input type='hidden' name='subpage' value='add_user' />
        <input type='hidden' name='submitted' value='new_password' />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        <div style='text-align: right'><input class='submit' id='user_man_submit' type='submit' value='<?php echo  $h->lang['user_man_send_password']; ?>' /></div>
    </form>
    
    <!-- REQUEST EMAIL VAIDATON -->
    <form name='user_man_send_email_validation' action='<?php echo BASEURL; ?>admin_index.php' method='post'>
        <h3><?php echo $h->lang['user_man_send_email_validation']; ?></h3>
        <p><?php echo $h->lang["user_man_send_email_validation_detail"]; ?></p>
        <table class='user_man_email_valid'>
            <tr><td>
                <?php if (!isset($h->vars['user_man_username_3'])) { $h->vars['user_man_username_3'] = ''; } ?>
                <?php echo $h->lang["user_signin_register_username"]; ?>&nbsp; </td><td><input type='text' size=30 name='username' value='<?php echo $h->vars['user_man_username_3']; ?>' />
            </td></tr>
        </table>
        <input type='hidden' name='plugin' value='user_manager' />
        <input type='hidden' name='page' value='plugin_settings' />
        <input type='hidden' name='subpage' value='add_user' />
        <input type='hidden' name='submitted' value='email_validation' />
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        <div style='text-align: right'><input class='submit' id='user_man_submit' type='submit' value='<?php echo  $h->lang['user_man_request_valid_email']; ?>' /></div>
    </form>

	<?php $h->pluginHook('user_man_tools'); ?>
</div>