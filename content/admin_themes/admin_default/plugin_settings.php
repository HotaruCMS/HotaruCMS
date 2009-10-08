<?php 
/**
 * Theme name: admin_default
 * Template name: plugin_settings.php
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

$this->plugins->folder = $this->cage->get->testAlnumLines('plugin'); // get plugin name from url
$this->plugins->getPluginName();
?>

<p class="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    &raquo; <a href="<?php echo $admin->hotaru->url(array(), 'admin'); ?>"><?php echo $admin->lang["admin_theme_main_admin_cp"]; ?></a> 
    &raquo; <?php echo $admin->lang["admin_theme_plugin_settings"]; ?> 
    <?php if ($admin->plugins->name) { echo "&raquo; " .  $admin->plugins->name; } ?>
</p>

<div id="plugin_settings">
    <?php 
        if ($admin->plugins->folder == "") {
            $admin->plugins->pluginHook('admin_sidebar_plugin_settings');
        } else {
            $admin->plugins->pluginHook('admin_plugin_settings', true, $admin->plugins->folder); 
        }
    ?>
</div>

