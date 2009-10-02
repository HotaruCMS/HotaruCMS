<?php 
/**
 * Theme name: admin_default
 * Template name: sidebar.php
 * Template author: Nick Ramsay
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
<?php $admin->plugins->pluginHook('admin_sidebar_top'); ?>

<ul id="sidebar">
    <li><a href="<?php echo url(array(), 'admin'); ?>"><?php echo $admin->lang["admin_theme_main_admin_home"]; ?></a></li>
    <?php if ($admin->current_user->loggedIn == true) { ?>
        <li><a href="<?php echo url(array('page' => 'admin_account'), 'admin'); ?>"><?php echo $admin->lang["admin_theme_account"]; ?></a></li>
    <?php } ?>
    <li><a href="<?php echo url(array('page' => 'settings'), 'admin'); ?>"><?php echo $admin->lang["admin_theme_settings"]; ?></a></li>
    <li><a href="<?php echo url(array('page' => 'maintenance'), 'admin'); ?>"><?php echo $admin->lang["admin_theme_maintenance"]; ?></a></li>
    <li><a href="<?php echo url(array('page' => 'blocked_list'), 'admin'); ?>"><?php echo $admin->lang["admin_theme_blocked_list"]; ?></a></li>
    <li><a href="<?php echo url(array('page' => 'plugins'), 'admin'); ?>"><?php echo $admin->lang["admin_theme_plugins"]; ?></a></li>
    <li><?php echo $admin->lang["admin_theme_plugin_settings"]; ?></li>
    <ul id="plugin_settings_list">
        <?php $admin->plugins->pluginHook('admin_sidebar_plugin_settings'); ?>
    </ul>
    
    <?php $admin->plugins->pluginHook('admin_sidebar'); ?>
</ul>

<?php $admin->plugins->pluginHook('admin_sidebar_bottom'); ?>
