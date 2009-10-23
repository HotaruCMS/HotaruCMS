<?php
/**
 * Plugin name: Comment Manager
 * Template name: plugins/comment_manager/com_man_main.php
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

//$bl_array = $hotaru->buildBlockedList();
//extract($bl_array); // extracts $output and $pagedResults;
?>

<!-- TITLE FOR ADMIN NEWS -->
<h2><?php echo $hotaru->lang["com_man"]; ?></h2>

<?php echo $hotaru->lang["com_man_desc"]; ?>

<?php echo $hotaru->showMessage(); ?>

<table><tr><td>

<form name='com_man_search_form' action='<?php echo BASEURL; ?>admin_index.php' method='get'>
    <h3><?php echo $hotaru->lang["com_man_search"]; ?></h3>
    <table>
        <tr class='table_headers'>
            <td><input type='text' size=30 name='search_value' value='<?php echo $hotaru->vars['search_term']; ?>' /></td>
            <td><input class='submit' type='submit' value='<?php echo $hotaru->lang['com_man_search_button']; ?>' /></td>
        </tr>
    </table>
    <input type='hidden' name='plugin' value='comment_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='type' value='search' />
</form>

</td><td>

<form name='com_man_filter_form' action='<?php echo BASEURL; ?>admin_index.php?plugin=comment_manager' method='get'>
    <h3><?php echo $hotaru->lang["com_man_filter"]; ?></h3>
    <table>
        <tr class='table_headers'>
            <td><select name='comment_status_filter'>
                <option style='font-weight: bold;' value='<?php echo $hotaru->vars['comment_status_filter']; ?>'><?php echo ucfirst($hotaru->vars['comment_status_filter']); ?></option>
                <option value='' disabled>-----</option>
                <option value='pending'><?php echo $hotaru->lang['com_man_filter_pending']; ?></option>
                <option value='approved'><?php echo $hotaru->lang['com_man_filter_approved']; ?></option>
                <option value='all'><?php echo $hotaru->lang['com_man_filter_all']; ?></option>
                <option value='' disabled>-----</option>
                <option value='newest'><?php echo $hotaru->lang['com_man_filter_newest']; ?></option>
                <option value='oldest'><?php echo $hotaru->lang['com_man_filter_oldest']; ?></option>
            </select></td>
            <td><input class='submit' type='submit' value='<?php echo $hotaru->lang['com_man_filter_button']; ?>' /></td>
        </tr>
    </table>
    <input type='hidden' name='plugin' value='comment_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='type' value='filter' />
</form>

</tr></table>

<div id="table_list">
    <table>
    <tr class='table_headers'>
        <td><?php echo $hotaru->lang["com_man_id"]; ?></td>
        <td><?php echo $hotaru->lang["com_man_status"]; ?></td>
        <td><?php echo $hotaru->lang["com_man_date"]; ?></td>
        <td><?php echo $hotaru->lang["com_man_author"]; ?></td>
        <td><?php echo $hotaru->lang["com_man_post"]; ?></td>
        <td><?php echo $hotaru->lang["com_man_approve"]; ?></td>
        <td><?php echo $hotaru->lang["com_man_delete"]; ?></td>
    </tr>
            <?php echo $hotaru->vars['com_man_rows']; ?>
    </table>
</div>

<div id="com_man_mass_edit">
    <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=approve_all">
        <?php echo $hotaru->lang["com_man_approve_all"]; ?>
    </a>
    &nbsp; | &nbsp;
    <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=delete_all">
        <span class="bold_red"><?php echo $hotaru->lang["com_man_delete_all"]; ?></span>
    </a>
    <br />
    <small><?php echo $hotaru->lang["com_man_all_note"]; ?></small>
</div>

<div class='clear'></div>

<?php echo $hotaru->vars['com_man_navi']; // pagination ?>