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

// fixes for undefined index errors:
if (!isset($h->vars['com_man_rows'])) { $h->vars['com_man_rows'] = ''; }
if (!isset($h->vars['com_man_navi'])) { $h->vars['com_man_navi'] = ''; }
?>

<!-- TITLE FOR ADMIN NEWS -->
<h2><?php echo $h->lang["com_man"]; ?></h2>

<?php echo $h->lang["com_man_desc"]; ?>

<?php echo " [<a href='" . BASEURL . "admin_index.php?comment_status_filter=pending&plugin=comment_manager&page=plugin_settings&type=filter'>" . $h->lang["com_man_num_pending"] . $h->vars['num_pending'] . "</a>]"; ?>

<?php echo $h->showMessage(); ?>

<table><tr><td>

<form name='com_man_search_form' action='<?php echo BASEURL; ?>admin_index.php' method='get'>
    <h3><?php echo $h->lang["com_man_search"]; ?></h3>
    <table>
        <tr class='table_headers'>
            <td><input type='text' size=30 name='search_value' value='<?php echo $h->vars['search_term']; ?>' /></td>
            <td><input class='submit' type='submit' value='<?php echo $h->lang['com_man_search_button']; ?>' /></td>
        </tr>
    </table>
    <input type='hidden' name='plugin' value='comment_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='type' value='search' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

</td><td>

<form name='com_man_filter_form' action='<?php echo BASEURL; ?>admin_index.php?plugin=comment_manager' method='get'>
    <h3><?php echo $h->lang["com_man_filter"]; ?></h3>
    <table>
        <tr class='table_headers'>
            <td><select name='comment_status_filter'>
                <option style='font-weight: bold;' value='<?php echo $h->vars['comment_status_filter']; ?>'><?php echo ucfirst($h->vars['comment_status_filter']); ?></option>
                <option value='' disabled>-----</option>
                <option value='all'><?php echo $h->lang['com_man_filter_all']; ?></option>
                <option value='approved'><?php echo $h->lang['com_man_filter_approved']; ?></option>
                <option value='pending'><?php echo $h->lang['com_man_filter_pending']; ?></option>
                <option value='buried'><?php echo $h->lang['com_man_filter_buried']; ?></option>
                <option value='' disabled>-----</option>
                <option value='newest'><?php echo $h->lang['com_man_filter_newest']; ?></option>
                <option value='oldest'><?php echo $h->lang['com_man_filter_oldest']; ?></option>
            </select></td>
            <td><input class='submit' type='submit' value='<?php echo $h->lang['com_man_filter_button']; ?>' /></td>
        </tr>
    </table>
    <input type='hidden' name='plugin' value='comment_manager' />
    <input type='hidden' name='page' value='plugin_settings' />
    <input type='hidden' name='type' value='filter' />
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
</form>

</tr></table>

<div id="table_list">
    <table>
    <tr class='table_headers'>
        <td><?php echo $h->lang["com_man_id"]; ?></td>
        <td><?php echo $h->lang["com_man_status"]; ?></td>
        <td><?php echo $h->lang["com_man_date"]; ?></td>
        <td><?php echo $h->lang["com_man_author"]; ?></td>
        <td><?php echo $h->lang["com_man_post"]; ?></td>
        <td><?php echo $h->lang["com_man_approve"]; ?></td>
        <?php if ($h->currentUser->getPermission('can_delete_comments') == 'yes') { ?>
            <td><?php echo $h->lang["com_man_delete"]; ?></td>
        <?php } ?>
    </tr>
            <?php echo $h->vars['com_man_rows']; ?>
    </table>
</div>

<div id="com_man_mass_edit">
    <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=approve_all">
        <?php echo $h->lang["com_man_approve_all"]; ?>
    </a>
    <?php if ($h->currentUser->getPermission('can_delete_comments') == 'yes') { ?>
    &nbsp; | &nbsp;
    <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=delete_all_pending">
        <span class="bold_red"><?php echo $h->lang["com_man_delete_all_pending"]; ?></span>
    </a>
    &nbsp; | &nbsp;
    <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=comment_manager&amp;action=delete_all_buried">
        <span class="bold_red"><?php echo $h->lang["com_man_delete_all_buried"]; ?></span>
    </a>
    <?php } ?>
    <br />
    <small><?php echo $h->lang["com_man_all_note"]; ?></small>
</div>

<div class='clear'></div>

<?php echo $h->vars['com_man_navi']; // pagination ?>
