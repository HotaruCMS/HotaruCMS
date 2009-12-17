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

<ul id="sidebar" class='<?php echo $hotaru->vars['admin_sidebar_layout']; ?>'>
    <li><a href="<?php echo $hotaru->url(array(), 'admin'); ?>"><?php echo $hotaru->lang["admin_theme_main_admin_home"]; ?></a></li>
    <?php if ($hotaru->currentUser->loggedIn == true) { ?>
        <li><a href="<?php echo BASEURL; ?>admin_index.php?page=admin_account"><?php echo $hotaru->lang["admin_theme_account"]; ?></a></li>
    <?php } ?>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=settings"><?php echo $hotaru->lang["admin_theme_settings"]; ?></a></li>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance"><?php echo $hotaru->lang["admin_theme_maintenance"]; ?></a></li>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=blocked_list"><?php echo $hotaru->lang["admin_theme_blocked_list"]; ?></a></li>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_management"><?php echo $hotaru->lang["admin_theme_plugins"]; ?></a></li>
    
    <?php if ($hotaru->vars['admin_sidebar_layout'] == 'horizontal') { ?>
        <li><a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings"><?php echo $hotaru->lang["admin_theme_plugin_settings"]; ?></a></li>
    <?php } else { ?>
        <li><?php echo $hotaru->lang["admin_theme_plugin_settings"]; ?></li>
        <ul id="plugin_settings_list">
            <?php 
                $sb_links = $hotaru->pluginHook('admin_sidebar_plugin_settings');
                if ($sb_links) {
                    $sb_links = sksort($sb_links, $subkey="name", $type="char", true);
                    foreach ($sb_links as $plugin => $details) { 
                        echo "<li><a href='" . BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=" . $details['plugin'] . "'>" . $details['name'] . "</a></li>";
                    }
                }
            ?>
        </ul>
    <?php } ?>
    
    <?php $hotaru->pluginHook('admin_sidebar'); ?>
</ul>
