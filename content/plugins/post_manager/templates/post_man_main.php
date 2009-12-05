<?php
/**
 * Plugin name: Post Manager
 * Template name: plugins/post_manager/post_man_main.php
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

// fixes for undefined index errors:
if (!isset($hotaru->vars['post_man_rows'])) { $hotaru->vars['post_man_rows'] = ''; }
if (!isset($hotaru->vars['post_man_navi'])) { $hotaru->vars['post_man_navi'] = ''; }
?>

<!-- TITLE FOR ADMIN NEWS -->
<h2><?php echo $hotaru->lang["post_man"]; ?></h2>

<?php echo $hotaru->lang["post_man_desc"]; ?>

<?php echo " [<a href='" . BASEURL . "admin_index.php?post_status_filter=pending&plugin=post_manager&page=plugin_settings&type=filter'>" . $hotaru->lang["post_man_num_pending"] . $hotaru->vars['num_pending'] . "</a>]"; ?>

<?php echo $hotaru->showMessage(); ?>

<table><tr><td>

<form name='post_man_search_form' action='<?php echo BASEURL; ?>admin_index.php' method='get'>
    <h3><?php echo $hotaru->lang["post_man_search"]; ?></h3>
    <table>
        <tr class='table_headers'>
            <td><input type='text' size=30 name='search_value' value='<?php echo $hotaru->vars['search_term']; ?>' /></td>
            <td><input class='submit' type='submit' value='<?php echo $hotaru->lang['post_man_search_button']; ?>' /></td>
        </tr>
    </table>
    <input type='hidden' name='plugin' value='post_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='type' value='search' />
</form>

</td><td>

<form name='post_man_filter_form' action='<?php echo BASEURL; ?>admin_index.php?plugin=post_manager' method='get'>
    <h3><?php echo $hotaru->lang["post_man_filter"]; ?></h3>
    <table>
        <tr class='table_headers'>
            <td><select name='post_status_filter'>
                <option style='font-weight: bold;' value='<?php echo $hotaru->vars['post_status_filter']; ?>'><?php echo ucfirst($hotaru->vars['post_status_filter']); ?></option>
                <option value='' disabled>-----</option>
                <option value='all'><?php echo $hotaru->lang['post_man_filter_all']; ?></option>
                <option value='not_buried'><?php echo $hotaru->lang['post_man_filter_not_buried']; ?></option>
                <option value='' disabled>-----</option>
                <option value='newest'><?php echo $hotaru->lang['post_man_filter_newest']; ?></option>
                <option value='oldest'><?php echo $hotaru->lang['post_man_filter_oldest']; ?></option>
                <option value='' disabled>-----</option>
                <?php 
                if ($hotaru->vars['statuses']) {
                    foreach ($hotaru->vars['statuses'] as $status) {
                        if ($status != 'unsaved') { 
                            echo "<option value=" . $status . ">" . ucfirst($status) . "</option>\n";
                        }
                    }
                }
                ?>
            </select></td>
            <td><input class='submit' type='submit' value='<?php echo $hotaru->lang['post_man_filter_button']; ?>' /></td>
        </tr>
    </table>
    <input type='hidden' name='plugin' value='post_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='type' value='filter' />
</form>

</tr></table>

<form name='post_man_checkbox_form' style='margin: 0px; padding: 0px;' action='<?php echo BASEURL; ?>admin_index.php?plugin=post_manager' method='get'>
    
<div id="table_list">
    <table>
    <tr class='table_headers'>
        <td><?php echo $hotaru->lang["post_man_id"]; ?></td>
        <td><?php echo $hotaru->lang["post_man_status"]; ?></td>
        <td><?php echo $hotaru->lang["post_man_date"]; ?></td>
        <td><?php echo $hotaru->lang["post_man_title"]; ?></td>
        <td><?php echo $hotaru->lang["post_man_edit"]; ?></td>
        <td><?php echo $hotaru->lang["post_man_check"]; ?></td>
    </tr>
            <?php echo $hotaru->vars['post_man_rows']; ?>
    </table>
</div>

<div class='post_man_submit_button'>
        <table>
            <tr class='table_headers'>
                <td><select name='checkbox_action'>
                    <option value='new_selected'><?php echo $hotaru->lang["post_man_set_new"]; ?></option>
                    <option value='top_selected'><?php echo $hotaru->lang["post_man_set_top"]; ?></option>
                    <option value='pending_selected'><?php echo $hotaru->lang["post_man_set_pending"]; ?></option>
                    <option value='bury_selected'><?php echo $hotaru->lang["post_man_set_buried"]; ?></option>
                    <option value='' disabled>-----</option>
                    <option style='color: red; font-weight: bold;' value='delete_selected'><?php echo $hotaru->lang["post_man_set_delete"]; ?></option>
                    </select>
                </td>
                <td><input class='submit' type='submit' value='<?php echo $hotaru->lang['post_man_checkbox_action']; ?>' /></td>
            </tr>
        </table>
        <input type='hidden' name='plugin' value='post_manager' />
        <input type='hidden' name='page' value='plugin_settings' />
        <input type='hidden' name='type' value='checkboxes' />
    </form>
</div>

<div class='clear'></div>

<?php echo $hotaru->vars['post_man_navi']; // pagination ?>