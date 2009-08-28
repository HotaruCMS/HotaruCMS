<?php 
/**
 * Theme name: admin_default
 * Template name: maintenance.php
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

global $plugin, $admin, $cage, $lang; // don't remove
?>

<p class="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    &raquo; <a href="<?php echo url(array(), 'admin'); ?>"><?php echo $lang["admin_theme_main_admin_cp"]; ?></a> 
    &raquo; <?php echo $lang["admin_theme_maintenance"]; ?>
</p>
    
<h2><?php echo $lang["admin_theme_maintenance_title"]; ?></h2>
    
<?php $plugin->check_actions('admin_maintenance_top'); ?>

<?php
    if ($action = $cage->get->testAlnumLines('action')) {
        if ($action == 'clear_db_cache') { $admin->clear_cache('db_cache'); }
        if ($action == 'clear_css_js_cache') { $admin->clear_cache('css_js_cache'); }
        if ($action == 'clear_rss_cache') { $admin->clear_cache('rss_cache'); }
        if ($action == 'optimize') { $admin->optimize_tables(); }
        if ($action == 'empty') { $admin->empty_table($cage->get->testAlnumLines('table')); }
        if ($action == 'drop') { $admin->drop_table($cage->get->testAlnumLines('table')); }
    }
?>


<br />
<h2><?php echo $lang["admin_theme_maintenance_cache"]; ?></h2>
<ul>
    <li><a href="<?php echo BASEURL; ?>admin/admin_index.php?page=maintenance&amp;action=clear_db_cache">
        <?php echo $lang["admin_theme_maintenance_db_cache"]; ?></a> - <?php echo $lang["admin_theme_maintenance_db_cache_desc"]; ?></li>
    <li><a href="<?php echo BASEURL; ?>admin/admin_index.php?page=maintenance&amp;action=clear_css_js_cache">
        <?php echo $lang["admin_theme_maintenance_css_js_cache"]; ?></a> - <?php echo $lang["admin_theme_maintenance_css_js_cache_desc"]; ?></li>
    <li><a href="<?php echo BASEURL; ?>admin/admin_index.php?page=maintenance&amp;action=clear_rss_cache">
        <?php echo $lang["admin_theme_maintenance_rss_cache"]; ?></a> - <?php echo $lang["admin_theme_maintenance_rss_cache_desc"]; ?></li>
</ul>

<br />
<h2><?php echo $lang["admin_theme_maintenance_database"]; ?></h2>
<ul>
    <li><a href="<?php echo BASEURL; ?>admin/admin_index.php?page=maintenance&amp;action=optimize">
        <?php echo $lang["admin_theme_maintenance_optimize"]; ?></a> - <?php echo $lang["admin_theme_maintenance_optimize_desc"]; ?></li>
</ul>

<?php $plugin_tables = $admin->list_plugin_tables(); ?>
<br />
<h2><?php echo $lang["admin_theme_maintenance_plugin_tables"]; ?></h2>
<?php echo $lang["admin_theme_maintenance_plugin_table_explanation"]; ?><br /><br />
<span style='color: red;'><?php echo $lang["admin_theme_maintenance_plugin_table_warning"]; ?></span><br /><br />
<span style='color: red;'><?php echo $lang["admin_theme_maintenance_plugin_table_warning2"]; ?></span><br /><br />
<?php echo $lang["admin_theme_maintenance_empty_explanation"]; ?><br /><br />
<ul>
<?php if($plugin_tables) { ?>
    <?php foreach ($plugin_tables as $table) { ?>
    <li><a href="<?php echo BASEURL; ?>admin/admin_index.php?page=maintenance&amp;action=empty&amp;table=<?php echo $table; ?>">
        <?php echo $lang["admin_theme_maintenance_empty"] . " " . $table; ?> </a></li>
    <?php } ?>
<?php } else { ?>
    <?php echo $lang["admin_theme_maintenance_no_plugin_tables_to_empty"]; ?>
<?php } ?>
</ul>
<br />
<?php echo $lang["admin_theme_maintenance_drop_explanation"]; ?><br /><br />
<ul>
<?php if($plugin_tables) { ?>
    <?php foreach ($plugin_tables as $table) { ?>
    <li><a href="<?php echo BASEURL; ?>admin/admin_index.php?page=maintenance&amp;action=drop&amp;table=<?php echo $table; ?>">
        <?php echo $lang["admin_theme_maintenance_drop"] . " " . $table; ?> </a></li>
    <?php } ?>
<?php } else { ?>
    <?php echo $lang["admin_theme_maintenance_no_plugin_tables_to_drop"]; ?>
<?php } ?>
</ul>
    
<?php $plugin->check_actions('admin_maintenance_bottom'); ?>
