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

$plugin_settings = $h->vars['admin_plugin_settings'];
$plugin_tables = $h->vars['admin_plugin_tables'];
?>
    
<h2><?php echo $h->lang["admin_theme_maintenance_title"]; ?></h2>

<?php $h->showMessage(); ?>

<?php $h->pluginHook('admin_maintenance_top'); ?>

<h2><?php echo $h->lang["admin_theme_maintenance_site"]; ?></h2>
<ul>
    <?php if (SITE_OPEN == "true") { ?>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=close">
        <?php echo $h->lang["admin_theme_maintenance_close_site"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_close_site_desc"]; ?></li>
    <?php } else { ?>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=open">
        <?php echo $h->lang["admin_theme_maintenance_open_site"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_open_site_desc"]; ?></li>
    <?php } ?>
</ul>

<br />
<h2><?php echo $h->lang["admin_theme_maintenance_cache"]; ?></h2>
<ul>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=clear_db_cache">
        <?php echo $h->lang["admin_theme_maintenance_db_cache"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_db_cache_desc"]; ?></li>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=clear_css_js_cache">
        <?php echo $h->lang["admin_theme_maintenance_css_js_cache"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_css_js_cache_desc"]; ?></li>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=clear_html_cache">
        <?php echo $h->lang["admin_theme_maintenance_html_cache"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_html_cache_desc"]; ?></li>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=clear_rss_cache">
        <?php echo $h->lang["admin_theme_maintenance_rss_cache"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_rss_cache_desc"]; ?></li>
</ul>

<br />
<h2><?php echo $h->lang["admin_theme_maintenance_database"]; ?></h2>
<ul>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=optimize">
        <?php echo $h->lang["admin_theme_maintenance_optimize"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_optimize_desc"]; ?></li>
    <?php $h->pluginHook('admin_maintenance_database'); ?>
</ul>

<br />
<h2><?php echo $h->lang["admin_theme_maintenance_plugin_settings"]; ?></h2>
<?php echo $h->lang["admin_theme_maintenance_plugin_settings_explanation"]; ?><br /><br />
<ul>
<?php if ($plugin_settings) { ?>
    <?php foreach ($plugin_settings as $settings) { ?>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=remove_settings&amp;settings=<?php echo $settings; ?>">
        <?php echo $h->lang["admin_theme_maintenance_remove"] . " " . make_name($settings) . " " . $h->lang["admin_theme_maintenance_settings"]; ?> </a></li>
    <?php } ?>
<?php } else { ?>
    <i><?php echo $h->lang["admin_theme_maintenance_no_plugin_settings_to_delete"]; ?></i>
<?php } ?>
</ul>

<br />
<h2><?php echo $h->lang["admin_theme_maintenance_plugin_tables"]; ?></h2>
<?php echo $h->lang["admin_theme_maintenance_plugin_table_explanation"]; ?><br /><br />
<span style='color: red;'><?php echo $h->lang["admin_theme_maintenance_plugin_table_warning"]; ?></span><br /><br />
<span style='color: red;'><?php echo $h->lang["admin_theme_maintenance_plugin_table_warning2"]; ?></span><br /><br />
<?php echo $h->lang["admin_theme_maintenance_empty_explanation"]; ?><br /><br />
<ul>
<?php if($plugin_tables) { ?>
    <?php foreach ($plugin_tables as $table) { ?>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=empty&amp;table=<?php echo $table; ?>">
        <?php echo $h->lang["admin_theme_maintenance_empty"] . " " . $table; ?> </a></li>
    <?php } ?>
<?php } else { ?>
    <i><?php echo $h->lang["admin_theme_maintenance_no_plugin_tables_to_empty"]; ?></i>
<?php } ?>
</ul>
<br />
<?php echo $h->lang["admin_theme_maintenance_drop_explanation"]; ?><br /><br />
<ul>
<?php if($plugin_tables) { ?>
    <?php foreach ($plugin_tables as $table) { ?>
    <li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=drop&amp;table=<?php echo $table; ?>">
        <?php echo $h->lang["admin_theme_maintenance_drop"] . " " . $table; ?> </a></li>
    <?php } ?>
<?php } else { ?>
    <i><?php echo $h->lang["admin_theme_maintenance_no_plugin_tables_to_drop"]; ?></i>
<?php } ?>
</ul>
    
<?php $h->pluginHook('admin_maintenance_bottom'); ?>
