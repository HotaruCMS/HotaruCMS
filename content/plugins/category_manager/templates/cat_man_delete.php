<?php
/**
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_delete.php
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
        <?php echo $h->lang["cat_man_delete_instruct"]; ?>
        <h3><i><?php echo $h->lang["cat_man_delete_notes"]; ?></i></h3>
        1. <?php echo $h->lang["cat_man_delete_note1"]; ?> <br />
        2. <?php echo $h->lang["cat_man_delete_note2"]; ?><br /><br />
    
        <form name='category_manager_delete_form' action='<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=delete_save' method='post'>
        <?php 
        foreach ($h->vars['the_cats'] as $cat) {
            if ($cat['category_safe_name'] != "all") {
            
                if ($cat['category_empty']) {    // safe to delete
                    echo "<input type='checkbox' name='delete_cats[" . $cat['category_id'] . "]' value='" . $cat['category_id']. "'>&nbsp;&nbsp;";
                } else {
                    echo "<input type='checkbox' name='delete_cats[" . $cat['category_id'] . "]' value='" . $cat['category_id']. "' DISABLED>&nbsp;&nbsp;";
                }
                
                if ($cat['category_parent'] > 1) {
                    for($i=1; $i<$cat['category_level']; $i++) {
                        echo "--- ";
                    }
                }
                
                if ($cat['category_empty']) {
                    echo $cat['category_name'] . " <span style='font-size: 0.8em; color: #888;'>(" . $cat['category_id'] . ")</span><br />";
                } else {
                    echo "<span style='color: #888;'>" . $cat['category_name'] . "</span> <span style='font-size: 0.8em; color: #888;'>(" . $cat['category_id'] . ")</span><br />";
                }
            }
        }
        ?>
        <br />
        <div style="text-align: center;">
            <input type='submit' name='cancel_all' value='<?php echo $h->lang["cat_man_cancel"]; ?>'>&nbsp;&nbsp;<input type='submit' name='delete' value='<?php echo $h->lang["cat_man_delete_selected"]; ?>'>
        </div>
        <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
        </form>
    </td>
    
    <td class="cat_man_menu_holder">
        <div>
            <h3><?php echo $h->lang["cat_man_menu_title"]; ?></h3>
            <?php $h->displayTemplate('cat_man_menu', 'category_manager'); ?>
            
            <h3><?php echo $h->lang["cat_man_category_tree"]; ?></h3>
            <?php
                $tree = new CategoryManagerSettings($h); 
                $tree->tree($h, $h->vars['the_cats']);
            ?>

        </div>
    </td>
    </tr></table>
