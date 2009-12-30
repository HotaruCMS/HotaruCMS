<?php
/**
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_add.php
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
 
    <h2><?php echo $h->lang["cat_man_add"]; ?></h2>

    <table class="cat_man_table">
    <tr><td class="cat_man_body">
    <h3>1. <?php echo $h->lang["cat_man_add_main"]; ?></h3>
    <form name='category_manager_add_parent_form' action='<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=add_save' method='post'>
    <?php echo $h->lang["cat_man_add_top_level"]; ?> <input name="new_category" type="text" value=""> &nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='save_new_category1' value='<?php echo $h->lang["cat_man_save"]; ?>'>
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </form>

    <h3>2. <?php echo $h->lang["cat_man_add_child_to_main"]; ?></h3>
    <form name='category_manager_add_child_form' action='<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=add_save' method='post'>
    <?php echo $h->lang["cat_man_add_add_to"]; ?>
        <select name="parent">
        <?php
        foreach ($h->vars['the_cats'] as $cat) {
            if ($cat['category_safe_name'] != "all") {
                if ($cat['category_parent'] == 1) {
                      echo "<option value='" . $cat['category_id'] . "'>" . $cat['category_name'] . "</option>";
                }
            }
          }
          ?>
          </select>
    <?php echo $h->lang["cat_man_add_name_it"]; ?> <input name="new_category" type="text" value=""> &nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='save_new_category2' value='<?php echo $h->lang["cat_man_save"]; ?>'>
    <input type='hidden' name='csrf' value='<?php echo $h->csrfToken; ?>' />
    </form>

    <h3>3. <?php echo $h->lang["cat_man_add_child_to_child"]; ?></h3>
    <form name='category_manager_add_child2_form' action='<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=add_save' method='post'>
    <?php echo $h->lang["cat_man_add_add_to"]; ?> 
        <select name="parent">
        <?php
        foreach ($h->vars['the_cats'] as $cat) {
            if ($cat['category_safe_name'] != "all") {
                if ($cat['category_parent']  > 1) {
                      echo "<option value='" . $cat['category_id'] . "'>" . $cat['category_name'] . "</option>";
                }    
            }
          }
          ?>
          </select>
    <?php echo $h->lang["cat_man_add_name_it"]; ?> <input name="new_category" type="text" value=""> &nbsp;&nbsp;<input style='padding: 2px;' type='submit' name='save_new_category3' value='<?php echo $h->lang["cat_man_save"]; ?>'>
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
