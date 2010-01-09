<?php
/**
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_delete_confirm.php
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

?>
 
    <h2><?php echo $h->lang["cat_man_delete"]; ?></h2>

    <table class="cat_man_table">
    <tr><td class="cat_man_body">
        <form name='category_manager_delete_confirm_form' action='<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=delete_confirm' method='post'>
        <?php echo $h->lang["cat_man_delete_following"]; ?><br /><br />
        <?php
        $counter = 0; 
        foreach ($h->vars['delete_list'] as $del) {
            echo "<input type='hidden' name='delete_list[" . $counter . "]' value='" . $del['del_id'] . "'>";
            echo $del['del_name'] . " <span style='font-size: 0.8em; color: #888;'>(" . $del['del_id'] . ")</span><br />";
            $counter++;
        }
        ?>
        <br />
        <h3><?php echo $h->lang["cat_man_delete_are_you_sure"]; ?></h3>
        <div style="float: right;">
            <input style='padding: 2px;' type='submit' name='delete_confirm_no' value='<?php echo $h->lang["cat_man_delete_no_cancel"]; ?>'>&nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='delete_confirm_yes' value='<?php echo $h->lang["cat_man_delete_yes_delete"]; ?>'>
        </div>
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        </form>
    </td>
    </tr></table>
