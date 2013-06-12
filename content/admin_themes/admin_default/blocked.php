<?php 
/**
 * Theme name: admin_default
 * Template name: blocked.php
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
extract($h->vars['admin_blocked_list']); // extracts $output and $pagedResults;
?>

<!-- TITLE FOR ADMIN NEWS -->
<h2><?php echo $h->lang("admin_theme_blocked_list"); ?></h2>

<?php echo $h->lang("admin_theme_blocked_desc"); ?>

<?php echo $h->showMessage(); ?>

<form name='blocked_list_new_form' action='<?php echo SITEURL; ?>admin_index.php?page=blocked' method='post'>
	<h3><?php echo $h->lang("admin_theme_blocked_new"); ?></h3>
	<table>
		<tr class='table_headers'>
			<td><select name='blocked_type'>
				<option value='ip'><?php echo $h->lang("admin_theme_blocked_ip"); ?></option>
				<option value='url'><?php echo $h->lang("admin_theme_blocked_url"); ?></option>
				<option value='email'><?php echo $h->lang("admin_theme_blocked_email"); ?></option>
				<option value='user'><?php echo $h->lang("admin_theme_blocked_username"); ?></option>
			</select></td>
			<td><input type='text' size=30 name='value' value='' /></td>
			<td><input class='submit' type='submit' value='<?php echo $h->lang('admin_theme_blocked_submit_add'); ?>' /></td>
		</tr>
	</table>
	<input type='hidden' name='type' value='new' />
	<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

<table><tr><td>

<form name='blocked_list_search_form' action='<?php echo SITEURL; ?>admin_index.php?page=blocked' method='post'>
	<h3><?php echo $h->lang("admin_theme_blocked_search"); ?></h3>
	<table>
		<tr class='table_headers'>
			<td><input type='text' size=30 name='search_value' value='' /></td>
			<td><input class='submit' type='submit' value='<?php echo $h->lang('admin_theme_blocked_submit_search'); ?>' /></td>
		</tr>
	</table>
	<input type='hidden' name='type' value='search' />
	<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

</td><td>

<form name='blocked_list_filter_form' action='<?php echo SITEURL; ?>admin_index.php?page=blocked' method='post'>
	<h3><?php echo $h->lang("admin_theme_blocked_filter"); ?></h3>
	<table>
		<tr class='table_headers'>
			<td><select name='blocked_type'>
				<option value='all'><?php echo $h->lang("admin_theme_blocked_all"); ?></option>
				<option value='ip'><?php echo $h->lang("admin_theme_blocked_ip"); ?></option>
				<option value='url'><?php echo $h->lang("admin_theme_blocked_url"); ?></option>
				<option value='email'><?php echo $h->lang("admin_theme_blocked_email"); ?></option>
				<option value='user'><?php echo $h->lang("admin_theme_blocked_username"); ?></option>
			</select></td>
			<td><input class='submit' type='submit' value='<?php echo $h->lang('admin_theme_blocked_submit_filter'); ?>' /></td>
		</tr>
	</table>
	<input type='hidden' name='type' value='filter' />
	<input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

</tr></table>

<div id="table_list">
	<table class="table table-bordered">
            <tr class='table_headers info'>
		<td><?php echo $h->lang("admin_theme_blocked_type"); ?></td>
		<td><?php echo $h->lang("admin_theme_blocked_value"); ?></td>
		<td><?php echo $h->lang("admin_theme_blocked_edit"); ?></td>
		<td><?php echo $h->lang("admin_theme_blocked_remove"); ?></td>
	</tr>
		<?php if (isset($blocked_items)) { echo $blocked_items; } ?>
	</table>
</div>

<?php 
	if (isset($pagedResults)) {
		echo $h->pageBar($pagedResults);
	}
?>
