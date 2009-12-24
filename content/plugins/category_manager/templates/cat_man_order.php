<?php
/**
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_order.php
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
 
    <h2><?php echo $h->lang["cat_man_order"]; ?></h2>

    <table class="cat_man_table">
    <tr><td class="cat_man_body">
        <?php echo $h->lang["cat_man_order_instruct"]; ?><br />

        <h3>1. <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order_alpha"><?php echo $h->lang["cat_man_order_alpha"]; ?></a></h3>
        <?php echo $h->lang["cat_man_order_alpha_desc"]; ?> <br />
        <h3>2. <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order_length"><?php echo $h->lang["cat_man_order_length"]; ?></a></h3>
        <?php echo $h->lang["cat_man_order_length_desc"]; ?>  <br />
        <h3>3. <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order_id"><?php echo $h->lang["cat_man_order_id"]; ?></a></h3>
        <?php echo $h->lang["cat_man_order_id_desc"]; ?> <br />
        <h3>4. <a href="<?php echo BASEURL; ?>admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order_posts"><?php echo $h->lang["cat_man_order_posts"]; ?></a></h3>
        <?php echo $h->lang["cat_man_order_posts_desc"]; ?>

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
