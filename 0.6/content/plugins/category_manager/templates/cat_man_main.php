<?php
/**
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_main.php
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
 
 global $hotaru, $lang, $the_cats;
 ?>
 
    <h2><?php echo $lang["cat_man_home"]; ?></h2>

    <table class="cat_man_table">
    <tr><td class="cat_man_body">
        <?php echo $lang["cat_man_home_intro1"]; ?><br /><br />
        <?php echo $lang["cat_man_home_intro2"]; ?><br />

        <h3>1. <a href="<?php echo BASEURL; ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order"><?php echo $lang["cat_man_home_order_categories"]; ?></a></h3>
        <?php echo $lang["cat_man_home_order_categories_desc"]; ?> <br />
        <h3>2. <a href="<?php echo BASEURL; ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=add"><?php echo $lang["cat_man_home_add_categories"]; ?></a></h3>
        <?php echo $lang["cat_man_home_add_categories_desc"]; ?> <br />
        <h3>3. <a href="<?php echo BASEURL; ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=edit"><?php echo $lang["cat_man_home_edit_categories"]; ?></a></h3>
        <?php echo $lang["cat_man_home_edit_categories_desc"]; ?> <br />
        <h3>4. <a href="<?php echo BASEURL; ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=edit_meta"><?php echo $lang["cat_man_home_edit_categories_meta"]; ?></a></h3>
        <?php echo $lang["cat_man_home_edit_categories_meta_desc"]; ?><br />
        <h3>5. <a href="<?php echo BASEURL; ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=move"><?php echo $lang["cat_man_home_move_categories"]; ?></a></h3>
        <?php echo $lang["cat_man_home_move_categories_desc"]; ?> <br />
        <h3>6. <a href="<?php echo BASEURL; ?>admin/admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=delete"><?php echo $lang["cat_man_home_delete_categories"]; ?></a></h3>
        <?php echo $lang["cat_man_home_delete_categories_desc"]; ?>
    </td>
    
    <td class="cat_man_menu_holder">
        <div>
            <h3><?php echo $lang["cat_man_menu_title"]; ?></h3>
            <?php $hotaru->displayTemplate('cat_man_menu', 'category_manager'); ?>
            
            <h3><?php echo $lang["cat_man_category_tree"]; ?></h3>
            <?php cat_man_tree($the_cats); ?>

        </div>
    </td>
    </tr></table>
