<?php
/**
 * Plugin name: User Manager
 * Template name: plugins/user_manager/user_man_perms.php
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
?>

<!-- TITLE FOR USER MANAGER -->
<h2><?php echo $h->lang["user_man"]; ?></h2>

<?php echo $h->lang["user_man_perms_desc"]; ?>

<p id="user_man_navigation">
    <a href='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager&page=plugin_settings'><?php echo $h->lang["user_man"]; ?></a>&nbsp;&nbsp;
    <b><u><?php echo $h->lang["user_man_default_perms"]; ?></u></b> &nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_settings'>" . $h->lang["user_man_default_settings"] . "</a>"; ?> &nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=add_user'>" . $h->lang["user_man_add"] . "</a>"; ?>
</p>

<p id="user_man_usergroup_links">
<?php 
    $roles = $h->getUniqueRoles();
    if ($roles) {
        foreach ($roles as $role) {
            if ($h->vars['user_man_role'] == $role) {
                $role_title = make_name($role);
                echo "<b><u>" . $role_title . "</u></b>&nbsp;&nbsp;";
            } else {
                echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_perms&role=" . $role . "'>" . make_name($role) . "</a>&nbsp;&nbsp;";
            }
        }
        
        if ($h->vars['user_man_role'] != 'default') {
            echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_perms&role=default'>Unregistered</a>";
        } else {
            echo "<b><u>Unregistered</u></b>";
            $role_title = "Unregistered";
        }
    }
?>
</p>

<?php echo $h->showMessage(); ?>

<p><?php echo $h->lang["user_man_default_perms_for"]; ?><u><b><?php echo $role_title; ?></b></u>:</p>

    <form name='user_man_permissions' action='<?php echo BASEURL; ?>admin_index.php' method='post'>
    <table class='permissions'>
        <?php echo $h->vars['perm_options']; ?>
        <?php if (!$h->vars['perm_options']) { $h->showMessage($h->lang['user_man_no_perms'], 'red'); } ?>
    </table>
    <?php if ($h->vars['user_man_role'] != 'default') { ?>
        <div id='user_man_perms_existing_users_note'>
            <?php echo $h->lang["user_man_apply_perms_desc"]; ?><br />
            <p><input type='checkbox' name='apply_perms' value='force_perms' <?php echo $h->vars['user_man_perms_existing']; ?>>
            <?php echo $h->lang["user_man_apply_perms"]; ?></p>
        </div>
    <?php } ?>
    <input type='hidden' name='plugin' value='user_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='subpage' value='default_perms' />
    <input type='hidden' name='submitted' value='true' />
    <input type='hidden' name='role' value='<?php echo $h->vars['user_man_role']; ?>' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    <div style='text-align: right'><input class='submit' id='user_man_submit' type='submit' value='<?php echo  $h->lang['main_form_update']; ?>' /></div>
    </form>

<div id='user_man_perms_revert_links'>
<p><a href='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_perms&role=<?php echo $h->vars['user_man_role']; ?>&revert=true'><?php echo $h->lang["user_man_revert_perms"]; ?></a> <?php echo $h->lang["user_man_revert_perms_note"]; ?></p>
<p><a href='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_perms&role=<?php echo $h->vars['user_man_role']; ?>&revert=all'><?php echo $h->lang["user_man_revert_all_perms"]; ?></a> <?php echo $h->lang["user_man_revert_perms_note"]; ?></p>
<p><a href='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_perms&role=<?php echo $h->vars['user_man_role']; ?>&revert=complete'><?php echo $h->lang["user_man_perms_trouble"]; ?></a> <?php echo $h->lang["user_man_perms_trouble_note"]; ?></p>
</div>

