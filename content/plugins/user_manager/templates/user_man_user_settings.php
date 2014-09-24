<?php
/**
 * Plugin name: User Manager
 * Template name: plugins/user_manager/user_man_user_settings.php
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

// set radio buttons plugin hook
$h->pluginHook('user_settings_fill_form'); 

?>

<!-- TITLE FOR USER MANAGER -->
<h2><?php echo $h->lang["user_man"]; ?></h2>

<?php echo $h->lang["user_man_user_settings_desc"]; ?>

<p id="user_man_navigation">
    <a href='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager&page=plugin_settings'><?php echo $h->lang["user_man"]; ?></a>&nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_perms'>" . $h->lang["user_man_default_perms"] . "</a>"; ?>&nbsp;&nbsp;
    <b><u><?php echo $h->lang["user_man_default_settings"]; ?></u></b> &nbsp;&nbsp;
    <?php echo "<a href='" . BASEURL . "admin_index.php?plugin=user_manager&page=plugin_settings&subpage=add_user'>" . $h->lang["user_man_add"] . "</a>"; ?>
</p>

<?php echo $h->showMessage(); ?>

    <form name='user_man_user_settings' action='<?php echo BASEURL; ?>admin_index.php' method='post'>
    <table class='user_settings'>
        <?php $h->pluginHook('user_settings_extra_settings'); ?>
        <?php if (!$h->vars['settings']) { $h->showMessage($h->lang['user_man_no_settings'], 'red'); } ?>
    </table>
    <div id='user_man_perms_existing_users_note'>
        <?php echo $h->lang["user_man_force_user_settings_desc"]; ?><br />
        <p><input type='checkbox' name='force_settings' value='force_settings' <?php echo $h->vars['user_man_user_settings_existing']; ?>>
        <?php echo $h->lang["user_man_force_user_settings"]; ?></p>
    </div>
    <input type='hidden' name='plugin' value='user_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='subpage' value='default_settings' />
    <input type='hidden' name='submitted' value='true' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    <div style='text-align: right'><input class='submit' id='user_man_submit' type='submit' value='<?php echo  $h->lang['main_form_update']; ?>' /></div>
    </form>

<div id='user_man_perms_revert_links'>
<p><a href='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_settings&revert=all'><?php echo $h->lang["user_man_revert_all_user_settings"]; ?></a> <?php echo $h->lang["user_man_revert_user_settings_note"]; ?></p>
<p><a href='<?php echo BASEURL; ?>admin_index.php?plugin=user_manager&page=plugin_settings&subpage=default_settings&revert=complete'><?php echo $h->lang["user_man_user_settings_trouble"]; ?></a> <?php echo $h->lang["user_man_user_settings_trouble_note"]; ?></p>
</div>

