<?php 
/**
 * Theme name: admin_default
 * Template name: blocked.php
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

global $hotaru, $lang, $admin; // don't remove
?>

<p class="breadcrumbs">
    <a href="<?php echo BASEURL; ?>"><?php echo SITE_NAME; ?></a> 
    &raquo; <a href="<?php echo url(array(), 'admin'); ?>"><?php echo $lang["admin_theme_main_admin_cp"]; ?></a> 
    &raquo; <?php echo $lang["admin_theme_blocked_list"]; ?>
</p>

<!-- TITLE FOR ADMIN NEWS -->
<h2><?php echo $lang["admin_theme_blocked_list"]; ?></h2>

<?php 
    $blocked_items = $admin->blockedList();
    echo $hotaru->showMessage();
?>

<form name='blocked_list_new_form' action='<?php echo BASEURL; ?>admin_index.php' method='post'>
    <h3><?php echo $lang["admin_theme_blocked_new"]; ?></h3>
    <table class='blocked_list'>
        <tr>
            <td><select name='blocked_type'>
                <option value='ip'><?php echo $lang["admin_theme_blocked_ip"]; ?></option>
                <option value='url'><?php echo $lang["admin_theme_blocked_url"]; ?></option>
                <option value='email'><?php echo $lang["admin_theme_blocked_email"]; ?></option>
            </select></td>
            <td><input type='text' size=30 name='value' value='' /></td>
            <td><input class='submit' type='submit' value='<?php echo $lang['admin_theme_blocked_submit']; ?>' /></td>
        </tr>
    </table>
    <input type='hidden' name='page' value='blocked_list' />
    <input type='hidden' name='type' value='new' />
</form>

<table>
<tr>
    <td><?php echo $lang["admin_theme_blocked_type"]; ?></td>
    <td><?php echo $lang["admin_theme_blocked_value"]; ?></td>
    <td><?php echo $lang["admin_theme_blocked_edit"]; ?></td>
    <td><?php echo $lang["admin_theme_blocked_remove"]; ?></td>
</tr>
        <?php echo $blocked_items; ?>
</table>