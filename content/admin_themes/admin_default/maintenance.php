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
 * @copyright Copyright (c) 2010, Hotaru CMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://www.hotarucms.org/
 */

$plugin_settings = $h->vars['admin_plugin_settings'];
$db_tables = $h->vars['admin_plugin_tables'];
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
	
	<br />
	<?php echo $h->lang["admin_theme_maintenance_announcement"]; ?>
	
	<form name='maintenance_announcement' action='<?php echo BASEURL; ?>admin_index.php' method='get'>    
	<table>
		<tr>
		<td colspan=2><textarea name='announcement_text' cols=80 rows=3><?php echo $h->vars['admin_announcement']; ?></textarea><br />
		<?php echo $h->lang["admin_theme_maintenance_announcement_tags"]; ?>
		</td>
		</tr>
		<tr>
		<td><input type='checkbox' name='announcement_enabled' value='announcement_enabled' <?php echo $h->vars['admin_announcement_enabled']; ?>>
			<?php echo $h->lang["admin_theme_maintenance_announcement_enable"]; ?></td>
		<td style='text-align:right;'><input type='submit' value='<?php echo $h->lang['main_form_submit']; ?>' /></td>
		</tr>
	</table>
	<input type='hidden' name='action' value='announcement'>
	<input type='hidden' name='page' value='maintenance'>
	<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
	</form>
</ul>

<h2><?php echo $h->lang["admin_theme_maintenance_cache"]; ?></h2>
<ul>
	<li style="margin-bottom: 1em;"><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=clear_all_cache">
		<?php echo $h->lang["admin_theme_maintenance_all_cache"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_all_cache_desc"]; ?></li>
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

<h2><?php echo $h->lang["admin_theme_maintenance_debug"]; ?></h2>
<ul>
	<li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=delete_debugs">
		<?php echo $h->lang["admin_theme_maintenance_debug_delete"]; ?></a></li>
	<li style="margin-bottom: 1em;"><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=system_report">
		<?php echo $h->lang["admin_theme_maintenance_system_report"]; ?></a></li>
	<li style="margin-bottom: 1em;"><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=email_report">
		<?php echo $h->lang["admin_theme_maintenance_email_system_report"]; ?></a>
		<?php echo $h->lang["admin_theme_maintenance_email_system_report_note"]; ?></li>
</ul>

<?php if ($h->vars['debug_files']) {
			echo $h->lang["admin_theme_maintenance_debug_view"] . "<br />";
			foreach ($h->vars['debug_files'] as $file) {
				echo "<a href='" . BASEURL . "admin_index.php?page=maintenance&amp;debug=" . $file . "'>" . $file . "</a><br />";
			}
		} else {
			echo $h->lang["admin_theme_maintenance_debug_no_files"];
		}
?>
<br />

<h2><?php echo $h->lang["admin_theme_maintenance_optimize"]; ?></h2>
<ul>
	<li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=optimize">
		<?php echo $h->lang["admin_theme_maintenance_optimize_database"]; ?></a> - <?php echo $h->lang["admin_theme_maintenance_optimize_desc"]; ?></li>
	<?php $h->pluginHook('admin_maintenance_database'); ?>
</ul>

<?php $h->pluginHook('admin_maintenance_middle'); ?>

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
<h2><?php echo $h->lang["admin_theme_maintenance_db_tables"]; ?></h2>
<span style='color: red;'><?php echo $h->lang["admin_theme_maintenance_db_table_warning"]; ?></span><br /><br />
<?php echo $h->lang["admin_theme_maintenance_empty_explanation"]; ?><br /><br />
<ul>
<?php if($db_tables) { ?>
	<?php foreach ($db_tables as $table) { ?>
	<li><a href="<?php echo BASEURL; ?>admin_index.php?page=maintenance&amp;action=empty&amp;table=<?php echo $table; ?>">
		<?php echo $h->lang["admin_theme_maintenance_empty"] . " " . $table; ?> </a></li>
	<?php } ?>
<?php } else { ?>
	<i><?php echo $h->lang["admin_theme_maintenance_no_db_tables_to_empty"]; ?></i>
<?php } ?>
</ul>

<?php $h->pluginHook('admin_maintenance_bottom'); ?>
