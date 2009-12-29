<?php
/**
 * Plugin name: Category Manager
 * Template name: plugins/category_manager/cat_man_menu.php
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
 
$home_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=category_manager";
$order_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=order";
$add_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=add";
$edit_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=edit";
$edit_meta_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=edit_meta";
$move_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=move";
$delete_link = BASEURL . "admin_index.php?page=plugin_settings&amp;plugin=category_manager&amp;action=delete";

?>
 
<div class='cat_man_menu'>
    &raquo; <a href = '<?php echo $home_link; ?>'><?php echo $h->lang["cat_man_menu_home"]; ?></a><br />
    &raquo; <a href = '<?php echo $order_link; ?>'><?php echo $h->lang["cat_man_menu_order"]; ?></a><br />
    &raquo; <a href = '<?php echo $add_link; ?>'><?php echo $h->lang["cat_man_menu_add"]; ?></a><br />
    &raquo; <a href = '<?php echo $edit_link; ?>'><?php echo $h->lang["cat_man_menu_edit"]; ?></a><br />
    &raquo; <a href = '<?php echo $edit_meta_link; ?>'><?php echo $h->lang["cat_man_menu_edit_meta"]; ?></a><br />
    &raquo; <a href = '<?php echo $move_link; ?>'><?php echo $h->lang["cat_man_menu_move"]; ?></a><br />
    &raquo; <a href = '<?php echo $delete_link; ?>'><?php echo $h->lang["cat_man_menu_delete"]; ?></a><br />
</div>
    
