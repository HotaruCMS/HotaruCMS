<?php 
/**
 * Theme name: admin_default
 * Template name: plugins.php
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
$h->pluginHook('plugins_top');

$h->template('admin_sidebar');

$h->showMessages();

$the_plugins = isset($h->vars['installed_plugins']) ? $h->vars['installed_plugins'] : array();

// Build tabs and content pages
$tabs = array(
    array('Install', array('the_plugins' => $the_plugins)),   // for some reason, even though we pass the array here it gets lost somehow in the include 
    'Updates',
    'Search',
    'Help'
 );

buildTabs($h, 'plugin_management', $tabs);

?>