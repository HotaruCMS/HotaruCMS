<?php 
/**
 * Theme name: admin_default
 * Template name: maintenance.php
 * Template author: shibuya246
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

$plugin_settings = isset($h->vars['admin_plugin_settings']) ? $h->vars['admin_plugin_settings'] : '';
$db_tables = isset($h->vars['admin_plugin_tables']) ? $h->vars['admin_plugin_tables'] : '';

$h->showMessages();

// Hook above content
$h->pluginHook('admin_maintenance_top');

// Tabs and content pages
$tabs = array('General', 'Cache', 'Debug', array('Database', array('db_tables' => $db_tables, 'some' => 'ds')), 'Other');

buildtabs($h, 'maintenance', $tabs);

// Hook below content
$h->pluginHook('admin_maintenance_bottom');


?>